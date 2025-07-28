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

namespace Teknoo\Tests\States\PHPStan\Reflection;

use _PHPStan_5878035a0\Nette\PhpGenerator\ClassType;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionFunction;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionMethod;
use PHPStan\BetterReflection\Reflection\ReflectionAttribute;
use PHPStan\BetterReflection\Reflection\ReflectionClass as BetterReflectionClass;
use PHPStan\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;
use PHPStan\BetterReflection\Reflection\ReflectionMethod as BetterReflectionMethod;
use PHPStan\BetterReflection\Reflection\ReflectionNamedType;
use PHPStan\Reflection\Assertions;
use PHPStan\Reflection\AttributeReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedParametersAcceptor;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\StringType;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use PHPStan\Type\VoidType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\PHPStan\Contracts\Reflection\AttributeReflectionFactoryInterface;
use Teknoo\States\PHPStan\Contracts\Reflection\InitializerExprTypeResolverInterface;
use Teknoo\States\PHPStan\Reflection\StateMethod;
use Teknoo\States\Proxy\ProxyInterface;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(StateMethod::class)]
class StateMethodTest extends TestCase
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

    /**
     * @param list<AttributeReflection> $attributes
     */
    protected function buildInstance(
        ?string $doc = 'factory doc',
        ?bool $isDeprecated = false,
        ?bool $acceptsNamedArguments = true,
        ?bool $isPure = false,
        ?Type $phpDocReturnType = null,
        ?Type $phpDocThrowType = null,
        ?Type $selfOutType = null,
        ?ReflectionNamedType $tentativeReturnType = null,
        array $attributes = [],
    ): StateMethod {
        $factoryReflection = $this->createMock(BetterReflectionMethod::class);
        $factoryReflection->method('getName')->willReturn('factory');
        $factoryReflection->method('getFileName')->willReturn('factory.php');
        $factoryReflection->expects($this->never())->method('getStartLine');
        $factoryReflection->expects($this->never())->method('getEndLine');
        $factoryReflection->method('getDocComment')->willReturn($doc);
        $factoryReflection->method('isStatic')->willReturn(false);
        $factoryReflection->method('isPrivate')->willReturn(false);
        $factoryReflection->method('isPublic')->willReturn(false);
        $factoryReflection->method('isDeprecated')->willReturn($isDeprecated);
        $factoryReflection->method('getPrototype')->willReturnSelf();
        $factoryReflection->method('getTentativeReturnType')->willReturn($tentativeReturnType);

        $attribute = $this->createMock(ReflectionAttribute::class);
        $attribute->method('getName')->willReturn('Deprecated');
        $attribute->method('getArguments')->willReturn([
            'foo bar',
        ]);

        $factoryReflection->method('getAttributes')->willReturn([$attribute]);
        $factoryReflection->method('isFinal')->willReturn(false);
        $factoryReflection->method('isInternal')->willReturn(false);
        $factoryReflection->method('isAbstract')->willReturn(false);
        $factoryReflection->expects($this->never())->method('isVariadic');
        $factoryReflection->expects($this->never())->method('getReturnType');
        $factoryReflection->expects($this->never())->method('getParameters');

        $fr = new ReflectionMethod($factoryReflection);

        $closureReflection = $this->createMock(BetterReflectionFunction::class);
        $closureReflection->expects($this->never())->method('getName');
        $closureReflection->expects($this->never())->method('getFileName');
        $closureReflection->method('getStartLine')->willReturn(12);
        $closureReflection->method('getEndLine')->willReturn(34);
        $closureReflection->expects($this->never())->method('getDocComment');
        $closureReflection->method('isVariadic')->willReturn(false);
        $closureReflection->method('returnsReference')->willReturn(false);

        $closureReflection->method('getParameters')->willReturn([]);

        $cr = new ReflectionFunction($closureReflection);

        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('getName')->willReturn(ProxyInterface::class);

        $rcOfDc = new ReflectionClass(ClassReflection::class);
        $dc = $rcOfDc->newInstanceWithoutConstructor();
        $rpr = $rcOfDc->getProperty('reflection');
        $rpr->setValue($dc, $brc);
        $rpr = $rcOfDc->getProperty('ancestors');
        $rpr->setValue($dc, []);
        $rpr = $rcOfDc->getProperty('methodsClassReflectionExtensions');
        $rpr->setValue($dc, [0 => null]);
        $rpr = $rcOfDc->getProperty('isGeneric');
        $rpr->setValue($dc, false);
        $rpr = $rcOfDc->getProperty('finalByKeywordOverride');
        $rpr->setValue($dc, false);
        $rpr = $rcOfDc->getProperty('anonymousFilename');
        $rpr->setValue($dc, null);
        $rpr = $rcOfDc->getProperty('acceptsNamedArguments');
        $rpr->setValue($dc, $acceptsNamedArguments);

        $this->getReflectionProviderMock()
            ->method('getClass')
            ->willReturn($dc);

        return new StateMethod(
            reflectionProvider: $this->getReflectionProviderMock(),
            initializerExprTypeResolver: $this->getInitializerExprTypeResolverMock(),
            attributeReflectionFactory: $this->getAttributeReflectionFactoryMock(),
            factoryReflection: $fr,
            closureReflection: $cr,
            declaringClass: $dc,
            phpDocReturnType: $phpDocReturnType,
            phpDocThrowType: $phpDocThrowType,
            selfOutType: $selfOutType,
            asserts: Assertions::createEmpty(),
            templateTypeMap: new TemplateTypeMap([]),
            isPure: $isPure,
            attributes: $attributes,
            acceptsNamedArguments: $acceptsNamedArguments,
        );
    }

    public function testGetName(): void
    {
        $this->assertSame('factory', $this->buildInstance()->getName());
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(ClassReflection::class, $this->buildInstance()->getDeclaringClass());
    }

    public function testGetDocComment(): void
    {
        $this->assertSame('factory doc', $this->buildInstance()->getDocComment());
    }

    public function testGetDocCommentNull(): void
    {
        $this->assertNotSame(1, ini_get('zend.assertions'));
        $this->assertEmpty($this->buildInstance(null)->getDocComment());
    }

    public function testIsStatic(): void
    {
        $this->assertFalse($this->buildInstance()->isStatic());
    }

    public function testIsPrivate(): void
    {
        $this->assertFalse($this->buildInstance()->isPrivate());
    }

    public function testIsPublic(): void
    {
        $this->assertFalse($this->buildInstance()->isPublic());
    }

    public function testGetPrototype(): void
    {
        $this->assertInstanceOf(StateMethod::class, $this->buildInstance()->getPrototype());
    }

    public function testGetPrototypeWithTentativeReturnType(): void
    {
        $type = $this->createMock(ReflectionNamedType::class);
        $type->expects($this->once())->method('getName')->willReturn(stdClass::class);
        $this->assertInstanceOf(StateMethod::class, $this->buildInstance(tentativeReturnType: $type)->getPrototype());
    }

    public function testIsDeprecated(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance()->isDeprecated());
        $this->assertEquals(TrinaryLogic::createYes(), $this->buildInstance(isDeprecated: true)->isDeprecated());
    }

    public function testIsFinal(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance()->isFinal());
    }

    public function testIsInternal(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance()->isInternal());
    }

    public function testIsAbstract(): void
    {
        $this->assertFalse($this->buildInstance()->isAbstract());
    }

    public function testIsVariadic(): void
    {
        $this->assertFalse($this->buildInstance()->isVariadic());
    }

    public function testReturnsByReference(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance()->returnsByReference());
    }

    public function testIsFinalByKeyword(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance()->isFinalByKeyword());
    }

    public function testGetDeprecatedDescription(): void
    {
        $this->assertNull($this->buildInstance()->getDeprecatedDescription());
    }

    public function testGetDeprecatedDescriptionWhenDeprecated(): void
    {
        $this->assertSame('foo bar', $this->buildInstance(isDeprecated: true)->getDeprecatedDescription());
    }

    public function testGetAttributes(): void
    {
        $this->assertSame([], $this->buildInstance()->getAttributes());
    }

    public function testGetThrowType(): void
    {
        $this->assertNotInstanceOf(\PHPStan\Type\Type::class, $this->buildInstance()->getThrowType());
    }

    public function testGetSelfOutType(): void
    {
        $this->assertNotInstanceOf(\PHPStan\Type\Type::class, $this->buildInstance()->getSelfOutType());
    }

    public function testGetAsserts(): void
    {
        $this->assertInstanceOf(Assertions::class, $this->buildInstance()->getAsserts());
    }

    public function testIsBuiltin(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance()->isBuiltin());
    }

    public function testGetNamedArgumentsVariants(): void
    {
        $this->assertNull($this->buildInstance()->getNamedArgumentsVariants());
    }

    public function testAcceptsNamedArguments(): void
    {
        $this->assertEquals(TrinaryLogic::createYes(), $this->buildInstance(acceptsNamedArguments: true)->acceptsNamedArguments());
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance(acceptsNamedArguments: false)->acceptsNamedArguments());
    }

    public function testIsPure(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance(isPure: false)->isPure());
        $this->assertEquals(TrinaryLogic::createMaybe(), $this->buildInstance(isPure: null)->isPure());
        $this->assertEquals(TrinaryLogic::createYes(), $this->buildInstance(isPure: true)->isPure());
    }

    public function testGetVariants(): void
    {
        $variants = $this->buildInstance()->getVariants();
        $this->assertIsArray($variants);
        $this->assertCount(1, $variants);
        $this->assertInstanceOf(ExtendedParametersAcceptor::class, $variants[0]);
    }

    public function testGetOnlyVariant(): void
    {
        $variant = $this->buildInstance()->getOnlyVariant();
        $this->assertInstanceOf(ExtendedParametersAcceptor::class, $variant);
    }

    public function testHasSideEffects(): void
    {
        $result = $this->buildInstance()->hasSideEffects();
        $this->assertInstanceOf(TrinaryLogic::class, $result);

        $result = $this->buildInstance(phpDocReturnType: new VoidType())->hasSideEffects();
        $this->assertEquals(TrinaryLogic::createYes(), $result);

        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('getName')->willReturn(ProxyInterface::class);

        $rcOfDc = new ReflectionClass(ClassReflection::class);
        $dc = $rcOfDc->newInstanceWithoutConstructor();
        $rpr = $rcOfDc->getProperty('reflection');
        $rpr->setValue($dc, $brc);
        $rpr = $rcOfDc->getProperty('ancestors');
        $rpr->setValue($dc, []);
        $rpr = $rcOfDc->getProperty('methodsClassReflectionExtensions');
        $rpr->setValue($dc, [0 => null]);
        $rpr = $rcOfDc->getProperty('isGeneric');
        $rpr->setValue($dc, false);
        $rpr = $rcOfDc->getProperty('finalByKeywordOverride');
        $rpr->setValue($dc, false);
        $rcOfDc->getProperty('acceptsNamedArguments');

        $result = $this->buildInstance(isPure: null, phpDocReturnType: new ThisType($dc))->hasSideEffects();
        $this->assertEquals(TrinaryLogic::createYes(), $result);

        $result = $this->buildInstance(isPure: null)->hasSideEffects();
        $this->assertEquals(TrinaryLogic::createMaybe(), $result);
    }

    public function testGetPrototypeException(): void
    {
        $result = $this->buildInstance()->getPrototype();
        $this->assertInstanceOf(StateMethod::class, $result);
    }

    public function testGetVariantWithPhpDocReturnType(): void
    {
        $variants = $this->buildInstance(phpDocReturnType: new StringType())->getVariants();
        $this->assertCount(1, $variants);
    }

    public function testWithThrowType(): void
    {
        $throwType = new StringType();

        $this->assertInstanceOf(StringType::class, $this->buildInstance(phpDocThrowType: $throwType)->getThrowType());
    }

    public function testWithSelfOutType(): void
    {
        $selfOutType = new StringType();

        $this->assertInstanceOf(StringType::class, $this->buildInstance(selfOutType: $selfOutType)->getSelfOutType());
    }

    public function testWithAttributes(): void
    {
        $attributes = [new AttributeReflection('foo', [])];

        $this->assertEquals($attributes, $this->buildInstance(attributes: $attributes)->getAttributes());
    }
}
