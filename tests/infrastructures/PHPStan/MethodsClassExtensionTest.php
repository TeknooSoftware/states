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

namespace Teknoo\Tests\States\PHPStan;

use DateTime;
use PHPStan\Analyser\NameScope;
use PHPStan\BetterReflection\Reflection\ReflectionClass as BetterReflectionClass;
use PHPStan\PhpDoc\PhpDocNodeResolver;
use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\PhpDoc\Tag\ReturnTag;
use PHPStan\PhpDoc\Tag\SelfOutTypeTag;
use PHPStan\PhpDoc\Tag\ThrowsTag;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\Generic\TemplateTypeVarianceMap;
use PHPStan\Type\Type;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use Teknoo\States\PHPStan\Contracts\Reflection\AttributeReflectionFactoryInterface;
use Teknoo\States\PHPStan\Contracts\Reflection\InitializerExprTypeResolverInterface;
use Teknoo\States\PHPStan\Contracts\Reflection\PhpDocInheritanceResolverInterface;
use Teknoo\States\PHPStan\MethodsClassExtension;
use Teknoo\States\PHPStan\Reflection\StateMethod;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;
use PHPUnit\Framework\TestCase;
use Teknoo\Tests\Support\Article\Article;
use Teknoo\Tests\Support\Article\Article\Draft;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(MethodsClassExtension::class)]
class MethodsClassExtensionTest extends TestCase
{
    private (ReflectionProvider&MockObject)|null $reflectionProvider = null;

    private (AttributeReflectionFactoryInterface&MockObject)|null $attributeReflectionFactory = null;

    private (InitializerExprTypeResolverInterface&MockObject)|null $initializerExprTypeResolver = null;

    private (PhpDocInheritanceResolverInterface&MockObject)|null $phpDocInheritanceResolver = null;

    private function getReflectionProviderMock(): ReflectionProvider&MockObject
    {
        if (!$this->reflectionProvider instanceof ReflectionProvider) {
            $this->reflectionProvider = $this->createMock(ReflectionProvider::class);
        }

        return $this->reflectionProvider;
    }

    private function getAttributeReflectionFactoryMock(): AttributeReflectionFactoryInterface&MockObject
    {
        if (!$this->attributeReflectionFactory instanceof AttributeReflectionFactoryInterface) {
            $this->attributeReflectionFactory = $this->createMock(AttributeReflectionFactoryInterface::class);
        }

        return $this->attributeReflectionFactory;
    }

    private function getInitializerExprTypeResolverMock(): InitializerExprTypeResolverInterface&MockObject
    {
        if (!$this->initializerExprTypeResolver instanceof InitializerExprTypeResolverInterface) {
            $this->initializerExprTypeResolver = $this->createMock(InitializerExprTypeResolverInterface::class);
        }

        return $this->initializerExprTypeResolver;
    }

    private function getPhpDocInheritanceResolverMock(): PhpDocInheritanceResolverInterface&MockObject
    {
        if (!$this->phpDocInheritanceResolver instanceof PhpDocInheritanceResolverInterface) {
            $this->phpDocInheritanceResolver = $this->createMock(PhpDocInheritanceResolverInterface::class);
        }

        return $this->phpDocInheritanceResolver;
    }

    protected function buildInstance(): MethodsClassReflectionExtension
    {
        return new MethodsClassExtension(
            reflectionProvider: $this->getReflectionProviderMock(),
            attributeReflectionFactory: $this->getAttributeReflectionFactoryMock(),
            initializerExprTypeResolver: $this->getInitializerExprTypeResolverMock(),
            phpDocInheritanceResolver: $this->getPhpDocInheritanceResolverMock(),
        );
    }

    public function testHasMethodIsInterface(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(true);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $instance = $this->buildInstance();
        $this->assertFalse($instance->hasMethod($cr, 'aMethodName'));
        $this->assertFalse($instance->hasMethod($cr, 'aMethodName'));
    }

    public function testHasMethodNotImplementProxyAndNotImplementState(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturn(false);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $instance = $this->buildInstance();
        $this->assertFalse($instance->hasMethod($cr, 'aMethodName'));
        $this->assertFalse($instance->hasMethod($cr, 'aMethodName'));
    }

    public function testHasMethodImplementProxyMethodInProxy(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn(Article::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $instance = $this->buildInstance();
        $this->assertFalse($instance->hasMethod($cr, 'getAttribute'));
        $this->assertFalse($instance->hasMethod($cr, 'getAttribute'));
    }

    public function testStatesListDeclaratoionReflectionError(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn(Article::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->assertFalse($this->buildInstance()->hasMethod($cr, 'aMethod'));
    }

    public function testHasMethodImplementProxyMethodInState(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn(Article::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $instance = $this->buildInstance();
        $this->assertTrue($instance->hasMethod($cr, 'getFormattedBody'));
        $this->assertTrue($instance->hasMethod($cr, 'getFormattedBody'));
    }

    public function testHasMethodImplementProxyMethodNotExist(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn(Article::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->assertFalse($this->buildInstance()->hasMethod($cr, 'aFakeMethodName'));
    }

    public function testHasMethodImplementStateProxyNotFound(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $brc->method('getName')->willReturn('Foo\Bar\Not\ExistClass');

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->assertFalse($this->buildInstance()->hasMethod($cr, 'aMethod'));
    }

    public function testHasMethodImplementStateMethodInState(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $brc->method('getName')->willReturn(Draft::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->assertTrue($this->buildInstance()->hasMethod($cr, 'getFormattedBody'));
    }

    public function testHasMethodImplementStateMethodInProxy(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $brc->method('getName')->willReturn(Draft::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->assertFalse($this->buildInstance()->hasMethod($cr, 'getAttribute'));
    }

    public function testHasMethodImplementStateMethodNotExist(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $brc->method('getName')->willReturn(Draft::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->assertFalse($this->buildInstance()->hasMethod($cr, 'aNonExistantMethod'));
    }

    public function testGetMethodIsInterface(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(true);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->buildInstance()->getMethod($cr, 'aMethod');
    }

    public function testGetMethodNotImplementProxyAndNotImplementState(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturn(false);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->buildInstance()->getMethod($cr, 'aMethod');
    }

    public function testGetMethodImplementProxyMethodInProxy(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn(Article::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->buildInstance()->getMethod($cr, 'getAttribute');
    }

    public function testGetMethodImplementProxyMethodInState(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn(Article::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $rpr = $rcOfCr->getProperty('anonymousFilename');
        $rpr->setValue($cr, 'foo/bar.php');

        $rcOfRPB = new ReflectionClass(ResolvedPhpDocBlock::class);
        $resolvedPHPDocBlock = $rcOfRPB->newInstanceWithoutConstructor();

        $rpr = $rcOfRPB->getProperty('templateTypeMap');
        $rpr->setValue($resolvedPHPDocBlock, new TemplateTypeMap([], []));

        $rpr = $rcOfRPB->getProperty('phpDocNodeResolver');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new ReflectionClass(PhpDocNodeResolver::class)->newInstanceWithoutConstructor()
        );

        $rpr = $rcOfRPB->getProperty('phpDocNode');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $this->createMock(PhpDocNode::class),
        );

        $rpr = $rcOfRPB->getProperty('nameScope');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new NameScope('foo', []),
        );

        $this->getPhpDocInheritanceResolverMock()
            ->method('resolvePhpDocForMethod')
            ->willReturn($resolvedPHPDocBlock);

        $this->assertInstanceOf(StateMethod::class, $this->buildInstance()->getMethod($cr, 'getFormattedBody'));
    }

    public function testGetMethodImplementProxyMethodInStateWithThrowTypeAndSelfOutTag(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn(Article::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $rpr = $rcOfCr->getProperty('anonymousFilename');
        $rpr->setValue($cr, 'foo/bar.php');

        $rcOfRPB = new ReflectionClass(ResolvedPhpDocBlock::class);
        $resolvedPHPDocBlock = $rcOfRPB->newInstanceWithoutConstructor();

        $rpr = $rcOfRPB->getProperty('templateTypeMap');
        $rpr->setValue($resolvedPHPDocBlock, new TemplateTypeMap([], []));

        $rpr = $rcOfRPB->getProperty('phpDocNodeResolver');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new ReflectionClass(PhpDocNodeResolver::class)->newInstanceWithoutConstructor()
        );

        $rpr = $rcOfRPB->getProperty('phpDocNode');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $this->createMock(PhpDocNode::class),
        );

        $rpr = $rcOfRPB->getProperty('nameScope');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new NameScope('foo', []),
        );

        $rcofval = new ReflectionClass(ThrowsTag::class);
        $throwTag = $rcofval->newInstanceWithoutConstructor();
        $rpr = $rcofval->getProperty('type');
        $rpr->setValue($throwTag, $this->createMock(Type::class));

        $rpr = $rcOfRPB->getProperty('throwsTag');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $throwTag,
        );


        $rcofval = new ReflectionClass(SelfOutTypeTag::class);
        $selfOutTag = $rcofval->newInstanceWithoutConstructor();
        $rpr = $rcofval->getProperty('type');
        $rpr->setValue($selfOutTag, $this->createMock(Type::class));

        $rpr = $rcOfRPB->getProperty('selfOutTypeTag');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $selfOutTag
        );

        $this->getPhpDocInheritanceResolverMock()
            ->method('resolvePhpDocForMethod')
            ->willReturn($resolvedPHPDocBlock);

        $this->assertInstanceOf(StateMethod::class, $this->buildInstance()->getMethod($cr, 'getFormattedBody'));
    }

    public function testGetMethodImplementProxyMethodInStateWithExplicitReturnTag(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn(Article::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $rpr = $rcOfCr->getProperty('anonymousFilename');
        $rpr->setValue($cr, 'foo/bar.php');

        $rpr = $rcOfCr->getProperty('activeTemplateTypeMap');
        $rpr->setValue($cr, new TemplateTypeMap([], []));

        $rpr = $rcOfCr->getProperty('callSiteVarianceMap');
        $rpr->setValue($cr, new TemplateTypeVarianceMap([]));

        $rcOfRPB = new ReflectionClass(ResolvedPhpDocBlock::class);
        $resolvedPHPDocBlock = $rcOfRPB->newInstanceWithoutConstructor();

        $rpr = $rcOfRPB->getProperty('templateTypeMap');
        $rpr->setValue($resolvedPHPDocBlock, new TemplateTypeMap([], []));

        $rpr = $rcOfRPB->getProperty('phpDocNodeResolver');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new ReflectionClass(PhpDocNodeResolver::class)->newInstanceWithoutConstructor()
        );

        $rpr = $rcOfRPB->getProperty('phpDocNode');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $this->createMock(PhpDocNode::class),
        );

        $rpr = $rcOfRPB->getProperty('nameScope');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new NameScope('foo', []),
        );

        $rcofval = new ReflectionClass(ReturnTag::class);
        $returnTag = $rcofval->newInstanceWithoutConstructor();
        $rpr = $rcofval->getProperty('type');
        $rpr->setValue($returnTag, $this->createMock(Type::class));

        $rpr = $rcofval->getProperty('isExplicit');
        $rpr->setValue($returnTag, true);

        $rpr = $rcOfRPB->getProperty('returnTag');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $returnTag,
        );

        $this->getPhpDocInheritanceResolverMock()
            ->method('resolvePhpDocForMethod')
            ->willReturn($resolvedPHPDocBlock);

        $this->assertInstanceOf(StateMethod::class, $this->buildInstance()->getMethod($cr, 'getFormattedBody'));
    }


    public function testGetMethodImplementProxyMethodInStateWithNonExplicitReturnTag(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn(Article::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $rpr = $rcOfCr->getProperty('anonymousFilename');
        $rpr->setValue($cr, 'foo/bar.php');

        $rpr = $rcOfCr->getProperty('activeTemplateTypeMap');
        $rpr->setValue($cr, new TemplateTypeMap([], []));

        $rpr = $rcOfCr->getProperty('callSiteVarianceMap');
        $rpr->setValue($cr, new TemplateTypeVarianceMap([]));

        $rcOfRPB = new ReflectionClass(ResolvedPhpDocBlock::class);
        $resolvedPHPDocBlock = $rcOfRPB->newInstanceWithoutConstructor();

        $rpr = $rcOfRPB->getProperty('templateTypeMap');
        $rpr->setValue($resolvedPHPDocBlock, new TemplateTypeMap([], []));

        $rpr = $rcOfRPB->getProperty('phpDocNodeResolver');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new ReflectionClass(PhpDocNodeResolver::class)->newInstanceWithoutConstructor()
        );

        $rpr = $rcOfRPB->getProperty('phpDocNode');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $this->createMock(PhpDocNode::class),
        );

        $rpr = $rcOfRPB->getProperty('nameScope');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new NameScope('foo', []),
        );

        $rcofval = new ReflectionClass(ReturnTag::class);
        $returnTag = $rcofval->newInstanceWithoutConstructor();
        $rpr = $rcofval->getProperty('type');
        $rpr->setValue($returnTag, $this->createMock(Type::class));

        $rpr = $rcofval->getProperty('isExplicit');
        $rpr->setValue($returnTag, false);

        $rpr = $rcOfRPB->getProperty('returnTag');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $returnTag,
        );

        $this->getPhpDocInheritanceResolverMock()
            ->method('resolvePhpDocForMethod')
            ->willReturn($resolvedPHPDocBlock);

        $this->assertInstanceOf(StateMethod::class, $this->buildInstance()->getMethod($cr, 'getFormattedBody'));
    }

    public function testGetMethodImplementProxyMethodNotExist(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn(Article::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->buildInstance()->getMethod($cr, 'notExistantMethod');
    }

    public function testGetMethodImplementProxyMethodClosureReturnedIsStatic(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn(Article::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->buildInstance()->getMethod($cr, 'returnStaticClosure');
    }

    public function testGetMethodImplementStateProxyNotFound(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $brc->method('getName')->willReturn('Foo\Bar\Not\ExistClass');

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->buildInstance()->getMethod($cr, 'aMethod');
    }

    public function testGetMethodImplementStateProxyNotImplement(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $brc->method('getName')->willReturn(DateTime::class.'\\Foo');

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->buildInstance()->getMethod($cr, 'aMethod');
    }

    public function testGetMethodImplementStateMethodInState(): void
    {
        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $brc->method('getName')->willReturn(Draft::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $rpr = $rcOfCr->getProperty('anonymousFilename');
        $rpr->setValue($cr, 'foo/bar.php');

        $rcOfRPB = new ReflectionClass(ResolvedPhpDocBlock::class);
        $resolvedPHPDocBlock = $rcOfRPB->newInstanceWithoutConstructor();

        $rpr = $rcOfRPB->getProperty('templateTypeMap');
        $rpr->setValue($resolvedPHPDocBlock, new TemplateTypeMap([], []));

        $rpr = $rcOfRPB->getProperty('phpDocNodeResolver');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new ReflectionClass(PhpDocNodeResolver::class)->newInstanceWithoutConstructor()
        );

        $rpr = $rcOfRPB->getProperty('phpDocNode');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $this->createMock(PhpDocNode::class),
        );

        $rpr = $rcOfRPB->getProperty('nameScope');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new NameScope('foo', []),
        );

        $this->getPhpDocInheritanceResolverMock()
            ->method('resolvePhpDocForMethod')
            ->willReturn($resolvedPHPDocBlock);

        $instance = $this->buildInstance();
        $this->assertInstanceOf(StateMethod::class, $instance->getMethod($cr, 'getFormattedBody'));
        //Check several time
        $this->assertInstanceOf(StateMethod::class, $instance->getMethod($cr, 'getFormattedBody'));
    }

    public function testGetMethodImplementStateMethodInProxy(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $brc->method('getName')->willReturn(Draft::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->buildInstance()->getMethod($cr, 'getAttribute');
    }

    public function testGetMethodImplementStateMethodNotExist(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $brc = $this->createMock(BetterReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, false],
            [StateInterface::class, true],
        ]);

        $brc->method('getName')->willReturn(Draft::class);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rpr = $rcOfCr->getProperty('reflection');
        $rpr->setValue($cr, $brc);

        $this->buildInstance()->getMethod($cr, 'notExistantMethod');
    }
}
