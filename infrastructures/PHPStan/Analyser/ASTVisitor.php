<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\PHPStan\Analyser;

use PHPParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Parser\Parser;
use PHPStan\Parser\ParserErrorsException;
use PHPStan\Reflection\ReflectionProvider;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;

use function get_parent_class;
use function is_array;

/**
 * AST Visitor tp alter the AST returned by PhpParser to remove all method in state class and migrate theirs
 * statements into related proxies classes, to avoid false positive with PHPStan about deadcode or "non existent method"
 *
 * @see http://php.net/manual/en/class.arrayaccess.php
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class ASTVisitor extends NodeVisitorAbstract
{
    /**
     * @var array<string, iterable<ClassMethod>>
     */
    private array $statesStmts = [];

    public function __construct(
       private ReflectionProvider $reflectionProvider,
       private Parser $parser,
    ) {
    }

    private function listStatesFromProxyClass(string $className): array
    {
        $proxyClass = $className;
        while (
            !empty($proxyClass)
            && (
                !class_exists($proxyClass)
                || !is_subclass_of($proxyClass, ProxyInterface::class)
            )
        ) {
            $explodedClass = explode('\\', $proxyClass);
            array_pop($explodedClass);
            $proxyClass = implode('\\', $explodedClass);
        }

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

        $classes = array_unique($classes + $this->extractStatesClassesDeclaration($proxyClass));
        $classes = array_diff($classes, [$className]);

        return $classes;
    }

    /**
     * @return array<class-string>
     * @throws ReflectionException
     */
    private function extractStatesClassesDeclaration(string $className): array
    {
        $listDeclarationReflection = $this->reflectionProvider
            ->getClass($className)
            ->getNativeReflection()
            ->getMethod('statesListDeclaration');

        $listClosure = $listDeclarationReflection->getClosure(null);

        if (!is_callable($listClosure)) {
            return [];
        }

        return $listClosure();
    }

    /**
     * @param class-string $className
     * @return array<int, ClassMethod>
     * @throws ParserErrorsException
     */
    private function getStateStmts(string $className): array
    {
        if (isset($this->statesStmts[$className])) {
            return $this->statesStmts[$className];
        }

        $reflection = $this->reflectionProvider->getClass($className);
        $fileName = $reflection->getFileName();

        $node = $this->parser->parseFile($fileName);
        $recursiveExtraction = static function(array $stmts, callable $recursiveExtraction): ?array {
            foreach ($stmts as $node) {
                if (
                    !$node instanceof Class_
                    && !empty($node->stmts)
                    && is_array($node->stmts)
                ) {
                    $return = $recursiveExtraction($node->stmts, $recursiveExtraction);

                    if (null !== $return) {
                        return $return;
                    }
                } elseif($node instanceof Class_) {
                    $stmts = [];

                    foreach ($node->stmts as $stmt) {
                        if (!$stmt instanceof ClassMethod) {
                            continue;
                        }

                        $stmts[] = $stmt;
                    }

                    return $stmts;
                }
            }

            return null;
        };

        return $this->statesStmts[$className] = $recursiveExtraction($node, $recursiveExtraction);
    }

    public function leaveNode(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        foreach ($node->implements as $className) {
            $interfaceName = $className->toString();

            $className = (string) $node->namespacedName;
            if (ProxyInterface::class === $interfaceName) {
                $classes = $this->listStatesFromProxyClass($className);
                foreach ($classes as $class) {
                    $node->stmts = $node->stmts + $this->getStateStmts($class);
                }
            }

            if (StateInterface::class === $interfaceName) {
                $this->$statesStmts[$className] = [];
                foreach ($node->stmts as $stmt) {
                    if (!$stmt instanceof ClassMethod) {
                        continue;
                    }

                    $this->$statesStmts[$className][] = $stmt;
                }
                $node->stmts = [];
            }
        }

        return $node;
    }
}
