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
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\PHPStan\Reflection;

use PHPStan\BetterReflection\Reflection\Adapter\ReflectionFunction;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionMethod;
use PHPStan\BetterReflection\Reflection\ReflectionClass as BetterReflectionClass;
use PHPStan\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;
use PHPStan\BetterReflection\Reflection\ReflectionMethod as BetterReflectionMethod;
use PHPStan\Reflection\Assertions;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Teknoo\States\PHPStan\Contracts\Reflection\AttributeReflectionFactoryInterface;
use Teknoo\States\PHPStan\Contracts\Reflection\InitializerExprTypeResolverInterface;
use Teknoo\States\PHPStan\Reflection\StateMethod;
use ReflectionIntersectionType as NativeReflectionIntersectionType;
use ReflectionNamedType as NativeReflectionNamedType;
use ReflectionUnionType as NativeReflectionUnionType;

use function ini_get;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(StateMethod::class)]
class StateMethodEmptyTest extends TestCase
{
    private (ReflectionProvider&MockObject)|null $reflectionProvider = null;

    private (AttributeReflectionFactoryInterface&MockObject)|null $attributeReflectionFactory = null;

    private (InitializerExprTypeResolverInterface&MockObject)|null $initializerExprTypeResolver = null;

    /**
     * @throws Exception
     */
    private function getReflectionProviderMock(): ReflectionProvider&MockObject
    {
        if (!$this->reflectionProvider instanceof ReflectionProvider) {
            $this->reflectionProvider = $this->createMock(ReflectionProvider::class);
        }

        return $this->reflectionProvider;
    }

    /**
     * @throws Exception
     */
    private function getAttributeReflectionFactoryMock(): AttributeReflectionFactoryInterface&MockObject
    {
        if (!$this->attributeReflectionFactory instanceof AttributeReflectionFactoryInterface) {
            $this->attributeReflectionFactory = $this->createMock(AttributeReflectionFactoryInterface::class);
        }

        return $this->attributeReflectionFactory;
    }

    /**
     * @throws Exception
     */
    private function getInitializerExprTypeResolverMock(): InitializerExprTypeResolverInterface&MockObject
    {
        if (!$this->initializerExprTypeResolver instanceof InitializerExprTypeResolverInterface) {
            $this->initializerExprTypeResolver = $this->createMock(InitializerExprTypeResolverInterface::class);
        }

        return $this->initializerExprTypeResolver;
    }

    protected function buildInstance(?string $doc = 'factory doc'): StateMethod
    {
        $factoryReflection = $this->createMock(BetterReflectionMethod::class);
        $factoryReflection->method('getName')->willReturn('factory');
        $factoryReflection->method('getDocComment')->willReturn($doc);
        $factoryReflection->method('isPrivate')->willReturn(false);
        $factoryReflection->method('isPublic')->willReturn(false);

        $fr = new ReflectionMethod($factoryReflection);

        $closureReflection = $this->createMock(BetterReflectionFunction::class);
        $closureReflection->method('isVariadic')->willReturn(false);

        // Keep the parameter setup for the test even though they're not used by StateMethod
        $p1 = $this->createMock(\ReflectionParameter::class);
        $p2 = $this->createMock(\ReflectionParameter::class);
        $p3 = $this->createMock(\ReflectionParameter::class);

        $p1->method('getType')
           ->willReturn($rf1 = $this->createMock(NativeReflectionIntersectionType::class));

        $rf1->method('allowsNull')
            ->willReturn(true);

        $rf1->method('getTypes')
            ->willReturn([
                $rf11 = $this->createMock(NativeReflectionNamedType::class),
                $rf12 = $this->createMock(NativeReflectionNamedType::class),
            ]);

        $rf11->method('getName')
             ->willReturn('pt11');

        $rf12->method('getName')
             ->willReturn('pt12');

        $p1->method('isOptional')
           ->willReturn(false);

        $p2->method('getType')
           ->willReturn($rf2 = $this->createMock(NativeReflectionUnionType::class));

        $rf2->method('allowsNull')
            ->willReturn(true);

        $rf2->method('getTypes')
            ->willReturn([
                $rf21 = $this->createMock(NativeReflectionNamedType::class),
                $rf22 = $this->createMock(NativeReflectionNamedType::class),
            ]);

        $rf21->method('getName')
             ->willReturn('pt21');

        $rf22->method('getName')
             ->willReturn('pt22');

        $p2->method('isOptional')
           ->willReturn(false);

        $p3->method('getType')
           ->willReturn($rf3 = $this->createMock(NativeReflectionNamedType::class));

        $rf3->method('allowsNull')
            ->willReturn(true);

        $rf3->method('getName')
            ->willReturn('pt3');

        $p3->method('getDefaultValue')
           ->willReturn('foo');

        $p3->method('isOptional')
           ->willReturn(true);

        $cr = new ReflectionFunction($closureReflection);

        $brc = $this->createMock(BetterReflectionClass::class);

        $rcOfDc = new ReflectionClass(ClassReflection::class);
        $dc = $rcOfDc->newInstanceWithoutConstructor();
        $rpr = $rcOfDc->getProperty('reflection');
        $rpr->setValue($dc, $brc);

        return new StateMethod(
            reflectionProvider: $this->getReflectionProviderMock(),
            initializerExprTypeResolver: $this->getInitializerExprTypeResolverMock(),
            attributeReflectionFactory: $this->getAttributeReflectionFactoryMock(),
            factoryReflection: $fr,
            closureReflection: $cr,
            declaringClass: $dc,
            phpDocReturnType: null,
            phpDocThrowType: null,
            selfOutType: null,
            asserts: Assertions::createEmpty(),
            templateTypeMap: new TemplateTypeMap([]),
            isPure: false,
            attributes: [],
            acceptsNamedArguments: true,
        );
    }

    public function testGetDocCommentNull(): void
    {
        $this->assertNotSame(1, ini_get('zend.assertions'));
        $this->assertEmpty($this->buildInstance('')->getDocComment());
    }

    public function testReturnsByReference(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance()->returnsByReference());
    }
}
