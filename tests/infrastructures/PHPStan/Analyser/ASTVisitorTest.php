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

namespace Teknoo\Tests\States\PHPStan\Analyser;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Parser\Parser;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Teknoo\States\PHPStan\Analyser\ASTVisitor;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;
use Teknoo\Tests\Support\Extendable\Mother\Mother;
use Teknoo\Tests\Support\Extendable\Mother\States\StateOne;
use Teknoo\Tests\Support\Extendable\Mother\States\StateTwo;
use Teknoo\Tests\Support\MockProxy;
use Teknoo\Tests\Support\MockProxyWithoutDeclaration;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @covers      \Teknoo\States\PHPStan\Analyser\ASTVisitor
 */
class ASTVisitorTest extends TestCase
{
    private ?ReflectionProvider $reflectionProvider = null;

    private ?Parser $parser = null;

    public string $currentClass = '';

    /**
     * @return ReflectionProvider|MockObject
     */
    private function getReflectionProviderMock(?string $file = 'foo/bar.php'): ReflectionProvider
    {
        if (!$this->reflectionProvider instanceof ReflectionProvider) {
            $this->reflectionProvider = $this->createMock(ReflectionProvider::class);

            $classReflection = $this->createMock(ClassReflection::class);
            $classReflection->expects(self::any())
                ->method('getFileName')
                ->willReturn($file);
            $classReflection->expects(self::any())
                ->method('getNativeReflection')
                ->willReturnCallback(
                    fn (): \ReflectionClass => new \ReflectionClass($this->currentClass)
                );

            $this->reflectionProvider->expects(self::any())
                ->method('getClass')
                ->willReturnCallback(
                    function (string $class) use ($classReflection): \PHPStan\Reflection\ClassReflection&\PHPUnit\Framework\MockObject\MockObject {
                        $this->currentClass = $class;

                        return $classReflection;
                    }
                );
        }

        return $this->reflectionProvider;
    }

    /**
     * @return Parser|MockObject
     */
    private function getParserMock(): Parser
    {
        if (!$this->parser instanceof Parser) {
            $this->parser = $this->createMock(Parser::class);
        }

        return $this->parser;
    }

    public function buildVisitor(): ASTVisitor
    {
        return new ASTVisitor(
            $this->getReflectionProviderMock(),
            $this->getParserMock()
        );
    }

    public function testLeaveNodeWithNonClassNode(): void
    {
        self::assertInstanceOf(
            Node::class,
            $this->buildVisitor()->leaveNode(
                $this->createMock(Node::class)
            )
        );
    }

    public function testLeaveNodeWithNonStatedClassNode(): void
    {
        self::assertInstanceOf(
            Node::class,
            $this->buildVisitor()->leaveNode(
                $this->createMock(Class_::class)
            )
        );
    }

    public function testLeaveNodeWithStateClassNode(): void
    {
        $method = new ClassMethod(
            'foo',
            [
                'returnType' => 'callable',
                'stmts' => [
                    new Node\Stmt\Return_(
                        $this->createMock(Node\Expr\Closure::class),
                    )
                ],
            ]
        );

        $stateClass = new Class_(
            'state',
            [
                'stmts' => [
                    $this->createMock(Node::class),
                    $method,
                ],
                'implements' => [new Name(StateInterface::class)]
            ]
        );
        $stateClass->namespacedName = 'state';

        self::assertInstanceOf(
            Node::class,
            $result = $this->buildVisitor()->leaveNode($stateClass)
        );

        self::assertCount(1, $result->stmts);
    }

    public function testLeaveNodeWithProxyClassNodeWithoutState(): void
    {
        $proxyClass = new Class_(
            MockProxy::class,
            [
                'stmts' => [
                    $this->createMock(Node::class),
                ],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = MockProxy::class;

        self::assertInstanceOf(
            Node::class,
            $result = $this->buildVisitor()->leaveNode($proxyClass)
        );

        self::assertTrue(!empty($result->stmts));
        self::assertCount(1, $result->stmts);
    }

    public function testLeaveNodeWithProxyClassNodeWithoutstatesListDeclaration(): void
    {
        $proxyClass = new Class_(
            MockProxyWithoutDeclaration::class,
            [
                'stmts' => [$this->createMock(Node::class)],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = MockProxyWithoutDeclaration::class;

        self::assertInstanceOf(
            Node::class,
            $result = $this->buildVisitor()->leaveNode($proxyClass)
        );

        self::assertTrue(!empty($result->stmts));
        self::assertCount(1, $result->stmts);
    }
    public function testLeaveNodeWithProxyClassNodeWithEmptyFile(): void
    {
        $stateClass = new Class_(
            StateOne::class,
            [
                'stmts' => [],
                'implements' => [new Name(StateInterface::class)]
            ]
        );
        $stateClass->namespacedName = StateOne::class;

        $proxyClass = new Class_(
            Mother::class,
            [
                'stmts' => [$this->createMock(Node::class)],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = Mother::class;

        $visitor = $this->buildVisitor();

        $this->getParserMock()
            ->expects(self::any())
            ->method('parseFile');

        self::assertInstanceOf(
            Node::class,
            $result = $visitor->leaveNode($stateClass)
        );

        self::assertTrue(empty($result->stmts));

        self::assertInstanceOf(
            Node::class,
            $result = $visitor->leaveNode($proxyClass)
        );

        self::assertTrue(!empty($result->stmts));
        self::assertCount(1, $result->stmts);
    }

    public function testLeaveNodeWithProxyClassNodeWithStateFileNotFound(): void
    {
        $this->getReflectionProviderMock(null);

        $proxyClass = new Class_(
            Mother::class,
            [
                'stmts' => [$this->createMock(Node::class)],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = Mother::class;

        $visitor = $this->buildVisitor();

        $this->getParserMock()
            ->expects(self::never())
            ->method('parseFile');

        self::assertInstanceOf(
            Node::class,
            $result = $visitor->leaveNode($proxyClass)
        );

        self::assertTrue(!empty($result->stmts));
        self::assertCount(1, $result->stmts);
    }


    public function testLeaveNodeWithProxyClassNode(): void
    {
        $state1Class = new Class_(
            'StateOne',
            [
                'stmts' => [
                    $this->createMock(Node::class),
                    new ClassMethod(new Node\Identifier('foo'), []),
                ],
                'implements' => [new Name(StateInterface::class)]
            ]
        );

        $state1Class->namespacedName = StateOne::class;

        $state2Class = new Class_(
            'StateTwo',
            [
                'stmts' => [
                    $this->createMock(Node::class),
                    new ClassMethod(new Node\Identifier('foo'), []),
                    new ClassMethod(new Node\Identifier('bar'), []),
                ],
                'implements' => [new Name(StateInterface::class)]
            ]
        );

        $state2Class->namespacedName = StateTwo::class;

        $proxyClass = new Class_(
            Mother::class,
            [
                'stmts' => [
                    $this->createMock(Node::class),
                    new ClassMethod(new Node\Identifier('hello'), []),
                ],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = Mother::class;

        $visitor = $this->buildVisitor();

        self::assertInstanceOf(
            Node::class,
            $result = $visitor->leaveNode($state1Class)
        );
        self::assertCount(1, $result->stmts);

        self::assertInstanceOf(
            Node::class,
            $result = $visitor->leaveNode($state2Class)
        );
        self::assertCount(1, $result->stmts);

        self::assertInstanceOf(
            Node::class,
            $result = $visitor->leaveNode($proxyClass)
        );

        self::assertTrue(!empty($result->stmts));
        self::assertCount(5, $result->stmts);
    }
}
