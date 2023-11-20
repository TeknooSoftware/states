<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\States\PHPStan\Analyser;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Parser\Parser;
use PHPStan\Parser\ParserErrorsException;
use PHPStan\Reflection\ReflectionProvider;
use ReflectionException;
use Teknoo\East\Paas\Compilation\Conductor;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;

use function array_flip;
use function array_keys;
use function array_map;
use function get_parent_class;
use function is_array;
use function array_merge;

/**
 * AST Visitor tp alter the AST returned by PhpParser to remove all method in state class and migrate theirs
 * statements into related proxies classes, to avoid false positive with PHPStan about deadcode or "non existent method"
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class ASTVisitor extends NodeVisitorAbstract
{
    /**
     * @var array<string, array<int, ClassMethod>>
     */
    private array $statesStmts = [];

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
        private readonly Parser $parser,
    ) {
    }

    /**
     * @return array<class-string>
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
     * @return array<class-string>
     * @throws ReflectionException
     */
    private function extractStatesClassesDeclaration(string $className): array
    {
        $nativeReflection = $this->reflectionProvider
            ->getClass($className)
            ->getNativeReflection();

        if (!$nativeReflection->hasMethod('statesListDeclaration')) {
            return [];
        }

        $listDeclarationReflection = $nativeReflection
            ->getMethod('statesListDeclaration');

        $listClosure = $listDeclarationReflection->getClosure(null);

        return array_flip(($listClosure ?? static fn (): array => [])());
    }

    /**
     * @param array<int, Stmt> $stmts
     * @return array<int, ClassMethod>|null
     */
    private static function recursiveExtraction(array $stmts, Class_ $parent, callable $recursiveExtraction): ?array
    {
        foreach ($stmts as $node) {
            if (
                !$node instanceof Class_
                && !empty($node->stmts)
                && is_array($node->stmts)
            ) {
                $return = $recursiveExtraction($node->stmts, $parent, $recursiveExtraction);

                if (null !== $return) {
                    return $return;
                }
            } elseif ($node instanceof Class_) {
                $stmts = [];

                foreach ($node->stmts as $stmt) {
                    if (!$stmt instanceof ClassMethod) {
                        continue;
                    }

                    $stmt->setAttribute('parent', $parent);

                    $stmts[] = $stmt;
                }

                return $stmts;
            }
        }

        return null;
    }

    /**
     * @param class-string $className
     * @return array<int, ClassMethod>
     * @throws ParserErrorsException
     */
    private function getStateStmts(string $className, Class_ $parent): array
    {
        if (isset($this->statesStmts[$className])) {
            return $this->statesStmts[$className];
        }

        $reflection = $this->reflectionProvider->getClass($className);
        $fileName = $reflection->getFileName();

        if (empty($fileName)) {
            return [];
        }

        $node = $this->parser->parseFile($fileName);
        $recursiveExtraction = self::recursiveExtraction(...);

        return $this->statesStmts[$className] = self::recursiveExtraction($node, $parent, $recursiveExtraction) ?? [];
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
                    $stmt->flags &= Class_::MODIFIER_PUBLIC & ~Class_::MODIFIER_PROTECTED & ~Class_::MODIFIER_PRIVATE;
                    ++$currentMethodsList[$lowerName];
                } else {
                    $currentMethodsList[$lowerName] = 1;
                }

                $proxyStmts[] = $stmt;
            }
        }

        return $proxyStmts;
    }

    public function leaveNode(Node $node): ?Node
    {
        if (
            !$node instanceof Class_
            || empty($node->implements)
        ) {
            return $node;
        }

        foreach ($node->implements as $className) {
            $interfaceName = $className->toString();

            $className = (string) $node->namespacedName;
            if (
                ProxyInterface::class === $interfaceName
                || is_subclass_of($interfaceName, ProxyInterface::class)
            ) {
                $classes = array_keys($this->listStatesFromProxyClass($className));
                $node->stmts = $this->mergeStmts(
                    $node->stmts,
                    array_map(fn ($class): array => $this->getStateStmts((string) $class, $node), $classes)
                );
            }

            if (StateInterface::class === $interfaceName) {
                $this->statesStmts[$className] = [];
                $stmtsToKeep = [];
                foreach ($node->stmts as $stmt) {
                    if (!$stmt instanceof ClassMethod) {
                        $stmtsToKeep[] = $stmt;
                        continue;
                    }

                    $this->statesStmts[$className][] = $stmt;
                }

                $node->stmts = $stmtsToKeep;
            }
        }

        return $node;
    }
}
