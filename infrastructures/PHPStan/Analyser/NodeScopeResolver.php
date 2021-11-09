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

namespace Teknoo\States\PHPStan\Analyser;

use PhpParser\NodeTraverser;
use PHPStan\Analyser\MutatingScope;
use PHPStan\Analyser\NodeScopeResolver as BaseNodeScopeResolver;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\BetterReflection\Reflector\ClassReflector;
use PHPStan\DependencyInjection\Reflection\ClassReflectionExtensionRegistryProvider;
use PHPStan\Parser\Parser;
use PHPStan\Type\FileTypeMapper;
use PHPStan\PhpDoc\StubPhpDocProvider;
use PHPStan\Php\PhpVersion;
use PHPStan\PhpDoc\PhpDocInheritanceResolver;
use PHPStan\File\FileHelper;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\DependencyInjection\Type\DynamicThrowTypeExtensionProvider;

/**
 * NodeScope Resolver extending to parse the AST before analyze it with the AST Visitor provided by
 * this library to support Statesclasses and method in state class and avoid false positive with PHPStan
 * about deadcode or "non existent method"
 *
 * @see http://php.net/manual/en/class.arrayaccess.php
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

class NodeScopeResolver extends BaseNodeScopeResolver
{
    public function __construct(
        ReflectionProvider $reflectionProvider,
        ClassReflector $classReflector,
        ClassReflectionExtensionRegistryProvider $classReflectionExtensionRegistryProvider,
        Parser $parser,
        FileTypeMapper $fileTypeMapper,
        StubPhpDocProvider $stubPhpDocProvider,
        PhpVersion $phpVersion,
        PhpDocInheritanceResolver $phpDocInheritanceResolver,
        FileHelper $fileHelper,
        TypeSpecifier $typeSpecifier,
        DynamicThrowTypeExtensionProvider $dynamicThrowTypeExtensionProvider,
        bool $polluteScopeWithLoopInitialAssignments,
        bool $polluteScopeWithAlwaysIterableForeach,
        array $earlyTerminatingMethodCalls,
        array $earlyTerminatingFunctionCalls,
        bool $implicitThrows,
        private ASTVisitor $astVisitor,
    ) {
        parent::__construct(
            $reflectionProvider,
            $classReflector,
            $classReflectionExtensionRegistryProvider,
            $parser,
            $fileTypeMapper,
            $stubPhpDocProvider,
            $phpVersion,
            $phpDocInheritanceResolver,
            $fileHelper,
            $typeSpecifier,
            $dynamicThrowTypeExtensionProvider,
            $polluteScopeWithLoopInitialAssignments,
            $polluteScopeWithAlwaysIterableForeach,
            $earlyTerminatingMethodCalls,
            $earlyTerminatingFunctionCalls,
            $implicitThrows,
        );
    }

    public function processNodes(array $nodes, MutatingScope $scope, callable $nodeCallback): void
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor($this->astVisitor);
        $traverser->traverse($nodes);

        parent::processNodes($nodes, $scope, $nodeCallback);
    }
}
