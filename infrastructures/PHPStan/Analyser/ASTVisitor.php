<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\PHPStan\Analyser;

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Node\AnonymousClassNode;
use PHPStan\Parser\Parser;
use PHPStan\Parser\ParserErrorsException;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use ReflectionClass;
use ReflectionException;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;

use function array_flip;
use function array_keys;
use function array_map;
use function get_parent_class;
use function strtolower;

/**
 * AST Visitor tp alter the AST returned by PhpParser to remove all method in state class and migrate theirs
 * statements into related proxies classes, to avoid false positive with PHPStan about deadcode or "non existent method"
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class ASTVisitor extends NodeVisitorAbstract
{
    /**
     * @var array<string, array<int, ClassMethod>>
     */
    private array $statesStmts = [];

    /**
     * @var array<string, true>
     */
    private array $classesUpdated = [];

    /**
     * @var callable(string): (\ReflectionClass<object>|ClassReflection)
     */
    private mixed $reflectionClassFactory;

    /**
     * @param ReflectionProvider|callable(string): (\ReflectionClass<object>|ClassReflection) $reflectionProvider
     */
    public function __construct(
        ReflectionProvider|callable $reflectionProvider,
        private readonly Parser $parser,
    ) {
        if ($reflectionProvider instanceof ReflectionProvider) {
            //@codeCoverageIgnoreStart
            $this->reflectionClassFactory = $reflectionProvider->getClass(...);
            //@codeCoverageIgnoreEnd
        } else {
            $this->reflectionClassFactory = $reflectionProvider;
        }
    }

    /**
     * @return array<class-string>
     * @throws ReflectionException
     */
    private function listStatesFromProxyClass(string $proxyClass): array
    {
        if (
            empty($proxyClass)
            || !class_exists($proxyClass)
            || !is_subclass_of($proxyClass, ProxyInterface::class)
        ) {
            return [];
        }

        $classes = [];

        if (!empty($parent = get_parent_class($proxyClass))) {
            $classes = $this->listStatesFromProxyClass($parent);
        }

        return $classes + $this->extractStatesClassesDeclaration($proxyClass);
    }

    /**
     * @param class-string $className
     * @return array<class-string>
     * @throws ReflectionException
     */
    private function extractStatesClassesDeclaration(string $className): array
    {
        $nativeReflection = new ReflectionClass($className);

        if (!$nativeReflection->hasMethod('statesListDeclaration')) {
            return [];
        }

        $listDeclarationReflection = $nativeReflection->getMethod('statesListDeclaration');

        /** @var array<class-string, int> $classesList */
        $classesList = $listDeclarationReflection->getClosure()();

        return array_flip($classesList);
    }

    /**
     * @param class-string $className
     * @return array<int, ClassMethod>
     * @throws ParserErrorsException
     */
    private function getStateStmts(string $className, Class_ $parent): array
    {
        if (!isset($this->statesStmts[$className])) {
            $reflection = ($this->reflectionClassFactory)($className);
            $fileName = $reflection->getFileName();

            if (empty($fileName)) {
                return [];
            }

            $this->parser->parseFile($fileName);
        }

        $this->statesStmts[$className] ??= [];
        foreach ($this->statesStmts[$className] as $stmt) {
            $stmt->setAttribute('parent', $parent);
        }

        return $this->statesStmts[$className];
    }

    /**
     * @param Stmt[] $proxyStmts
     * @param ClassMethod[][] $statesStmts
     * @return Stmt[]
     */
    private function mergeStmts(array $proxyStmts, array $statesStmts): array
    {
        /** @var array<string, int> $currentMethodsList */
        $currentMethodsList = [];

        foreach ($statesStmts as $stateStmts) {
            foreach ($stateStmts as $stmt) {
                $lowerName = $stmt->name->toLowerString();

                if (isset($currentMethodsList[$lowerName])) {
                    //The method is renamed and virtualy set a public to avoid false positive about duplicated code.
                    $stmt->name = new Identifier(((string) $stmt->name) . $currentMethodsList[$lowerName]);
                    $stmt->flags &= Modifiers::PUBLIC & ~Modifiers::PROTECTED & ~Modifiers::PRIVATE;
                    ++$currentMethodsList[$lowerName];
                } else {
                    $currentMethodsList[$lowerName] = 1;
                }

                $proxyStmts[] = $stmt;
            }
        }

        return $proxyStmts;
    }

    /**
     * @throws ParserErrorsException
     * @throws ReflectionException
     */
    public function leaveNode(Node $node): ?Node
    {
        if (
            !$node instanceof Class_
            || $node instanceof AnonymousClassNode
            || empty($node->implements)
        ) {
            return $node;
        }

        foreach ($node->implements as $className) {
            $interfaceName = $className->toString();

            $className = (string) $node->namespacedName;
            if (
                !isset($this->classesUpdated[$className])
                && (
                    ProxyInterface::class === $interfaceName
                    || is_subclass_of($interfaceName, ProxyInterface::class)
                )
            ) {
                $this->classesUpdated[$className] = true;
                $classes = array_keys($this->listStatesFromProxyClass($className));
                $node->stmts = $this->mergeStmts(
                    $node->stmts,
                    array_map(
                        /**
                         * @throws ParserErrorsException
                         */
                        fn ($class): array => $this->getStateStmts((string) $class, $node),
                        $classes,
                    )
                );
            }

            if (
                StateInterface::class === $interfaceName
                && !isset($this->statesStmts[$className])
            ) {
                $stmtsToKeep = [];
                foreach ($node->stmts as $stmt) {
                    if (!$stmt instanceof ClassMethod) {
                        $stmtsToKeep[] = $stmt;
                        continue;
                    }

                    if (
                        isset($stmt->stmts[0])
                        && (
                            (
                                $stmt->returnType instanceof Identifier
                                && strtolower($stmt->returnType->name) === 'callable'
                            )
                            || (
                                $stmt->returnType instanceof Node\Name\FullyQualified
                                && 'closure' === strtolower((string) $stmt->returnType)
                            )
                        )
                    ) {
                        /** @var Stmt\Return_ $returnStmt */
                        $returnStmt = $stmt->stmts[0];
                        /** @var Node\Expr\Closure $closureStmt */
                        $closureStmt =  $returnStmt->expr;
                        $stmt->params = $closureStmt->params;
                        $stmt->returnType = $closureStmt->returnType;
                        $stmt->attrGroups = $closureStmt->attrGroups;
                        $stmt->stmts = $closureStmt->stmts;
                    }

                    $this->statesStmts[$className][] = $stmt;
                }

                $node->stmts = $stmtsToKeep;
            }
        }

        return $node;
    }
}
