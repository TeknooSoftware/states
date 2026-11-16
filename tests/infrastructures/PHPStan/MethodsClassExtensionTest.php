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
use PHPStan\BetterReflection\Reflection\ReflectionMethod as BetterReflectionMethod;
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
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\Generic\TemplateTypeVarianceMap;
use PHPStan\Type\Type;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use ReflectionClass;
use Teknoo\States\PHPStan\Contracts\Reflection\AttributeReflectionFactoryInterface;
use Teknoo\States\PHPStan\Contracts\Reflection\FileTypeMapperInterface;
use Teknoo\States\PHPStan\Contracts\Reflection\InitializerExprTypeResolverInterface;
use Teknoo\States\PHPStan\Contracts\Reflection\PhpDocInheritanceResolverInterface;
use Teknoo\States\PHPStan\MethodsClassExtension;
use Teknoo\States\PHPStan\Reflection\StateMethod;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;
use PHPUnit\Framework\TestCase;
use Teknoo\Tests\Support\Article\Article;
use Teknoo\Tests\Support\Article\Article\Draft;
use Teknoo\Tests\Support\Article\Article\StateDefault;
use Teknoo\Tests\Support\Greeter\Greeter;
use Teknoo\Tests\Support\Greeter\States\GreetingTrait;
use Teknoo\Tests\Support\Greeter\States\Polite;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(MethodsClassExtension::class)]
class MethodsClassExtensionTest extends TestCase
{
    private (ReflectionProvider&Stub)|null $reflectionProvider = null;

    private (AttributeReflectionFactoryInterface&Stub)|null $attributeReflectionFactory = null;

    private (InitializerExprTypeResolverInterface&Stub)|null $initializerExprTypeResolver = null;

    private (PhpDocInheritanceResolverInterface&Stub)|null $phpDocInheritanceResolver = null;

    private (FileTypeMapperInterface&Stub)|null $fileTypeMapper = null;

    private function getReflectionProviderStub(): ReflectionProvider&Stub
    {
        if (!$this->reflectionProvider instanceof ReflectionProvider) {
            $this->reflectionProvider = $this->createStub(ReflectionProvider::class);
        }

        return $this->reflectionProvider;
    }

    private function getAttributeReflectionFactoryStub(): AttributeReflectionFactoryInterface&Stub
    {
        if (!$this->attributeReflectionFactory instanceof AttributeReflectionFactoryInterface) {
            $this->attributeReflectionFactory = $this->createStub(AttributeReflectionFactoryInterface::class);
        }

        return $this->attributeReflectionFactory;
    }

    private function getInitializerExprTypeResolverStub(): InitializerExprTypeResolverInterface&Stub
    {
        if (!$this->initializerExprTypeResolver instanceof InitializerExprTypeResolverInterface) {
            $this->initializerExprTypeResolver = $this->createStub(InitializerExprTypeResolverInterface::class);
        }

        return $this->initializerExprTypeResolver;
    }

    private function getPhpDocInheritanceResolverStub(): PhpDocInheritanceResolverInterface&Stub
    {
        if (!$this->phpDocInheritanceResolver instanceof PhpDocInheritanceResolverInterface) {
            $this->phpDocInheritanceResolver = $this->createStub(PhpDocInheritanceResolverInterface::class);
        }

        return $this->phpDocInheritanceResolver;
    }

    private function getFileTypeMapperStub(): FileTypeMapperInterface&Stub
    {
        if (!$this->fileTypeMapper instanceof FileTypeMapperInterface) {
            $this->fileTypeMapper = $this->createStub(FileTypeMapperInterface::class);
        }

        return $this->fileTypeMapper;
    }

    protected function buildInstance(): MethodsClassReflectionExtension
    {
        return new MethodsClassExtension(
            reflectionProvider: $this->getReflectionProviderStub(),
            attributeReflectionFactory: $this->getAttributeReflectionFactoryStub(),
            initializerExprTypeResolver: $this->getInitializerExprTypeResolverStub(),
            phpDocInheritanceResolver: $this->getPhpDocInheritanceResolverStub(),
            fileTypeMapper: $this->getFileTypeMapperStub(),
        );
    }

    public function testHasMethodIsInterface(): void
    {
        $brc = $this->createStub(ReflectionClass::class);
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
        $brc = $this->createStub(ReflectionClass::class);
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
        $brc = $this->createStub(ReflectionClass::class);
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
        $brc = $this->createStub(ReflectionClass::class);
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
        $brc = $this->createStub(ReflectionClass::class);
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
        $brc = $this->createStub(ReflectionClass::class);
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
        $brc = $this->createStub(ReflectionClass::class);
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
        $brc = $this->createStub(ReflectionClass::class);
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
        $brc = $this->createStub(ReflectionClass::class);
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
        $brc = $this->createStub(ReflectionClass::class);
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

        $brc = $this->createStub(ReflectionClass::class);
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

        $brc = $this->createStub(ReflectionClass::class);
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

        $brc = $this->createStub(ReflectionClass::class);
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
        $brc = $this->createStub(ReflectionClass::class);
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
            $this->createStub(PhpDocNode::class),
        );

        $rpr = $rcOfRPB->getProperty('nameScope');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new NameScope('foo', []),
        );

        $this->getPhpDocInheritanceResolverStub()
            ->method('resolvePhpDocForMethod')
            ->willReturn($resolvedPHPDocBlock);

        $this->assertInstanceOf(StateMethod::class, $this->buildInstance()->getMethod($cr, 'getFormattedBody'));
    }

    public function testGetMethodImplementProxyMethodInStateWithThrowTypeAndSelfOutTag(): void
    {
        $brc = $this->createStub(ReflectionClass::class);
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
            $this->createStub(PhpDocNode::class),
        );

        $rpr = $rcOfRPB->getProperty('nameScope');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new NameScope('foo', []),
        );

        $rcofval = new ReflectionClass(ThrowsTag::class);
        $throwTag = $rcofval->newInstanceWithoutConstructor();
        $rpr = $rcofval->getProperty('type');
        $rpr->setValue($throwTag, $this->createStub(Type::class));

        $rpr = $rcOfRPB->getProperty('throwsTag');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $throwTag,
        );


        $rcofval = new ReflectionClass(SelfOutTypeTag::class);
        $selfOutTag = $rcofval->newInstanceWithoutConstructor();
        $rpr = $rcofval->getProperty('type');
        $rpr->setValue($selfOutTag, $this->createStub(Type::class));

        $rpr = $rcOfRPB->getProperty('selfOutTypeTag');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $selfOutTag
        );

        $this->getPhpDocInheritanceResolverStub()
            ->method('resolvePhpDocForMethod')
            ->willReturn($resolvedPHPDocBlock);

        $this->assertInstanceOf(StateMethod::class, $this->buildInstance()->getMethod($cr, 'getFormattedBody'));
    }

    public function testGetMethodImplementProxyMethodInStateWithExplicitReturnTag(): void
    {
        $brc = $this->createStub(ReflectionClass::class);
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
            $this->createStub(PhpDocNode::class),
        );

        $rpr = $rcOfRPB->getProperty('nameScope');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new NameScope('foo', []),
        );

        $rcofval = new ReflectionClass(ReturnTag::class);
        $returnTag = $rcofval->newInstanceWithoutConstructor();
        $rpr = $rcofval->getProperty('type');
        $rpr->setValue($returnTag, $this->createStub(Type::class));

        $rpr = $rcofval->getProperty('isExplicit');
        $rpr->setValue($returnTag, true);

        $rpr = $rcOfRPB->getProperty('returnTag');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $returnTag,
        );

        $this->getPhpDocInheritanceResolverStub()
            ->method('resolvePhpDocForMethod')
            ->willReturn($resolvedPHPDocBlock);

        $this->assertInstanceOf(StateMethod::class, $this->buildInstance()->getMethod($cr, 'getFormattedBody'));
    }


    public function testGetMethodImplementProxyMethodInStateWithNonExplicitReturnTag(): void
    {
        $brc = $this->createStub(ReflectionClass::class);
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
            $this->createStub(PhpDocNode::class),
        );

        $rpr = $rcOfRPB->getProperty('nameScope');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new NameScope('foo', []),
        );

        $rcofval = new ReflectionClass(ReturnTag::class);
        $returnTag = $rcofval->newInstanceWithoutConstructor();
        $rpr = $rcofval->getProperty('type');
        $rpr->setValue($returnTag, $this->createStub(Type::class));

        $rpr = $rcofval->getProperty('isExplicit');
        $rpr->setValue($returnTag, false);

        $rpr = $rcOfRPB->getProperty('returnTag');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $returnTag,
        );

        $this->getPhpDocInheritanceResolverStub()
            ->method('resolvePhpDocForMethod')
            ->willReturn($resolvedPHPDocBlock);

        $this->assertInstanceOf(StateMethod::class, $this->buildInstance()->getMethod($cr, 'getFormattedBody'));
    }

    public function testGetMethodImplementProxyMethodNotExist(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $brc = $this->createStub(ReflectionClass::class);
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

    /**
     * A builder can return a static closure : it can not be bound to an instance of the proxy, only to its scope.
     * It stays a method of the stated class, called from an instance.
     */
    public function testGetMethodImplementProxyMethodClosureReturnedIsStatic(): void
    {
        $brc = $this->createStub(ReflectionClass::class);
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

        $this->getPhpDocInheritanceResolverStub()
            ->method('resolvePhpDocForMethod')
            ->willReturn(null);

        $method = $this->buildInstance()->getMethod($cr, 'returnStaticClosure');

        $this->assertInstanceOf(StateMethod::class, $method);
        $this->assertSame('returnStaticClosure', $method->getName());
        $this->assertFalse($method->isStatic());
    }

    public function testGetMethodImplementStateProxyNotFound(): void
    {
        $this->expectException(ShouldNotHappenException::class);

        $brc = $this->createStub(ReflectionClass::class);
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

        $brc = $this->createStub(ReflectionClass::class);
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
        $brc = $this->createStub(ReflectionClass::class);
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
            $this->createStub(PhpDocNode::class),
        );

        $rpr = $rcOfRPB->getProperty('nameScope');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new NameScope('foo', []),
        );

        $this->getPhpDocInheritanceResolverStub()
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

        $brc = $this->createStub(ReflectionClass::class);
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

        $brc = $this->createStub(ReflectionClass::class);
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

    public function testGetMethodImplementProxyMethodInStateWithNullResolvedPhpDoc(): void
    {
        $brc = $this->createStub(ReflectionClass::class);
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

        $this->getPhpDocInheritanceResolverStub()
            ->method('resolvePhpDocForMethod')
            ->willReturn(null);

        $this->assertInstanceOf(StateMethod::class, $this->buildInstance()->getMethod($cr, 'getFormattedBody'));
    }

    /**
     * @param class-string $className
     */
    private function buildProxyClassReflection(string $className = Article::class): ClassReflection
    {
        $brc = $this->createStub(ReflectionClass::class);
        $brc->method('isInterface')->willReturn(false);
        $brc->method('implementsInterface')->willReturnMap([
            [ProxyInterface::class, true],
            [StateInterface::class, false],
        ]);
        $brc->method('getName')->willReturn($className);

        $rcOfCr = new ReflectionClass(ClassReflection::class);
        $cr = $rcOfCr->newInstanceWithoutConstructor();
        $rcOfCr->getProperty('reflection')->setValue($cr, $brc);
        $rcOfCr->getProperty('anonymousFilename')->setValue($cr, 'foo/bar.php');
        $rcOfCr->getProperty('isGeneric')->setValue($cr, false);
        $rcOfCr->getProperty('finalByKeywordOverride')->setValue($cr, null);

        return $cr;
    }

    /**
     * The phpdoc of the builder documents the closure : it is resolved in the scope of the state's file, and merged
     * with the phpdoc inherited by the stated class.
     */
    public function testGetMethodResolvesThePhpDocOfTheBuilder(): void
    {
        $cr = $this->buildProxyClassReflection();

        $builderPhpDoc = ResolvedPhpDocBlock::createEmpty();
        $resolvedPhpDoc = ResolvedPhpDocBlock::createEmpty();
        $nativeBuilder = new \ReflectionMethod(StateDefault::class, 'isPublished');
        //Like PHPStan, the phpdoc read by BetterReflection, reformatted
        $docComment = BetterReflectionMethod::createFromInstance(
            new ReflectionClass(StateDefault::class)->newInstanceWithoutConstructor(),
            'isPublished',
        )->getDocComment();

        $fileTypeMapper = $this->createMock(FileTypeMapperInterface::class);
        $fileTypeMapper->expects($this->once())
            ->method('getResolvedPhpDoc')
            ->with(
                $this->callback(
                    static fn (?string $file): bool => realpath((string) $file) === $nativeBuilder->getFileName()
                ),
                StateDefault::class,
                null,
                'isPublished',
                $docComment,
            )
            ->willReturn($builderPhpDoc);
        $this->fileTypeMapper = $fileTypeMapper;

        $phpDocInheritanceResolver = $this->createMock(PhpDocInheritanceResolverInterface::class);
        $phpDocInheritanceResolver->expects($this->once())
            ->method('resolvePhpDocForMethod')
            ->with($cr, 'isPublished', $builderPhpDoc, [])
            ->willReturn($resolvedPhpDoc);
        $this->phpDocInheritanceResolver = $phpDocInheritanceResolver;

        $method = $this->buildInstance()->getMethod($cr, 'isPublished');

        $this->assertInstanceOf(StateMethod::class, $method);
        $this->assertSame($resolvedPhpDoc, $method->getResolvedPhpDoc());
    }

    /**
     * Like PHPStan does for methods, the phpdoc of a builder provided by a trait is resolved in the scope of the trait.
     */
    public function testGetMethodResolvesThePhpDocOfABuilderFromATrait(): void
    {
        $cr = $this->buildProxyClassReflection(Greeter::class);

        $builderPhpDoc = ResolvedPhpDocBlock::createEmpty();
        //Like PHPStan, in the file of the class using the trait, not in the trait's file
        $stateFile = new ReflectionClass(Polite::class)->getFileName();
        $docComment = BetterReflectionMethod::createFromInstance(
            new ReflectionClass(Polite::class)->newInstanceWithoutConstructor(),
            'greet',
        )->getDocComment();

        $fileTypeMapper = $this->createMock(FileTypeMapperInterface::class);
        $fileTypeMapper->expects($this->once())
            ->method('getResolvedPhpDoc')
            ->with(
                $this->callback(
                    static fn (?string $file): bool => realpath((string) $file) === $stateFile
                ),
                Polite::class,
                GreetingTrait::class,
                'greet',
                $docComment,
            )
            ->willReturn($builderPhpDoc);
        $this->fileTypeMapper = $fileTypeMapper;

        $phpDocInheritanceResolver = $this->createMock(PhpDocInheritanceResolverInterface::class);
        $phpDocInheritanceResolver->expects($this->once())
            ->method('resolvePhpDocForMethod')
            ->with($cr, 'greet', $builderPhpDoc, ['name'])
            ->willReturn($builderPhpDoc);
        $this->phpDocInheritanceResolver = $phpDocInheritanceResolver;

        $method = $this->buildInstance()->getMethod($cr, 'greet');

        $this->assertInstanceOf(StateMethod::class, $method);
        $this->assertSame($builderPhpDoc, $method->getResolvedPhpDoc());
    }

    /**
     * Without phpdoc, the purity of the method is unknown : it is not considered as a method without side effect.
     */
    public function testGetMethodWithoutPhpDocOnTheBuilder(): void
    {
        $cr = $this->buildProxyClassReflection();

        $fileTypeMapper = $this->createMock(FileTypeMapperInterface::class);
        $fileTypeMapper->expects($this->never())->method('getResolvedPhpDoc');
        $this->fileTypeMapper = $fileTypeMapper;

        $phpDocInheritanceResolver = $this->createMock(PhpDocInheritanceResolverInterface::class);
        $phpDocInheritanceResolver->expects($this->once())
            ->method('resolvePhpDocForMethod')
            ->with($cr, 'getFormattedBody', null, [])
            ->willReturn(null);
        $this->phpDocInheritanceResolver = $phpDocInheritanceResolver;

        $method = $this->buildInstance()->getMethod($cr, 'getFormattedBody');

        $this->assertInstanceOf(StateMethod::class, $method);
        $this->assertNull($method->getResolvedPhpDoc());
        $this->assertEquals(TrinaryLogic::createMaybe(), $method->isPure());
        $this->assertEquals(TrinaryLogic::createMaybe(), $method->hasSideEffects());
    }


    public function testGetMethodImplementProxyMethodInStateWithNonExplicitReturnTagCompatibleType(): void
    {
        $brc = $this->createStub(ReflectionClass::class);
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
            $this->createStub(PhpDocNode::class),
        );

        $rpr = $rcOfRPB->getProperty('nameScope');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            new NameScope('foo', []),
        );

        $rcofval = new ReflectionClass(ReturnTag::class);
        $returnTag = $rcofval->newInstanceWithoutConstructor();

        $typeStub = $this->createStub(Type::class);
        $rpr = $rcofval->getProperty('type');
        $rpr->setValue($returnTag, $typeStub);

        $rpr = $rcofval->getProperty('isExplicit');
        $rpr->setValue($returnTag, false);

        $rpr = $rcOfRPB->getProperty('returnTag');
        $rpr->setValue(
            $resolvedPHPDocBlock,
            $returnTag,
        );

        $this->getPhpDocInheritanceResolverStub()
            ->method('resolvePhpDocForMethod')
            ->willReturn($resolvedPHPDocBlock);

        $this->assertInstanceOf(StateMethod::class, $this->buildInstance()->getMethod($cr, 'getFormattedBody'));
    }
}
