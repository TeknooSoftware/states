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
use Teknoo\Tests\Support\Extendable\GrandDaughter\GrandDaughter;
use Teknoo\Tests\Support\Extendable\Mother\Mother;
use Teknoo\Tests\Support\Extendable\Mother\States\StateOne;
use Teknoo\Tests\Support\MockProxy;

/**
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
    private function getReflectionProviderMock(): ReflectionProvider
    {
        if (!$this->reflectionProvider instanceof ReflectionProvider) {
            $this->reflectionProvider = $this->createMock(ReflectionProvider::class);

            $classReflection = $this->createMock(ClassReflection::class);
            $classReflection->expects(self::any())
                ->method('getFileName')
                ->willReturn('foo/bar.php');
            $classReflection->expects(self::any())
                ->method('getNativeReflection')
                ->willReturnCallback(
                    function () {
                        return new \ReflectionClass($this->currentClass);
                    }
                );

            $this->reflectionProvider->expects(self::any())
                ->method('getClass')
                ->willReturnCallback(
                    function (string $class) use ($classReflection) {
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

    public function testLeaveNodeWithNonClassNode()
    {
        self::assertInstanceOf(
            Node::class,
            $this->buildVisitor()->leaveNode(
                $this->createMock(Node::class)
            )
        );
    }

    public function testLeaveNodeWithNonStatedClassNode()
    {
        self::assertInstanceOf(
            Node::class,
            $this->buildVisitor()->leaveNode(
                $this->createMock(Class_::class)
            )
        );
    }

    public function testLeaveNodeWithStateClassNode()
    {
        $stateClass = new Class_(
            'state',
            [
                'stmts' => [$this->createMock(Node::class)],
                'implements' => [new Name(StateInterface::class)]
            ]
        );
        $stateClass->namespacedName = 'state';

        self::assertInstanceOf(
            Node::class,
            $result = $this->buildVisitor()->leaveNode($stateClass)
        );

        self::assertTrue(empty($result->stmts));
    }

    public function testLeaveNodeWithProxyClassNodeWithoutState()
    {
        $proxyClass = new Class_(
            MockProxy::class,
            [
                'stmts' => [$this->createMock(Node::class)],
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

    public function testLeaveNodeWithProxyClassNodeWithStateAlreadyFetched()
    {
        $stateClass = new Class_(
            StateOne::class,
            [
                'stmts' => [
                    $this->createMock(Node::class),
                    $this->createMock(ClassMethod::class),
                ],
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
            ->method('parseFile')
            ->willReturn([
                $this->createMock(Node::class),
                new Node\Stmt\Namespace_(new Name('Foo\\Bar'), [clone $stateClass]),
                $this->createMock(Node::class),
            ]);

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
        self::assertCount(4, $result->stmts);
    }

    public function testLeaveNodeWithProxyClassNodeWithStateNotAlreadyFetched()
    {
        $stateClass = new Class_(
            StateOne::class,
            [
                'stmts' => [
                    $this->createMock(Node::class),
                    $this->createMock(ClassMethod::class),
                ],
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
            ->method('parseFile')
            ->willReturn([
                $this->createMock(Node::class),
                new Node\Stmt\Namespace_(new Name('Foo\\Bar'), [$stateClass]),
                $this->createMock(Node::class),
            ]);

        self::assertInstanceOf(
            Node::class,
            $result = $visitor->leaveNode($proxyClass)
        );

        self::assertTrue(!empty($result->stmts));
        self::assertCount(4, $result->stmts);
    }

    public function testLeaveNodeWithProxyClassNodeWithInheritance()
    {
        $stateClass = new Class_(
            StateOne::class,
            [
                'stmts' => [
                    $this->createMock(Node::class),
                    $this->createMock(ClassMethod::class),
                ],
                'implements' => [new Name(StateInterface::class)]
            ]
        );
        $stateClass->namespacedName = StateOne::class;

        $proxyClass = new Class_(
            GrandDaughter::class,
            [
                'stmts' => [$this->createMock(Node::class)],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = GrandDaughter::class;

        $visitor = $this->buildVisitor();

        $this->getParserMock()
            ->expects(self::any())
            ->method('parseFile')
            ->willReturn([
                $this->createMock(Node::class),
                new Node\Stmt\Namespace_(new Name('Foo\\Bar'), [$stateClass]),
                $this->createMock(Node::class),
            ]);

        self::assertInstanceOf(
            Node::class,
            $result = $visitor->leaveNode($proxyClass)
        );

        self::assertTrue(!empty($result->stmts));
        self::assertCount(9, $result->stmts);
    }
}