<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\PHPStan\Analyser;

use PHPUnit\Framework\TestCase;
use PhpParser\Node;
use PHPStan\Analyser\MutatingScope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\BetterReflection\Reflector\Reflector;
use PHPStan\DependencyInjection\Reflection\ClassReflectionExtensionRegistryProvider;
use PHPStan\Parser\Parser;
use PHPStan\Type\FileTypeMapper;
use PHPStan\PhpDoc\StubPhpDocProvider;
use PHPStan\Php\PhpVersion;
use PHPStan\PhpDoc\PhpDocInheritanceResolver;
use PHPStan\Reflection\InitializerExprTypeResolver;
use PHPStan\File\FileHelper;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\DependencyInjection\Type\DynamicThrowTypeExtensionProvider;
use PHPStan\Rules\Properties\ReadWritePropertiesExtensionProvider;
use Teknoo\States\PHPStan\Analyser\ASTVisitor;
use Teknoo\States\PHPStan\Analyser\NodeScopeResolver;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @covers      \Teknoo\States\PHPStan\Analyser\NodeScopeResolver
 */
class NodeScopeResolverTest extends TestCase
{
    public function testProcessNodes(): void
    {
        $visitor = $this->createMock(ASTVisitor::class);

        $nodeScopeResolver = new NodeScopeResolver(
            $this->createMock(ReflectionProvider::class),
            $this->createMock(InitializerExprTypeResolver::class),
            $this->createMock(Reflector::class),
            $this->createMock(ClassReflectionExtensionRegistryProvider::class),
            $this->createMock(Parser::class),
            $this->createMock(FileTypeMapper::class),
            $this->createMock(StubPhpDocProvider::class),
            $this->createMock(PhpVersion::class),
            $this->createMock(PhpDocInheritanceResolver::class),
            $this->createMock(FileHelper::class),
            $this->createMock(TypeSpecifier::class),
            $this->createMock(DynamicThrowTypeExtensionProvider::class),
            $this->createMock(ReadWritePropertiesExtensionProvider::class),
            true,
            true,
            [],
            [],
            true,
            true,
            $visitor,
        );

        $visitor->expects(self::atLeastOnce())
            ->method('leaveNode');

        $nodeScopeResolver->processNodes(
            [$this->createMock(Node::class)],
            $this->createMock(MutatingScope::class),
            function (): void {}
        );
    }
}
