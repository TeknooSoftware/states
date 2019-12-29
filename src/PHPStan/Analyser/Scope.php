<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\PHPStan\Analyser;

use PHPStan\Analyser\MutatingScope as PHPStanScope;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Analyser\ScopeFactory;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\VariableTypeHolder;
use PHPStan\Broker\Broker;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\ClassReflection;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Rules\Properties\PropertyReflectionFinder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ThisType;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateTrait;

/**
 * To overide Scope in PHPStan to manage correctly states class in a stated class :
 * Closure written in this states class are executed in the context of the proxy and not of the state class,
 * but PHPStan can not manage this in its native distribution.
 *
 * @see http://php.net/manual/en/class.arrayaccess.php
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
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
     * @throws \ReflectionException
     */
    private function updateScopeIfStateClass(
        ClassReflection $classReflection,
        ScopeContext $initialContext,
        array &$variablesTypes,
        Broker $broker
    ): ScopeContext {
        $nativeReflection = $classReflection->getNativeReflection();

        if (!\in_array(StateTrait::class, $nativeReflection->getTraitNames())) {
            return $initialContext;
        }

        if (!isset($variablesTypes['this'])) {
            return $initialContext;
        }

        $proxyClass = $stateClass = $nativeReflection->getName();
        do {
            $explodedClass = \explode('\\', $proxyClass);
            \array_pop($explodedClass);
            $proxyClass = \implode('\\', $explodedClass);
        } while (!empty($proxyClass) && !\class_exists($proxyClass));

        if (empty($proxyClass) || !\class_exists($proxyClass)) {
            throw new ShouldNotHappenException("Proxy class was not found for $stateClass");
        }

        $nativeReflectionProxy = new \ReflectionClass($proxyClass);
        if (!$nativeReflectionProxy->implementsInterface(ProxyInterface::class)) {
            throw new ShouldNotHappenException(
                "$proxyClass must implements the interface " . ProxyInterface::class . " to be a valid Stated Class"
            );
        }

        $variablesTypes['this'] = VariableTypeHolder::createYes(new ThisType($proxyClass));

        return ScopeContext::create($initialContext->getFile())->enterClass($broker->getClass($proxyClass));
    }

    /**
     * @param \PHPStan\Reflection\FunctionReflection|MethodReflection|null $function
     * @param \PHPStan\Analyser\VariableTypeHolder[] $variablesTypes
     * @param \PHPStan\Analyser\VariableTypeHolder[] $moreSpecificTypes
     * @param array<string, true> $currentlyAssignedExpressions
     * @param string[] $dynamicConstantNames
     * @throws ShouldNotHappenException
     * @throws ClassNotFoundException
     * @throws \ReflectionException
     */
    public function __construct(
        ScopeFactory $scopeFactory,
        Broker $broker,
        Standard $printer,
        TypeSpecifier $typeSpecifier,
        PropertyReflectionFinder $propertyReflectionFinder,
        ScopeContext $context,
        bool $declareStrictTypes = \false,
        $function = null,
        ?string $namespace = null,
        array $variablesTypes = [],
        array $moreSpecificTypes = [],
        ?string $inClosureBindScopeClass = null,
        ?ParametersAcceptor $anonymousFunctionReflection = null,
        bool $inFirstLevelStatement = \true,
        array $currentlyAssignedExpressions = [],
        array $dynamicConstantNames = []
    ) {
        $classReflection = $context->getClassReflection();
        if (null !== $anonymousFunctionReflection && $classReflection instanceof ClassReflection) {
            $context = $this->updateScopeIfStateClass($classReflection, $context, $variablesTypes, $broker);
        }

        parent::__construct(
            $scopeFactory,
            $broker,
            $printer,
            $typeSpecifier,
            $propertyReflectionFinder,
            $context,
            $declareStrictTypes,
            $function,
            $namespace,
            $variablesTypes,
            $moreSpecificTypes,
            $inClosureBindScopeClass,
            $anonymousFunctionReflection,
            $inFirstLevelStatement,
            $currentlyAssignedExpressions,
            $dynamicConstantNames
        );
    }
}
