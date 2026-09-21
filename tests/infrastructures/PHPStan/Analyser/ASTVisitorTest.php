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

use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Parser\Parser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Teknoo\States\PHPStan\Analyser\ASTVisitor;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;
use Teknoo\Tests\Support\Extendable\Mother\Mother;
use Teknoo\Tests\Support\Extendable\Mother\MotherLegacy;
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
            $this->reflectionProvider = function (string $class) use ($file): ReflectionClass {
                $this->currentClass = $class;

                $mock = $this->createStub(ReflectionClass::class);

                $mock->method('getFileName')->willReturn($file);

                return $mock;
            };
        }

        return $this->reflectionProvider;
    }

    private function getParserStub(): Parser&Stub
    {
        if (!$this->parser instanceof Parser) {
            $this->parser = $this->createStub(Parser::class);
        }

        return $this->parser;
    }

    public function buildVisitor(): ASTVisitor
    {
        return new ASTVisitor(
            $this->getReflectionProviderMock(),
            $this->getParserStub()
        );
    }

    public function testLeaveNodeWithNonClassNode(): void
    {
        $this->assertInstanceOf(
            Node::class,
            $this->buildVisitor()->leaveNode(
                $this->createStub(Node::class)
            )
        );
    }

    public function testLeaveNodeWithNonStatedClassNode(): void
    {
        $this->assertInstanceOf(
            Node::class,
            $this->buildVisitor()->leaveNode(
                $this->createStub(Class_::class)
            )
        );
    }

    public function testLeaveNodeWithStateClassNode(): void
    {
        $closure = $this->createStub(
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
                    $this->createStub(Node::class),
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
                    $this->createStub(Node::class),
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
                'stmts' => [$this->createStub(Node::class)],
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
                'stmts' => [$this->createStub(Node::class)],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = new Name(Mother::class);

        $visitor = $this->buildVisitor();

        $this->assertInstanceOf(Node::class, $result = $visitor->leaveNode($stateClass));

        $this->assertEmpty($result->stmts);

        $this->assertInstanceOf(Node::class, $result = $visitor->leaveNode($proxyClass));

        $this->assertNotEmpty($result->stmts);
        $this->assertCount(1, $result->stmts);
    }

    public function testLeaveNodeWithProxyClassNodeWithStateFileNotFound(): void
    {
        $proxyClass = new Class_(
            Mother::class,
            [
                'stmts' => [$this->createStub(Node::class)],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = new Name(Mother::class);

        $parserMock = $this->createMock(Parser::class);
        $parserMock
            ->expects($this->never())
            ->method('parseFile');

        $visitor = new ASTVisitor(
            $this->getReflectionProviderMock(false),
            $parserMock
        );

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
                    $this->createStub(Node::class),
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
                    $this->createStub(Node::class),
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
                    $this->createStub(Node::class),
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

    /**
     * A method available in several states is renamed and set as public in the proxy's node, to avoid false
     * positives : only its visibility must be changed, others modifiers (static, final, abstract) must be kept.
     */
    public function testRenamedDuplicateKeepsNonVisibilityModifiers(): void
    {
        $state1Class = new Class_(
            'StateOne',
            [
                'stmts' => [
                    new ClassMethod(new Node\Identifier('foo'), []),
                    new ClassMethod(new Node\Identifier('bar'), []),
                    new ClassMethod(new Node\Identifier('hello'), []),
                ],
                'implements' => [new Name(StateInterface::class)]
            ]
        );
        $state1Class->namespacedName = new Name(StateOne::class);

        $state2Class = new Class_(
            'StateTwo',
            [
                'stmts' => [
                    new ClassMethod(new Node\Identifier('foo'), ['flags' => Modifiers::PRIVATE | Modifiers::FINAL]),
                    new ClassMethod(new Node\Identifier('bar'), ['flags' => Modifiers::PROTECTED | Modifiers::STATIC]),
                    new ClassMethod(new Node\Identifier('hello'), ['flags' => Modifiers::PUBLIC]),
                ],
                'implements' => [new Name(StateInterface::class)]
            ]
        );
        $state2Class->namespacedName = new Name(StateTwo::class);

        $proxyClass = new Class_(
            Mother::class,
            [
                'stmts' => [],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = new Name(Mother::class);

        $visitor = $this->buildVisitor();
        $visitor->leaveNode($state1Class);
        $visitor->leaveNode($state2Class);
        $result = $visitor->leaveNode($proxyClass);

        $this->assertInstanceOf(Class_::class, $result);

        $methods = [];
        foreach ($result->stmts as $stmt) {
            $this->assertInstanceOf(ClassMethod::class, $stmt);
            $methods[(string) $stmt->name] = $stmt;
        }

        $this->assertSame(['foo', 'bar', 'hello', 'foo1', 'bar1', 'hello1'], array_keys($methods));

        $this->assertTrue($methods['foo1']->isPublic());
        $this->assertFalse($methods['foo1']->isPrivate());
        $this->assertTrue($methods['foo1']->isFinal());
        $this->assertFalse($methods['foo1']->isStatic());

        $this->assertTrue($methods['bar1']->isPublic());
        $this->assertFalse($methods['bar1']->isProtected());
        $this->assertTrue($methods['bar1']->isStatic());
        $this->assertFalse($methods['bar1']->isFinal());

        $this->assertTrue($methods['hello1']->isPublic());
        $this->assertSame(Modifiers::PUBLIC, $methods['hello1']->flags);
    }

    /**
     * Builders are replaced, in the proxy's node, by the closure they return : it can be a closure or an arrow
     * function (which has no statement, only an expression to return).
     */
    public function testLeaveNodeWithBuildersReturningAClosureOrAnArrowFunction(): void
    {
        $closure = new Node\Expr\Closure([
            'params' => [new Node\Param(new Node\Expr\Variable('name'))],
            'returnType' => new Node\Identifier('string'),
            'stmts' => [new Node\Stmt\Return_(new Node\Scalar\String_('hello'))],
        ]);

        $state1Class = new Class_(
            'StateOne',
            [
                'stmts' => [
                    new ClassMethod(
                        new Node\Identifier('sayHello'),
                        [
                            'returnType' => new Name\FullyQualified('Closure'),
                            'stmts' => [new Node\Stmt\Return_($closure)],
                        ]
                    ),
                ],
                'implements' => [new Name(StateInterface::class)],
            ]
        );
        $state1Class->namespacedName = new Name(StateOne::class);

        $arrowFunction = new Node\Expr\ArrowFunction([
            'params' => [new Node\Param(new Node\Expr\Variable('now'))],
            'returnType' => new Node\Identifier('int'),
            'expr' => new Node\Scalar\Int_(123),
        ]);

        $state2Class = new Class_(
            'StateTwo',
            [
                'stmts' => [
                    new ClassMethod(
                        new Node\Identifier('displayDate'),
                        [
                            'returnType' => new Name\FullyQualified('Closure'),
                            'stmts' => [new Node\Stmt\Return_($arrowFunction)],
                        ]
                    ),
                ],
                'implements' => [new Name(StateInterface::class)],
            ]
        );
        $state2Class->namespacedName = new Name(StateTwo::class);

        $proxyClass = new Class_(
            Mother::class,
            [
                'stmts' => [],
                'implements' => [new Name(ProxyInterface::class)],
            ]
        );
        $proxyClass->namespacedName = new Name(Mother::class);

        $visitor = $this->buildVisitor();

        //Methods of states are removed from theirs nodes
        $this->assertInstanceOf(Class_::class, $result = $visitor->leaveNode($state1Class));
        $this->assertCount(0, $result->stmts);
        $this->assertInstanceOf(Class_::class, $result = $visitor->leaveNode($state2Class));
        $this->assertCount(0, $result->stmts);

        //To be added in the proxy's node, where builders are replaced by theirs closures
        $this->assertInstanceOf(Class_::class, $result = $visitor->leaveNode($proxyClass));
        $this->assertCount(2, $result->stmts);

        [$sayHello, $displayDate] = $result->stmts;

        //Nodes are cloned by the visitor : only theirs values are compared
        $this->assertInstanceOf(ClassMethod::class, $sayHello);
        $this->assertSame('sayHello', (string) $sayHello->name);
        $this->assertCount(1, $sayHello->params);
        $this->assertInstanceOf(Node\Expr\Variable::class, $sayHello->params[0]->var);
        $this->assertSame('name', $sayHello->params[0]->var->name);
        $this->assertInstanceOf(Node\Identifier::class, $sayHello->returnType);
        $this->assertSame('string', $sayHello->returnType->name);
        $this->assertIsArray($sayHello->stmts);
        $this->assertCount(1, $sayHello->stmts);
        $this->assertInstanceOf(Node\Stmt\Return_::class, $sayHello->stmts[0]);
        $this->assertInstanceOf(Node\Scalar\String_::class, $sayHello->stmts[0]->expr);
        $this->assertSame('hello', $sayHello->stmts[0]->expr->value);

        $this->assertInstanceOf(ClassMethod::class, $displayDate);
        $this->assertSame('displayDate', (string) $displayDate->name);
        $this->assertCount(1, $displayDate->params);
        $this->assertInstanceOf(Node\Expr\Variable::class, $displayDate->params[0]->var);
        $this->assertSame('now', $displayDate->params[0]->var->name);
        $this->assertInstanceOf(Node\Identifier::class, $displayDate->returnType);
        $this->assertSame('int', $displayDate->returnType->name);
        $this->assertIsArray($displayDate->stmts);
        $this->assertCount(1, $displayDate->stmts);
        $this->assertInstanceOf(Node\Stmt\Return_::class, $displayDate->stmts[0]);
        $this->assertInstanceOf(Node\Scalar\Int_::class, $displayDate->stmts[0]->expr);
        $this->assertSame(123, $displayDate->stmts[0]->expr->value);
    }

    /**
     * A static closure is only bound to the scope of the stated class, `$this` is not available : the method added
     * in the proxy's node must be static too, to be analysed like it is executed.
     */
    public function testLeaveNodeWithBuildersReturningStaticClosures(): void
    {
        $buildBuilder = static function (string $name, Node\Expr $closure): ClassMethod {
            return new ClassMethod(
                new Node\Identifier($name),
                [
                    'flags' => Modifiers::PROTECTED,
                    'returnType' => new Name\FullyQualified('Closure'),
                    'stmts' => [new Node\Stmt\Return_($closure)],
                ]
            );
        };

        $stateClass = new Class_(
            'StateOne',
            [
                'stmts' => [
                    $buildBuilder('staticClosure', new Node\Expr\Closure(['static' => true, 'stmts' => []])),
                    $buildBuilder(
                        'staticArrowFunction',
                        new Node\Expr\ArrowFunction(['static' => true, 'expr' => new Node\Scalar\Int_(123)])
                    ),
                    $buildBuilder('closure', new Node\Expr\Closure(['stmts' => []])),
                    $buildBuilder('arrowFunction', new Node\Expr\ArrowFunction(['expr' => new Node\Scalar\Int_(123)])),
                ],
                'implements' => [new Name(StateInterface::class)],
            ]
        );
        $stateClass->namespacedName = new Name(StateOne::class);

        $proxyClass = new Class_(
            Mother::class,
            [
                'stmts' => [],
                'implements' => [new Name(ProxyInterface::class)],
            ]
        );
        $proxyClass->namespacedName = new Name(Mother::class);

        $visitor = $this->buildVisitor();
        $visitor->leaveNode($stateClass);
        $result = $visitor->leaveNode($proxyClass);

        $this->assertInstanceOf(Class_::class, $result);

        $methods = [];
        foreach ($result->stmts as $stmt) {
            $this->assertInstanceOf(ClassMethod::class, $stmt);
            $methods[(string) $stmt->name] = $stmt;
        }

        $this->assertSame(['staticClosure', 'staticArrowFunction', 'closure', 'arrowFunction'], array_keys($methods));

        $this->assertTrue($methods['staticClosure']->isStatic());
        $this->assertTrue($methods['staticArrowFunction']->isStatic());
        $this->assertFalse($methods['closure']->isStatic());
        $this->assertFalse($methods['arrowFunction']->isStatic());

        //The visibility of the builder is kept
        foreach ($methods as $method) {
            $this->assertTrue($method->isProtected());
        }
    }

    /**
     * A builder is only replaced by its closure when it directly returns this closure : all others builders must be
     * kept unchanged, without error.
     */
    public function testLeaveNodeWithBuilderNotDirectlyReturningAClosure(): void
    {
        $builderStmts = [
            new Node\Stmt\Expression(
                new Node\Expr\Assign(new Node\Expr\Variable('closure'), new Node\Expr\ConstFetch(new Name('null')))
            ),
            new Node\Stmt\Return_(new Node\Expr\Variable('closure')),
        ];

        $stateClass = new Class_(
            'StateOne',
            [
                'stmts' => [
                    new ClassMethod(
                        new Node\Identifier('foo'),
                        [
                            'returnType' => new Name\FullyQualified('Closure'),
                            'stmts' => $builderStmts,
                        ]
                    ),
                ],
                'implements' => [new Name(StateInterface::class)],
            ]
        );
        $stateClass->namespacedName = new Name(StateOne::class);

        $proxyClass = new Class_(
            Mother::class,
            [
                'stmts' => [],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = new Name(Mother::class);

        $visitor = $this->buildVisitor();
        $visitor->leaveNode($stateClass);
        $result = $visitor->leaveNode($proxyClass);

        $this->assertInstanceOf(Class_::class, $result);
        $this->assertCount(1, $result->stmts);
        $this->assertInstanceOf(ClassMethod::class, $result->stmts[0]);
        //Nodes are cloned by the visitor : only theirs values are compared
        $this->assertSame('foo', (string) $result->stmts[0]->name);
        $this->assertSame([], $result->stmts[0]->params);
        $this->assertInstanceOf(Name\FullyQualified::class, $result->stmts[0]->returnType);
        $this->assertSame('Closure', $result->stmts[0]->returnType->toString());
        $this->assertIsArray($result->stmts[0]->stmts);
        $this->assertCount(2, $result->stmts[0]->stmts);
        $this->assertInstanceOf(Node\Stmt\Expression::class, $result->stmts[0]->stmts[0]);
        $this->assertInstanceOf(Node\Stmt\Return_::class, $result->stmts[0]->stmts[1]);
    }

    #[IgnoreDeprecations]
    public function testLeaveNodeWithLegacyProxyClassNode(): void
    {
        $state1Class = new Class_(
            'StateOne',
            [
                'stmts' => [
                    $this->createStub(Node::class),
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
                    $this->createStub(Node::class),
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
                    $this->createStub(Node::class),
                    new ClassMethod(new Node\Identifier('hello'), []),
                ],
                'implements' => [new Name(ProxyInterface::class)]
            ]
        );
        $proxyClass->namespacedName = new Name(MotherLegacy::class);

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
