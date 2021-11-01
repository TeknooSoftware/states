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

use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\VariableTypeHolder;
use PHPStan\Broker\Broker;
use PHPStan\Parser\Parser;
use PHPStan\Php\PhpVersion;
use PHPStan\Reflection\ClassReflection;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Properties\PropertyReflectionFinder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\DynamicReturnTypeExtensionRegistry;
use PHPStan\Type\OperatorTypeSpecifyingExtensionRegistry;
use PHPStan\Type\StringType;
use PHPStan\Type\ThisType;
use Teknoo\States\PHPStan\Analyser\Scope;
use Teknoo\States\State\StateTrait;
use PHPUnit\Framework\TestCase;
use Teknoo\Tests\Support\Article\Article;
use Teknoo\Tests\Support\Article\Article\Draft;

/**
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers      \Teknoo\States\PHPStan\Analyser\Scope
 */
class ScopeTest extends TestCase
{
    private function prepareArguments(): array
    {
        return [
            $this->createMock(ScopeFactory::class), //0: ScopeFactory $scopeFactory
            $this->createMock(ReflectionProvider::class), //1: $reflectionProvider,
            $this->createMock(DynamicReturnTypeExtensionRegistry::class), //2: $dynamicReturnTypeExtensionRegistry,
            $this->createMock(OperatorTypeSpecifyingExtensionRegistry::class), //3 $operatorTypeSpecifyingExtensionRegistry,
            $this->createMock(Standard::class), //4: Standard $printer,
            $this->createMock(TypeSpecifier::class), //5: TypeSpecifier $typeSpecifier,
            $this->createMock(PropertyReflectionFinder::class), //6: PropertyReflectionFinder $propertyReflectionFinder,
            $this->createMock(Parser::class), //7 Parser $parser
            $this->createMock(NodeScopeResolver::class), //8: NodeScopeResolver $nodeScopeResolver
            $this->createMock(ScopeContext::class), //9: ScopeContext $context,
            $this->createMock(PhpVersion::class), //10: ScopeContext $context,
            false, //11: bool $declareStrictTypes = \false,
            [], //12: $constantTypes = []
            null, //13: $function = null,
            null, //14: ?string $namespace = null,
            ['foo' => VariableTypeHolder::createYes(new StringType())], //15: array $variablesTypes = [],
            [], //16: array $moreSpecificTypes = [],
            [], //17 : $conditionalExpressions
            null, //18: ?string $inClosureBindScopeClass = null,
            null, //19: ?ParametersAcceptor $anonymousFunctionReflection = null,
            true, //20: bool $inFirstLevelStatement = \true,
            [], //21: array $currentlyAssignedExpressions = [],
            [] //22: array $dynamicConstantNames = []
        ];
    }

    public function testNoScopeUpdatingWithoutAnonymousReflectionAndClassReflection()
    {
        $arguments = $this->prepareArguments();
        $scope = new Scope(...$arguments);
        self::assertEquals(new StringType(), $scope->getVariableType('foo'));
    }

    public function testNoScopeUpdatingWithAnonymousReflectionAndNotClassReflection()
    {
        $arguments = $this->prepareArguments();
        $arguments[19] = $this->createMock(ParametersAcceptor::class);
        $scope = new Scope(...$arguments);
        self::assertEquals(new StringType(), $scope->getVariableType('foo'));
    }

    public function testNoScopeUpdatingWhenNoStateTraitUsed()
    {
        $arguments = $this->prepareArguments();
        $arguments[19] = $this->createMock(ParametersAcceptor::class);
        $arguments[9]->expects(self::any())->method('getClassReflection')->willReturn($classReflection = $this->createMock(ClassReflection::class));
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('getTraitNames')->willReturn([]);
        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);
        $scope = new Scope(...$arguments);
        self::assertEquals(new StringType(), $scope->getVariableType('foo'));
    }

    public function testNoScopeUpdatingWithoutThisDefinedInScope()
    {
        $arguments = $this->prepareArguments();
        $arguments[19] = $this->createMock(ParametersAcceptor::class);
        $arguments[9]->expects(self::any())->method('getClassReflection')->willReturn($classReflection = $this->createMock(ClassReflection::class));
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('getTraitNames')->willReturn([StateTrait::class]);
        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);
        $scope = new Scope(...$arguments);
        self::assertEquals(new StringType(), $scope->getVariableType('foo'));
    }

    public function testExceptionWhenProxyClassDoesNotExist()
    {
        $this->expectException(ShouldNotHappenException::class);

        $arguments = $this->prepareArguments();
        $arguments[19] = $this->createMock(ParametersAcceptor::class);
        $arguments[9]->expects(self::any())->method('getClassReflection')->willReturn($classReflection = $this->createMock(ClassReflection::class));
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('getTraitNames')->willReturn([StateTrait::class]);
        $nativeReflection->expects(self::any())->method('getName')->willReturn('Foo\Bar\Not\ExistClass');
        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);
        $arguments[15]['this'] = VariableTypeHolder::createYes($this->createMock(ThisType::class));
        $scope = new Scope(...$arguments);
    }

    public function testExceptionWhenProxyClassDoesNotImplementInterface()
    {
        $this->expectException(ShouldNotHappenException::class);

        $arguments = $this->prepareArguments();
        $arguments[19] = $this->createMock(ParametersAcceptor::class);
        $arguments[9]->expects(self::any())->method('getClassReflection')->willReturn($classReflection = $this->createMock(ClassReflection::class));
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('getTraitNames')->willReturn([StateTrait::class]);
        $nativeReflection->expects(self::any())->method('getName')->willReturn(\DateTime::class.'\\Foo');
        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);
        $arguments[15]['this'] = VariableTypeHolder::createYes($this->createMock(ThisType::class));
        $scope = new Scope(...$arguments);
    }

    public function testScopeUpdating()
    {
        $arguments = $this->prepareArguments();
        $arguments[19] = $this->createMock(ParametersAcceptor::class);
        $arguments[9]->expects(self::any())->method('getClassReflection')->willReturn($classReflection = $this->createMock(ClassReflection::class));
        $nativeReflection = $this->createMock(\ReflectionClass::class);
        $nativeReflection->expects(self::any())->method('getTraitNames')->willReturn([StateTrait::class]);
        $nativeReflection->expects(self::any())->method('getName')->willReturn(Draft::class);
        $classReflection->expects(self::any())->method('getNativeReflection')->willReturn($nativeReflection);
        $arguments[15]['this'] = VariableTypeHolder::createYes($this->createMock(ThisType::class));
        $broker = $this->createMock(Broker::class);
        Broker::registerInstance($broker);
        $scope = new Scope(...$arguments);

        self::assertEquals(new StringType(), $scope->getVariableType('foo'));
        $type = $scope->getVariableType('this');
        self::assertInstanceOf(ThisType::class, $type);
    }
}
