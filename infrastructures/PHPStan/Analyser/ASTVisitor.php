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
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Node\AnonymousClassNode;
use PHPStan\Parser\Parser;
use PHPStan\Parser\ParserErrorsException;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use ReflectionClass;
use ReflectionException;
use Teknoo\States\Attributes\StateClass;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;

use function array_flip;
use function array_keys;
use function array_map;
use function array_merge;
use function array_unique;
use function get_parent_class;
use function is_subclass_of;
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
     * @return array<class-string, int>
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
     * @return array<class-string, int>
     * @throws ReflectionException
     */
    private function extractStatesClassesDeclaration(string $className): array
    {
        $nativeReflection = new ReflectionClass($className);

        $attributeStatesList = [];
        foreach ($nativeReflection->getAttributes(StateClass::class) as $attribute) {
            /** @var \ReflectionAttribute<StateClass> $attribute */
            $attributeStatesList[] = $attribute->newInstance()->getClassNames();
        }

        $methodStatesList = [];
        if ($nativeReflection->hasMethod('statesListDeclaration')) {
            $listDeclarationReflection = $nativeReflection->getMethod('statesListDeclaration');

            /** @var array<class-string> $methodStatesList */
            $methodStatesList = $listDeclarationReflection->getClosure()();

            if ([] !== $methodStatesList && is_array($methodStatesList)) {
                trigger_error(
                    "Since teknoo/states 7.1.0, Method '{$className}::statesListDeclaration()' is deprecated, "
                    . "use instead PHP attribute #[StateClass]",
                    E_USER_DEPRECATED,
                );
            }
        }

        return array_flip(array_unique(array_merge($methodStatesList, ...$attributeStatesList)));
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

        // Return independent clones so the shared cache is never mutated by
        // mergeStmts (name/flags) nor by the parent attribute set below.
        $stmts = $this->cloneStmts($this->statesStmts[$className]);
        foreach ($stmts as $stmt) {
            $stmt->setAttribute('parent', $parent);
        }

        return $stmts;
    }

    /**
     * @param array<int, ClassMethod> $stmts
     * @return array<int, ClassMethod>
     */
    private function cloneStmts(array $stmts): array
    {
        $traverser = new NodeTraverser(new CloningVisitor());

        /** @var array<int, ClassMethod> $cloned */
        $cloned = $traverser->traverse($stmts);

        return $cloned;
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
                    //Only the visibility is replaced, others modifiers (static, final, abstract) must be kept
                    $stmt->flags = ($stmt->flags & ~Modifiers::VISIBILITY_MASK) | Modifiers::PUBLIC;
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
     * To replace, in the state's method, the builder by the closure it directly returns (closure or arrow
     * function) : arguments, return type, attributes and body of the method become theirs of the closure, to be
     * analysed as a method of the proxy. All others builders are kept unchanged.
     * A static closure is only bound to the scope of the proxy, `$this` is not available : its method is static too.
     */
    private function replaceBuilderByItsClosure(ClassMethod $stmt): void
    {
        $returnType = $stmt->returnType;
        if (
            !(
                $returnType instanceof Identifier
                && 'callable' === strtolower($returnType->name)
            )
            && !(
                $returnType instanceof Node\Name\FullyQualified
                && 'closure' === strtolower((string) $returnType)
            )
        ) {
            return;
        }

        $returnStmt = $stmt->stmts[0] ?? null;
        if (!$returnStmt instanceof Stmt\Return_) {
            return;
        }

        $closureExpr = $returnStmt->expr;
        if (
            ($closureExpr instanceof Node\Expr\Closure || $closureExpr instanceof Node\Expr\ArrowFunction)
            && true === ($closureExpr->static ?? false)
        ) {
            $stmt->flags |= Modifiers::STATIC;
        }

        if ($closureExpr instanceof Node\Expr\Closure) {
            $stmt->params = $closureExpr->params;
            $stmt->returnType = $closureExpr->returnType;
            $stmt->attrGroups = $closureExpr->attrGroups;
            $stmt->stmts = $closureExpr->stmts;

            return;
        }

        if ($closureExpr instanceof Node\Expr\ArrowFunction) {
            //An arrow function has no statements, only a returned expression
            $stmt->params = $closureExpr->params;
            $stmt->returnType = $closureExpr->returnType;
            $stmt->attrGroups = $closureExpr->attrGroups;
            $stmt->stmts = [new Stmt\Return_($closureExpr->expr, $closureExpr->getAttributes())];
        }
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

        $className = (string) $node->namespacedName;

        $isProxy = false;
        $isState = false;
        foreach ($node->implements as $implement) {
            $interfaceName = $implement->toString();

            $isProxy = $isProxy
                || ProxyInterface::class === $interfaceName
                || is_subclass_of($interfaceName, ProxyInterface::class);

            $isState = $isState || StateInterface::class === $interfaceName;
        }

        if ($isProxy) {
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

        if ($isState) {
            // The methods are ALWAYS stripped so the output node is deterministic
            // on every parse; they are collected into the cache only on the first
            // parse of this state class.
            $collect = !isset($this->statesStmts[$className]);
            $stmtsToKeep = [];
            foreach ($node->stmts as $stmt) {
                if (!$stmt instanceof ClassMethod) {
                    $stmtsToKeep[] = $stmt;
                    continue;
                }

                if (!$collect) {
                    continue;
                }

                $this->replaceBuilderByItsClosure($stmt);

                $this->statesStmts[$className][] = $stmt;
            }

            $node->stmts = $stmtsToKeep;
        }

        return $node;
    }
}
