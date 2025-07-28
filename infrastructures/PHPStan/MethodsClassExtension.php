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

namespace Teknoo\States\PHPStan;

use Closure;
use OutOfBoundsException;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionFunction;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionMethod;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionParameter;
use PHPStan\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;
use PHPStan\BetterReflection\Reflection\ReflectionMethod as BetterReflectionMethod;
use PHPStan\BetterReflection\SourceLocator\Exception\NoClosureOnLine;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\PhpDoc\PhpDocInheritanceResolver;
use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\Reflection\Assertions;
use PHPStan\Reflection\AttributeReflectionFactory;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Reflection\InitializerExprContext;
use PHPStan\Reflection\InitializerExprTypeResolver;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Generic\TemplateTypeHelper;
use PHPStan\Type\Generic\TemplateTypeVariance;
use PHPStan\Type\Type;
use PHPStan\Type\TypehintHelper;
use ReflectionClass;
use ReflectionException;
use Teknoo\States\PHPStan\Contracts\Reflection\AttributeReflectionFactoryInterface;
use Teknoo\States\PHPStan\Contracts\Reflection\InitializerExprTypeResolverInterface;
use Teknoo\States\PHPStan\Contracts\Reflection\PhpDocInheritanceResolverInterface;
use Teknoo\States\PHPStan\Reflection\StateMethod;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;

use function array_map;
use function array_pop;
use function class_exists;
use function explode;
use function implode;

/**
 * Extension for PHPStan to support methods defined in states in Stated class when they are called from the proxy
 * or another method in a state.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class MethodsClassExtension implements MethodsClassReflectionExtension
{
    /**
     * @var array<ReflectionClass<object>>
     */
    private array $proxyNativeReflection = [];

    /**
     * @var array<string, bool>
     */
    private array $hasMethodsCache = [];

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
        private readonly AttributeReflectionFactory|AttributeReflectionFactoryInterface $attributeReflectionFactory,
        private readonly InitializerExprTypeResolver|InitializerExprTypeResolverInterface $initializerExprTypeResolver,
        private readonly PhpDocInheritanceResolver|PhpDocInheritanceResolverInterface $phpDocInheritanceResolver,
    ) {
    }

    /**
     * @throws ReflectionException
     */
    private function checkIfManagedClass(ClassReflection $reflection): bool
    {
        if ($reflection->isInterface()) {
            return false;
        }

        $className = $reflection->getName();
        if ($reflection->implementsInterface(ProxyInterface::class)) {
            $this->proxyNativeReflection[$className] = new ReflectionClass($className);

            return true;
        }

        if (!$reflection->implementsInterface(StateInterface::class)) {
            return false;
        }

        $proxyClass = $className;
        do {
            $explodedClass = explode('\\', $proxyClass);
            array_pop($explodedClass);
            $proxyClass = implode('\\', $explodedClass);
        } while (!empty($proxyClass) && !class_exists($proxyClass));

        if (empty($proxyClass) || !class_exists($proxyClass)) {
            return false;
        }

        $proxyReflection = new ReflectionClass($proxyClass);
        if (!$proxyReflection->implementsInterface(ProxyInterface::class)) {
            return false;
        }

        $this->proxyNativeReflection[$className] = $proxyReflection;

        return true;
    }

    /**
     * @return array<class-string>
     */
    private function listStateClassFor(string $className): array
    {
        try {
            $listDeclarationReflection = $this->proxyNativeReflection[$className]->getMethod('statesListDeclaration');
            $listClosure = $listDeclarationReflection->getClosure();

            /** @var array<class-string> $statesClasses */
            $statesClasses = $listClosure();

            return $statesClasses;
            //@codeCoverageIgnoreStart
        } catch (ReflectionException) {
            return [];
        }

        //@codeCoverageIgnoreEnd
    }

    private function checkMethod(ClassReflection $reflection, string $methodName): bool
    {
        $proxyClassName = $reflection->getName();
        foreach ($this->listStateClassFor($proxyClassName) as $stateClass) {
            $nf = new ReflectionClass($stateClass);
            if ($nf->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws ReflectionException
     */
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        $cacheKey = $classReflection->getName() . '::' . $methodName;
        if (isset($this->hasMethodsCache[$cacheKey])) {
            return $this->hasMethodsCache[$cacheKey];
        }

        if (!$this->checkIfManagedClass($classReflection)) {
            return $this->hasMethodsCache[$cacheKey] = false;
        }

        return $this->hasMethodsCache[$cacheKey] = $this->checkMethod($classReflection, $methodName);
    }

    private function getPhpDocReturnType(
        ClassReflection $phpDocBlockClassReflection,
        ResolvedPhpDocBlock $resolvedPhpDoc,
        Type $nativeReturnType
    ): ?Type {
        $returnTag = $resolvedPhpDoc->getReturnTag();

        if (null === $returnTag) {
            return null;
        }

        $phpDocReturnType = $returnTag->getType();
        $phpDocReturnType = TemplateTypeHelper::resolveTemplateTypes(
            type: $phpDocReturnType,
            standins: $phpDocBlockClassReflection->getActiveTemplateTypeMap(),
            callSiteVariances: $phpDocBlockClassReflection->getCallSiteVarianceMap(),
            positionVariance: TemplateTypeVariance::createCovariant(),
        );

        if ($returnTag->isExplicit() || $nativeReturnType->isSuperTypeOf($phpDocReturnType)->yes()) {
            return $phpDocReturnType;
        }

        return null;
    }

    /**
     * @param class-string<object> $proxyClassName
     * @param ReflectionClass<object> $stateNativeReflection
     * @param non-empty-string $method
     * @throws ClassNotFoundException
     * @throws ReflectionException|ShouldNotHappenException
     */
    private function getMethodReflection(
        string $proxyClassName,
        ClassReflection $classReflection,
        ReflectionClass $stateNativeReflection,
        string $method
    ): ExtendedMethodReflection {
        $factoryNativeReflection = $stateNativeReflection->getMethod($method);
        $stateInstance = $stateNativeReflection->newInstanceWithoutConstructor();
        $factoryClosure = $factoryNativeReflection->getClosure($stateInstance);
        /** @var Closure $stateClosure */
        $stateClosure = $factoryClosure();

        //To use the original \ReflectionClass api and not "BetterReflectionClass" whome not implements all the api.
        $stateClosure = @$stateClosure->bindTo(
            new ReflectionClass($proxyClassName)->newInstanceWithoutConstructor(),
            $proxyClassName,
        );

        if (null === $stateClosure) {
            throw new ShouldNotHappenException(
                "Closure returned by {$stateNativeReflection->getName()}::{$method} must be not static"
            );
        }

        try {
            $factoryReflection = new ReflectionMethod(
                BetterReflectionMethod::createFromInstance($stateInstance, $method)
            );
            //@codeCoverageIgnoreStart
        } catch (OutOfBoundsException) {
            throw new ShouldNotHappenException(
                "Closure returned by {$stateNativeReflection->getName()}::{$method} must be not static"
            );
        }

        //@codeCoverageIgnoreEnd
        try {
            $closureReflection = new ReflectionFunction(BetterReflectionFunction::createFromClosure($stateClosure));
            //@codeCoverageIgnoreStart
        } catch (NoClosureOnLine) {
            throw new ShouldNotHappenException(
                "Closure returned by {$stateNativeReflection->getName()}::{$method} must be not static"
            );
        }

        //@codeCoverageIgnoreEnd
        $docComment = $factoryReflection->getDocComment();
        if (false === $docComment) {
            $docComment = null;
        }

        $positionalParameterNames = array_map(
            static fn (ReflectionParameter $parameter): string => $parameter->getName(),
            $closureReflection->getParameters()
        );

        $resolvedPhpDoc = $this->phpDocInheritanceResolver->resolvePhpDocForMethod(
            $docComment,
            $classReflection->getFileName(),
            $classReflection,
            null,
            $factoryReflection->getName(),
            $positionalParameterNames,
        );

        $templateTypeMap = $resolvedPhpDoc->getTemplateTypeMap();
        $nativeReturnType = TypehintHelper::decideTypeFromReflection(
            reflectionType: $closureReflection->getReturnType(),
            selfClass: $classReflection,
        );
        $phpDocReturnType = $this->getPhpDocReturnType(
            $classReflection,
            $resolvedPhpDoc,
            $nativeReturnType
        );

        $phpDocThrowType = null;
        if ($resolvedPhpDoc->getThrowsTag() !== null) {
            $phpDocThrowType = $resolvedPhpDoc->getThrowsTag()->getType();
        }

        $selfOutType = null;
        if ($resolvedPhpDoc->getSelfOutTag() !== null) {
            $selfOutType = $resolvedPhpDoc->getSelfOutTag()->getType();
        }

        $asserts = Assertions::createFromResolvedPhpDocBlock($resolvedPhpDoc);

        $acceptsNamedArguments = $resolvedPhpDoc->acceptsNamedArguments();

        return new StateMethod(
            reflectionProvider: $this->reflectionProvider,
            initializerExprTypeResolver: $this->initializerExprTypeResolver,
            attributeReflectionFactory: $this->attributeReflectionFactory,
            factoryReflection: $factoryReflection,
            closureReflection: $closureReflection,
            declaringClass: $classReflection,
            phpDocReturnType: $phpDocReturnType,
            phpDocThrowType: $phpDocThrowType,
            selfOutType: $selfOutType,
            asserts: $asserts,
            templateTypeMap: $templateTypeMap,
            isPure: $resolvedPhpDoc->isPure(),
            attributes: $this->attributeReflectionFactory->fromNativeReflection(
                reflections: $closureReflection->getAttributes(),
                context: InitializerExprContext::fromClassMethod(
                    className: $classReflection->getName(),
                    traitName: null,
                    methodName: $factoryReflection->getName(),
                    fileName: $classReflection->getFileName()
                )
            ),
            acceptsNamedArguments: $acceptsNamedArguments,
        );
    }

    /**
     * @throws ShouldNotHappenException
     * @throws ClassNotFoundException
     * @param non-empty-string $methodName
     * @throws ReflectionException
     */
    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $proxyClassName = $classReflection->getName();

        if (!$this->checkIfManagedClass($classReflection)) {
            throw new ShouldNotHappenException("Class $proxyClassName is not managed by this extension");
        }

        foreach ($this->listStateClassFor($classReflection->getName()) as $stateClass) {
            $stateNativeReflection = new ReflectionClass($stateClass);
            if ($stateNativeReflection->hasMethod($methodName)) {
                return $this->getMethodReflection(
                    proxyClassName: $proxyClassName,
                    classReflection: $classReflection,
                    stateNativeReflection: $stateNativeReflection,
                    method: $methodName,
                );
            }
        }

        throw new ShouldNotHappenException("Class $proxyClassName has no method $methodName");
    }
}
