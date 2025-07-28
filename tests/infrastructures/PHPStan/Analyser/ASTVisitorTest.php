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

namespace Teknoo\Tests\States\PHPStan\Analyser;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Parser\Parser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
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
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(ASTVisitor::class)]
class ASTVisitorTest extends TestCase
{
    /**
     * @var callable|null
     */
    private mixed $reflectionProvider = null;

    private ?Parser $parser = null;

    public string $currentClass = '';

    private function getReflectionProviderMock(bool|string $file = 'foo/bar.php'): callable
    {
        if (!$this->reflectionProvider) {
            $this->reflectionProvider = function (string $class) use ($file): ReflectionClass&MockObject {
                $this->currentClass = $class;

                $mock = $this->createMock(ReflectionClass::class);

                $mock->method('getFileName')->willReturn($file);

                return $mock;
            };
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
        $this->assertInstanceOf(
            Node::class,
            $this->buildVisitor()->leaveNode(
                $this->createMock(Node::class)
            )
        );
    }

    public function testLeaveNodeWithNonStatedClassNode(): void
    {
        $this->assertInstanceOf(
            Node::class,
            $this->buildVisitor()->leaveNode(
                $this->createMock(Class_::class)
            )
        );
    }

    public function testLeaveNodeWithStateClassNode(): void
    {
        $closure = $this->createMock(
            Node\Expr\Closure::class
        );
        $closure->params = [];
        $closure->attrGroups = [];
        $closure->stmts = [];
        $closure->returnType = new Node\Identifier('void');

        $method = new ClassMethod(
            'foo',
            [
                'returnType' => new Node\Identifier('callable'),
                'stmts' => [
                    new Node\Stmt\Return_(
                        $closure,
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
        $stateClass->namespacedName = new Name('state');

        $this->assertInstanceOf(
            Node::class,
            $result = $this->buildVisitor()->leaveNode($stateClass)
        );

        $this->assertCount(1, $result->stmts);
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
        $proxyClass->namespacedName = new Name(MockProxy::class);

        $this->assertInstanceOf(
            Node::class,
            $result = $this->buildVisitor()->leaveNode($proxyClass)
        );

        $this->assertNotEmpty($result->stmts);
        $this->assertCount(1, $result->stmts);
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
        $proxyClass->namespacedName = new Name(MockProxyWithoutDeclaration::class);

        $this->assertInstanceOf(Node::class, $result = $this->buildVisitor()->leaveNode($proxyClass));

        $this->assertNotEmpty($result->stmts);
        $this->assertCount(1, $result->stmts);
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
        $stateClass->namespacedName = new Name(StateOne::class);

        $proxyClass = new Class_(
            Mother::class,
            [
                'stmts' => [$this->createMock(Node::class)],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = new Name(Mother::class);

        $visitor = $this->buildVisitor();

        $this->getParserMock()
            ->method('parseFile');

        $this->assertInstanceOf(Node::class, $result = $visitor->leaveNode($stateClass));

        $this->assertEmpty($result->stmts);

        $this->assertInstanceOf(Node::class, $result = $visitor->leaveNode($proxyClass));

        $this->assertNotEmpty($result->stmts);
        $this->assertCount(1, $result->stmts);
    }

    public function testLeaveNodeWithProxyClassNodeWithStateFileNotFound(): void
    {
        $this->getReflectionProviderMock(false);

        $proxyClass = new Class_(
            Mother::class,
            [
                'stmts' => [$this->createMock(Node::class)],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = new Name(Mother::class);

        $visitor = $this->buildVisitor();

        $this->getParserMock()
            ->expects($this->never())
            ->method('parseFile');

        $this->assertInstanceOf(Node::class, $result = $visitor->leaveNode($proxyClass));

        $this->assertNotEmpty($result->stmts);
        $this->assertCount(1, $result->stmts);
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

        $state1Class->namespacedName = new Name(StateOne::class);

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

        $state2Class->namespacedName = new Name(StateTwo::class);

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
        $proxyClass->namespacedName = new Name(Mother::class);

        $visitor = $this->buildVisitor();

        $this->assertInstanceOf(Node::class, $result = $visitor->leaveNode($state1Class));
        $this->assertCount(1, $result->stmts);

        $this->assertInstanceOf(Node::class, $result = $visitor->leaveNode($state2Class));
        $this->assertCount(1, $result->stmts);

        $this->assertInstanceOf(Node::class, $result = $visitor->leaveNode($proxyClass));

        $this->assertNotEmpty($result->stmts);
        $this->assertCount(5, $result->stmts);
    }
}
