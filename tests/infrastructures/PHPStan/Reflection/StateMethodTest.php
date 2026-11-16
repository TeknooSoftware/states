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

use PHPStan\BetterReflection\Reflection\Adapter\ReflectionFunction;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionMethod;
use PHPStan\BetterReflection\Reflection\ReflectionAttribute;
use PHPStan\BetterReflection\Reflection\ReflectionClass as BetterReflectionClass;
use PHPStan\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;
use PHPStan\BetterReflection\Reflection\ReflectionMethod as BetterReflectionMethod;
use PHPStan\BetterReflection\Reflection\ReflectionNamedType;
use PHPStan\BetterReflection\Reflection\ReflectionParameter as BetterReflectionParameter;
use PHPStan\DependencyInjection\Reflection\ClassReflectionExtensionRegistryProvider;
use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\PhpDoc\Tag\DeprecatedTag;
use PHPStan\PhpDoc\Tag\ParamClosureThisTag;
use PHPStan\PhpDoc\Tag\ParamOutTag;
use PHPStan\PhpDoc\Tag\ParamTag;
use PHPStan\Reflection\Assertions;
use PHPStan\Reflection\AttributeReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ClassReflectionExtensionRegistry;
use PHPStan\Reflection\ClassReflectionFactory;
use PHPStan\Reflection\ExtendedParameterReflection;
use PHPStan\Reflection\ExtendedParametersAcceptor;
use PHPStan\Reflection\InitializerExprTypeResolver;
use PHPStan\Reflection\Php\PhpClassReflectionExtension;
use PHPStan\Reflection\Php\PhpParameterReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\Generic\TemplateTypeVarianceMap;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use PHPStan\Type\VoidType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use stdClass;
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
    private (ReflectionProvider&Stub)|null $reflectionProvider = null;

    private (AttributeReflectionFactoryInterface&Stub)|null $attributeReflectionFactory = null;

    private (InitializerExprTypeResolverInterface&Stub)|null $initializerExprTypeResolver = null;

    /**
     * @throws Exception
     */
    private function getReflectionProviderStub(): ReflectionProvider&Stub
    {
        if (!$this->reflectionProvider instanceof ReflectionProvider) {
            $this->reflectionProvider = $this->createStub(ReflectionProvider::class);
        }

        return $this->reflectionProvider;
    }

    /**
     * @throws Exception
     */
    private function getAttributeReflectionFactoryStub(): AttributeReflectionFactoryInterface&Stub
    {
        if (!$this->attributeReflectionFactory instanceof AttributeReflectionFactoryInterface) {
            $this->attributeReflectionFactory = $this->createStub(AttributeReflectionFactoryInterface::class);
        }

        return $this->attributeReflectionFactory;
    }

    /**
     * @throws Exception
     */
    private function getInitializerExprTypeResolverStub(): InitializerExprTypeResolverInterface&Stub
    {
        if (!$this->initializerExprTypeResolver instanceof InitializerExprTypeResolverInterface) {
            $this->initializerExprTypeResolver = $this->createStub(InitializerExprTypeResolverInterface::class);
        }

        return $this->initializerExprTypeResolver;
    }

    /**
     * @param array<string, mixed> $values values of the phpdoc's properties, others are empty
     */
    private function buildPhpDoc(array $values): ResolvedPhpDocBlock
    {
        $phpDoc = ResolvedPhpDocBlock::createEmpty();
        foreach ($values as $property => $value) {
            new ReflectionProperty($phpDoc, $property)->setValue($phpDoc, $value);
        }

        return $phpDoc;
    }

    /**
     * @param list<AttributeReflection> $attributes
     * @param list<BetterReflectionParameter> $closureParameters
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
        array $closureParameters = [],
        ?InitializerExprTypeResolver $initializerExprTypeResolver = null,
        ?ResolvedPhpDocBlock $resolvedPhpDoc = null,
        bool $isFinal = false,
        ?ClassReflection $declaringClass = null,
    ): StateMethod {
        $factoryReflection = $this->createMock(BetterReflectionMethod::class);
        $factoryReflection->modifiers = 0;
        if ($isFinal) {
            $factoryReflection->modifiers = \ReflectionMethod::IS_FINAL;
        }

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

        $attribute = $this->createStub(ReflectionAttribute::class);
        $attribute->name = 'Deprecated';
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

        $closureReflection->method('getParameters')->willReturn($closureParameters);

        $cr = new ReflectionFunction($closureReflection);

        $brc = $this->createStub(ReflectionClass::class);
        $brc->method('getName')->willReturn(ProxyInterface::class);

        $rcPfPcre = new ReflectionClass(PhpClassReflectionExtension::class);
        $pcre = $rcPfPcre->newInstanceWithoutConstructor();

        $rcOfDc = new ReflectionClass(ClassReflection::class);
        $dc = $rcOfDc->newInstanceWithoutConstructor();
        $rpr = $rcOfDc->getProperty('reflection');
        $rpr->setValue($dc, $brc);
        $rpr = $rcOfDc->getProperty('classReflectionExtensionRegistryProvider');
        $providerMock = $this->createStub(ClassReflectionExtensionRegistryProvider::class);
        $registryReflection = new ReflectionClass(ClassReflectionExtensionRegistry::class);
        $registry = $registryReflection->newInstanceWithoutConstructor();
        $phpClassReflectionExtensionProp = $registryReflection->getProperty('phpClassReflectionExtension');
        $phpClassReflectionExtensionProp->setValue($registry, $pcre);
        $providerMock->method('getRegistry')->willReturn($registry);
        $rpr->setValue($dc, $providerMock);
        $rpr = $rcOfDc->getProperty('ancestors');
        $rpr->setValue($dc, []);
        $rpr = $rcOfDc->getProperty('isGeneric');
        $rpr->setValue($dc, false);
        $rpr = $rcOfDc->getProperty('finalByKeywordOverride');
        $rpr->setValue($dc, null);
        $rpr = $rcOfDc->getProperty('anonymousFilename');
        $rpr->setValue($dc, null);
        $rpr = $rcOfDc->getProperty('acceptsNamedArguments');
        $rpr->setValue($dc, $acceptsNamedArguments);
        $rpr = $rcOfDc->getProperty('activeTemplateTypeMap');
        $rpr->setValue($dc, TemplateTypeMap::createEmpty());
        $rpr = $rcOfDc->getProperty('callSiteVarianceMap');
        $rpr->setValue($dc, TemplateTypeVarianceMap::createEmpty());

        $this->getReflectionProviderStub()
            ->method('getClass')
            ->willReturn($dc);

        return new StateMethod(
            reflectionProvider: $this->getReflectionProviderStub(),
            initializerExprTypeResolver: $initializerExprTypeResolver ?? $this->getInitializerExprTypeResolverStub(),
            attributeReflectionFactory: $this->getAttributeReflectionFactoryStub(),
            factoryReflection: $fr,
            closureReflection: $cr,
            declaringClass: $declaringClass ?? $dc,
            phpDocReturnType: $phpDocReturnType,
            phpDocThrowType: $phpDocThrowType,
            selfOutType: $selfOutType,
            asserts: Assertions::createEmpty(),
            templateTypeMap: new TemplateTypeMap([]),
            isPure: $isPure,
            attributes: $attributes,
            acceptsNamedArguments: $acceptsNamedArguments,
            resolvedPhpDoc: $resolvedPhpDoc,
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
        $type->name = stdClass::class;
        $type->isIdentifier = false;
        $type->expects($this->once())->method('getName')->willReturn(stdClass::class);
        $this->assertInstanceOf(StateMethod::class, $this->buildInstance(tentativeReturnType: $type)->getPrototype());
    }

    public function testIsDeprecated(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance()->isDeprecated());
        $this->assertEquals(TrinaryLogic::createYes(), $this->buildInstance(isDeprecated: true)->isDeprecated());
    }

    public function testIsDeprecatedFromPhpDoc(): void
    {
        $phpDoc = $this->buildPhpDoc(['isDeprecated' => true, 'deprecatedTag' => new DeprecatedTag('use bar')]);

        $method = $this->buildInstance(resolvedPhpDoc: $phpDoc);
        $this->assertEquals(TrinaryLogic::createYes(), $method->isDeprecated());
        $this->assertSame('use bar', $method->getDeprecatedDescription());
    }

    public function testIsFinal(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance()->isFinal());
        $this->assertEquals(TrinaryLogic::createYes(), $this->buildInstance(isFinal: true)->isFinal());
        $this->assertEquals(
            TrinaryLogic::createYes(),
            $this->buildInstance(resolvedPhpDoc: $this->buildPhpDoc(['isFinal' => true]))->isFinal()
        );
    }

    public function testIsInternal(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance()->isInternal());
        $this->assertEquals(
            TrinaryLogic::createYes(),
            $this->buildInstance(resolvedPhpDoc: $this->buildPhpDoc(['isInternal' => true]))->isInternal()
        );
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
        $this->assertEquals(TrinaryLogic::createYes(), $this->buildInstance(isFinal: true)->isFinalByKeyword());
        $this->assertEquals(
            TrinaryLogic::createNo(),
            $this->buildInstance(resolvedPhpDoc: $this->buildPhpDoc(['isFinal' => true]))->isFinalByKeyword()
        );
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
        $this->assertNotInstanceOf(Type::class, $this->buildInstance()->getThrowType());
    }

    public function testGetSelfOutType(): void
    {
        $this->assertNotInstanceOf(Type::class, $this->buildInstance()->getSelfOutType());
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

    public function testGetPureUnlessCallableIsImpureParameters(): void
    {
        $this->assertSame([], $this->buildInstance()->getPureUnlessCallableIsImpureParameters());

        $phpDoc = $this->buildPhpDoc(['paramsPureUnlessCallableIsImpure' => ['callback' => true, 'other' => false]]);
        $this->assertEquals(
            ['callback' => TrinaryLogic::createYes(), 'other' => TrinaryLogic::createNo()],
            $this->buildInstance(resolvedPhpDoc: $phpDoc)->getPureUnlessCallableIsImpureParameters()
        );
    }

    public function testGetPureUnlessParameterPassedParameters(): void
    {
        $this->assertSame([], $this->buildInstance()->getPureUnlessParameterPassedParameters());

        $phpDoc = $this->buildPhpDoc(['paramsPureUnlessParameterPassed' => ['flag' => true]]);
        $this->assertEquals(
            ['flag' => TrinaryLogic::createYes()],
            $this->buildInstance(resolvedPhpDoc: $phpDoc)->getPureUnlessParameterPassedParameters()
        );
    }

    /**
     * The builder's phpdoc documents the closure : its tags about parameters are applied to the closure's parameters.
     */
    public function testParametersFromPhpDoc(): void
    {
        $declaringFunction = $this->createStub(BetterReflectionFunction::class);
        $declaringFunction->method('getName')->willReturn('{closure}');
        $declaringFunction->method('getFileName')->willReturn('closure.php');

        $buildParameter = function (string $name) use ($declaringFunction): BetterReflectionParameter {
            $parameter = $this->createStub(BetterReflectionParameter::class);
            $parameter->function = $declaringFunction;
            $parameter->attributes = [];
            $parameter->name = $name;

            return $parameter;
        };

        $phpDoc = $this->buildPhpDoc([
            'paramTags' => ['documented' => new ParamTag(new IntegerType(), false)],
            'paramOutTags' => ['documented' => new ParamOutTag(new StringType())],
            'paramsImmediatelyInvokedCallable' => ['documented' => true],
            'paramClosureThisTags' => ['documented' => new ParamClosureThisTag(new ObjectType(stdClass::class))],
            'paramsPureUnlessCallableIsImpure' => ['documented' => true],
            'paramsPureUnlessParameterPassed' => ['documented' => true],
        ]);

        // PhpParameterReflection requires the final class InitializerExprTypeResolver, not the contract
        $resolver = new ReflectionClass(InitializerExprTypeResolver::class)->newInstanceWithoutConstructor();

        [$documented, $undocumented] = $this->buildInstance(
            closureParameters: [$buildParameter('documented'), $buildParameter('undocumented')],
            initializerExprTypeResolver: $resolver,
            resolvedPhpDoc: $phpDoc,
        )->getOnlyVariant()->getParameters();

        $this->assertInstanceOf(PhpParameterReflection::class, $documented);
        $this->assertInstanceOf(IntegerType::class, $documented->getPhpDocType());
        $this->assertInstanceOf(StringType::class, $documented->getOutType());
        $this->assertEquals(TrinaryLogic::createYes(), $documented->isImmediatelyInvokedCallable());
        $this->assertInstanceOf(ObjectType::class, $documented->getClosureThisType());
        $this->assertEquals(TrinaryLogic::createYes(), $documented->isPureUnlessCallableIsImpureParameter());
        $this->assertEquals(TrinaryLogic::createYes(), $documented->isPureUnlessParameterPassedParameter());

        $this->assertInstanceOf(PhpParameterReflection::class, $undocumented);
        $this->assertInstanceOf(MixedType::class, $undocumented->getPhpDocType());
        $this->assertNull($undocumented->getOutType());
        $this->assertEquals(TrinaryLogic::createMaybe(), $undocumented->isImmediatelyInvokedCallable());
        $this->assertNull($undocumented->getClosureThisType());
        $this->assertEquals(TrinaryLogic::createNo(), $undocumented->isPureUnlessCallableIsImpureParameter());
        $this->assertEquals(TrinaryLogic::createNo(), $undocumented->isPureUnlessParameterPassedParameter());
    }

    public function testParametersAreNotConditionallyPure(): void
    {
        $declaringFunction = $this->createStub(BetterReflectionFunction::class);
        $declaringFunction->method('getName')->willReturn('{closure}');
        $declaringFunction->method('getFileName')->willReturn('closure.php');

        $parameter = $this->createStub(BetterReflectionParameter::class);
        $parameter->function = $declaringFunction;
        $parameter->attributes = [];
        $parameter->name = 'value';

        // PhpParameterReflection requires the final class InitializerExprTypeResolver, not the contract
        $resolver = new ReflectionClass(InitializerExprTypeResolver::class)->newInstanceWithoutConstructor();

        $parameters = $this->buildInstance(closureParameters: [$parameter], initializerExprTypeResolver: $resolver)
            ->getOnlyVariant()
            ->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertInstanceOf(ExtendedParameterReflection::class, $parameters[0]);
        $this->assertEquals(TrinaryLogic::createNo(), $parameters[0]->isPureUnlessCallableIsImpureParameter());
        $this->assertEquals(TrinaryLogic::createNo(), $parameters[0]->isPureUnlessParameterPassedParameter());
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

        $brc = $this->createStub(ReflectionClass::class);
        $brc->method('getName')->willReturn(ProxyInterface::class);

        $rcOfDc = new ReflectionClass(ClassReflection::class);
        $dc = $rcOfDc->newInstanceWithoutConstructor();
        $rpr = $rcOfDc->getProperty('reflection');
        $rpr->setValue($dc, $brc);
        $rpr = $rcOfDc->getProperty('ancestors');
        $rpr->setValue($dc, []);
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

    /**
     * A pure method (`@phpstan-pure`) has no side effect, an impure method (`@phpstan-impure`) has side effects.
     */
    public function testHasSideEffectsOfPureAndImpureMethods(): void
    {
        $this->assertEquals(TrinaryLogic::createNo(), $this->buildInstance(isPure: true)->hasSideEffects());
        $this->assertEquals(TrinaryLogic::createYes(), $this->buildInstance(isPure: false)->hasSideEffects());
    }

    /**
     * Like PHPStan does for its own methods, a method returning `$this` has side effects, also when it is called on a
     * new instance (`new Foo()->bar()`) : the reflection of its class is then final by keyword override.
     */
    public function testHasSideEffectsOfAMethodReturningThisCalledOnANewInstance(): void
    {
        $brc = $this->createStub(ReflectionClass::class);
        $brc->method('getName')->willReturn(ProxyInterface::class);

        $rcOfDc = new ReflectionClass(ClassReflection::class);

        $declaredClass = $rcOfDc->newInstanceWithoutConstructor();
        $rcOfDc->getProperty('reflection')->setValue($declaredClass, $brc);
        $rcOfDc->getProperty('ancestors')->setValue($declaredClass, []);
        $rcOfDc->getProperty('isGeneric')->setValue($declaredClass, false);
        $rcOfDc->getProperty('finalByKeywordOverride')->setValue($declaredClass, null);
        $rcOfDc->getProperty('activeTemplateTypeMap')->setValue($declaredClass, TemplateTypeMap::createEmpty());
        $rcOfDc->getProperty('callSiteVarianceMap')->setValue($declaredClass, TemplateTypeVarianceMap::createEmpty());

        $classReflectionFactory = $this->createMock(ClassReflectionFactory::class);
        $classReflectionFactory->expects($this->once())
            ->method('create')
            ->with(ProxyInterface::class, $brc, null, null, null, null, null, null)
            ->willReturn($declaredClass);

        $newInstanceClass = $rcOfDc->newInstanceWithoutConstructor();
        $rcOfDc->getProperty('classReflectionFactory')->setValue($newInstanceClass, $classReflectionFactory);
        $rcOfDc->getProperty('displayName')->setValue($newInstanceClass, ProxyInterface::class);
        $rcOfDc->getProperty('reflection')->setValue($newInstanceClass, $brc);
        $rcOfDc->getProperty('anonymousFilename')->setValue($newInstanceClass, null);
        $rcOfDc->getProperty('resolvedTemplateTypeMap')->setValue($newInstanceClass, null);
        $rcOfDc->getProperty('stubPhpDocBlockCallback')->setValue($newInstanceClass, null);
        $rcOfDc->getProperty('resolvedCallSiteVarianceMap')->setValue($newInstanceClass, null);
        $rcOfDc->getProperty('ancestors')->setValue($newInstanceClass, []);
        $rcOfDc->getProperty('isGeneric')->setValue($newInstanceClass, false);
        $rcOfDc->getProperty('finalByKeywordOverride')->setValue($newInstanceClass, true);
        $rcOfDc->getProperty('activeTemplateTypeMap')->setValue($newInstanceClass, TemplateTypeMap::createEmpty());
        $rcOfDc->getProperty('callSiteVarianceMap')->setValue($newInstanceClass, TemplateTypeVarianceMap::createEmpty());

        $result = $this->buildInstance(
            isPure: null,
            phpDocReturnType: new ThisType($declaredClass),
            declaringClass: $newInstanceClass,
        )->hasSideEffects();

        $this->assertEquals(TrinaryLogic::createYes(), $result);
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

    public function testMustUseReturnValueWithNoAttributes(): void
    {
        $result = $this->buildInstance(attributes: [])->mustUseReturnValue();
        $this->assertEquals(TrinaryLogic::createNo(), $result);
    }

    public function testMustUseReturnValueWithNoDiscardAttribute(): void
    {
        $attributes = [
            new AttributeReflection('SomeOtherAttribute', []),
            new AttributeReflection('AnotherAttribute', [])
        ];

        $result = $this->buildInstance(attributes: $attributes)->mustUseReturnValue();
        $this->assertEquals(TrinaryLogic::createNo(), $result);
    }

    public function testMustUseReturnValueWithNoDiscardAttributeLowerCase(): void
    {
        $attributes = [new AttributeReflection('nodiscard', [])];

        $result = $this->buildInstance(attributes: $attributes)->mustUseReturnValue();
        $this->assertEquals(TrinaryLogic::createYes(), $result);
    }

    public function testMustUseReturnValueWithNoDiscardAttributeUpperCase(): void
    {
        $attributes = [new AttributeReflection('NODISCARD', [])];

        $result = $this->buildInstance(attributes: $attributes)->mustUseReturnValue();
        $this->assertEquals(TrinaryLogic::createYes(), $result);
    }

    public function testMustUseReturnValueWithNoDiscardAttributeMixedCase(): void
    {
        $attributes = [new AttributeReflection('NoDiscard', [])];

        $result = $this->buildInstance(attributes: $attributes)->mustUseReturnValue();
        $this->assertEquals(TrinaryLogic::createYes(), $result);
    }

    public function testMustUseReturnValueWithMultipleAttributesIncludingNoDiscard(): void
    {
        $attributes = [
            new AttributeReflection('SomeAttribute', []),
            new AttributeReflection('nodiscard', []),
            new AttributeReflection('AnotherAttribute', [])
        ];

        $result = $this->buildInstance(attributes: $attributes)->mustUseReturnValue();
        $this->assertEquals(TrinaryLogic::createYes(), $result);
    }

    public function testGetResolvedPhpDoc(): void
    {
        $this->assertNull($this->buildInstance()->getResolvedPhpDoc());

        $phpDoc = $this->buildPhpDoc([]);
        $this->assertSame($phpDoc, $this->buildInstance(resolvedPhpDoc: $phpDoc)->getResolvedPhpDoc());
    }
}
