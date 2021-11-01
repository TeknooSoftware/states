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

use PHPStan\Analyser\ConditionalExpressionHolder;
use PHPStan\Analyser\MutatingScope as PHPStanScope;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\VariableTypeHolder;
use PHPStan\Parser\Parser;
use PHPStan\Php\PhpVersion;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\DynamicReturnTypeExtensionRegistry;
use PHPStan\Type\OperatorTypeSpecifyingExtensionRegistry;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ClassReflection;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Rules\Properties\PropertyReflectionFinder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ThisType;
use PHPStan\Type\Type;
use ReflectionClass;
use ReflectionException;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateTrait;

use function array_flip;
use function array_pop;
use function class_exists;
use function explode;
use function implode;

/**
 * To over ride Scope in PHPStan to manage correctly states class in a stated class :
 * Closure written in this states class are executed in the context of the proxy and not of the state class,
 * but PHPStan can not manage this in its native distribution.
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
class Scope extends PHPStanScope
{
    /**
     * @param array<VariableTypeHolder> $variablesTypes
     * @throws ShouldNotHappenException
     * @throws ClassNotFoundException
     * @throws ReflectionException
     */
    private function updateScopeIfStateClass(
        ClassReflection $classReflection,
        ScopeContext $initialContext,
        array &$variablesTypes,
        ReflectionProvider $reflectionProvider
    ): ScopeContext {
        $nativeReflection = $classReflection->getNativeReflection();

        if (!isset(array_flip($nativeReflection->getTraitNames())[StateTrait::class])) {
            return $initialContext;
        }

        if (!isset($variablesTypes['this'])) {
            return $initialContext;
        }

        $proxyClass = $stateClass = $nativeReflection->getName();
        do {
            $explodedClass = explode('\\', $proxyClass);
            array_pop($explodedClass);
            $proxyClass = implode('\\', $explodedClass);
        } while (!empty($proxyClass) && !class_exists($proxyClass));

        if (empty($proxyClass) || !class_exists($proxyClass)) {
            throw new ShouldNotHappenException("Proxy class was not found for $stateClass");
        }

        $nativeReflectionProxy = new ReflectionClass($proxyClass);
        if (!$nativeReflectionProxy->implementsInterface(ProxyInterface::class)) {
            throw new ShouldNotHappenException(
                "$proxyClass must implements the interface " . ProxyInterface::class . " to be a valid Stated Class"
            );
        }

        $reflecionClass = $reflectionProvider->getClass($proxyClass);

        $variablesTypes['this'] = VariableTypeHolder::createYes(new ThisType($reflecionClass));

        return ScopeContext::create($initialContext->getFile())->enterClass($reflecionClass);
    }

    /**
     * @param array<string, Type> $constantTypes
     * @param \PHPStan\Reflection\FunctionReflection|MethodReflection|null $function
     * @param \PHPStan\Analyser\VariableTypeHolder[] $variablesTypes
     * @param \PHPStan\Analyser\VariableTypeHolder[] $moreSpecificTypes
     * @param array<string, ConditionalExpressionHolder[]> $conditionalExpressions
     * @param array<string, true> $currentlyAssignedExpressions
     * @param array<string, Type> $nativeExpressionTypes
     * @param array<MethodReflection|FunctionReflection> $inFunctionCallsStack
     * @param string[] $dynamicConstantNames
     * @param bool $treatPhpDocTypesAsCertain
     * @throws ShouldNotHappenException
     * @throws ClassNotFoundException
     * @throws ReflectionException
     */
    public function __construct(
        ScopeFactory $scopeFactory,
        ReflectionProvider $reflectionProvider,
        DynamicReturnTypeExtensionRegistry $dynamicReturnTypeExtensionRegistry,
        OperatorTypeSpecifyingExtensionRegistry $operatorTypeSpecifyingExtensionRegistry,
        Standard $printer,
        TypeSpecifier $typeSpecifier,
        PropertyReflectionFinder $propertyReflectionFinder,
        Parser $parser,
        NodeScopeResolver $nodeScopeResolver,
        ScopeContext $context,
        PhpVersion $phpVersion,
        bool $declareStrictTypes = \false,
        array $constantTypes = [],
        $function = null,
        ?string $namespace = null,
        array $variablesTypes = [],
        array $moreSpecificTypes = [],
        array $conditionalExpressions = [],
        ?string $inClosureBindScopeClass = null,
        ?ParametersAcceptor $anonymousFunctionReflection = null,
        bool $inFirstLevelStatement = \true,
        array $currentlyAssignedExpressions = [],
        array $nativeExpressionTypes = [],
        array $inFunctionCallsStack = [],
        array $dynamicConstantNames = [],
        bool $treatPhpDocTypesAsCertain = \true,
        bool $afterExtractCall = false,
        ?Scope $parentScope = null
    ) {
        $classReflection = $context->getClassReflection();
        if (null !== $anonymousFunctionReflection && $classReflection instanceof ClassReflection) {
            $context = $this->updateScopeIfStateClass($classReflection, $context, $variablesTypes, $reflectionProvider);
        }

        parent::__construct(
            $scopeFactory,
            $reflectionProvider,
            $dynamicReturnTypeExtensionRegistry,
            $operatorTypeSpecifyingExtensionRegistry,
            $printer,
            $typeSpecifier,
            $propertyReflectionFinder,
            $parser,
            $nodeScopeResolver,
            $context,
            $phpVersion,
            $declareStrictTypes,
            $constantTypes,
            $function,
            $namespace,
            $variablesTypes,
            $moreSpecificTypes,
            $conditionalExpressions,
            $inClosureBindScopeClass,
            $anonymousFunctionReflection,
            $inFirstLevelStatement,
            $currentlyAssignedExpressions,
            $nativeExpressionTypes,
            $inFunctionCallsStack,
            $dynamicConstantNames,
            $treatPhpDocTypesAsCertain,
            $afterExtractCall,
            $parentScope
        );
    }
}
