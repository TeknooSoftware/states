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

namespace Teknoo\States\PHPStan;

use OutOfBoundsException;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionFunction;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionMethod;
use PHPStan\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;
use PHPStan\BetterReflection\Reflection\ReflectionMethod as BetterReflectionMethod;
use PHPStan\BetterReflection\SourceLocator\Exception\NoClosureOnLine;
use PHPStan\Cache\Cache;
use PHPStan\Parser\Parser;
use PHPStan\Parser\FunctionCallStatementFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\InitializerExprTypeResolver;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Reflection\AttributeReflectionFactory;
use PHPStan\Reflection\ReflectionProvider\DirectReflectionProviderProvider;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Reflection\Assertions;
use PHPStan\Type\Type;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction as NatveReflectionFunction;
use Teknoo\States\PHPStan\Reflection\StateMethod;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;

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
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class MethodsClassExtension implements MethodsClassReflectionExtension
{
    /**
     * @var array<ReflectionClass<object>>>
     */
    private array $proxyNativeReflection = [];

    /**
     * @var array<string, bool>
     */
    private array $hasMethodsCache = [];

    public function __construct(
        private readonly Parser $parser,
        private readonly FunctionCallStatementFinder $functionCallStatementFinder,
        private readonly Cache $cache,
        private readonly ReflectionProvider $reflectionProvider,
        private readonly InitializerExprTypeResolver $initializerExprTypeResolver,
    ) {
    }

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
     * @throws ReflectionException
     */
    private function listStateClassFor(string $className): array
    {
        try {
            $listDeclarationReflection = $this->proxyNativeReflection[$className]->getMethod('statesListDeclaration');
            $listClosure = $listDeclarationReflection->getClosure(null);

            return $listClosure();
            //@codeCoverageIgnoreStart
        } catch (ReflectionException) {
            return [];
        }

        //@codeCoverageIgnoreEnd
    }

    /**
     * @throws ReflectionException
     */
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

    /**
     * @param class-string<object> $proxyClassName
     * @param ReflectionClass<object> $stateNativeReflection
     * @param non-empty-string $method
     * @throws \PHPStan\Broker\ClassNotFoundException
     * @throws ReflectionException
     */
    private function getMethodReflection(
        string $proxyClassName,
        ClassReflection $classReflection,
        string $stateClass,
        ReflectionClass $stateNativeReflection,
        string $method
    ): PhpMethodReflection {
        $factoryNativeReflection = $stateNativeReflection->getMethod($method);
        /** @var \Closure $factoryClosure */
        $factoryClosure = $factoryNativeReflection->getClosure($stateNativeReflection->newInstanceWithoutConstructor());
        $stateClosure = $factoryClosure();

        //To use the original \ReflectionClass api and not "BetterReflectionClass" whome not implements all the api.
        $stateClosure = @$stateClosure->bindTo(
            (new ReflectionClass($proxyClassName))->newInstanceWithoutConstructor(),
            $proxyClassName,
        );

        if (null === $stateClosure) {
            throw new ShouldNotHappenException(
                "Closure returned by {$stateNativeReflection->getName()}::{$method} must be not static"
            );
        }

        try {
            $factoryReflection = new ReflectionMethod(BetterReflectionMethod::createFromName($stateClass, $method));
            //@codeCoverageIgnoreStart
        } catch (OutOfBoundsException) {
            $factoryReflection = $factoryNativeReflection;
        }

        //@codeCoverageIgnoreEnd

        try {
            $closureReflection = new ReflectionFunction(BetterReflectionFunction::createFromClosure($stateClosure));
            //@codeCoverageIgnoreStart
        } catch (NoClosureOnLine) {
            $closureReflection = new NatveReflectionFunction($stateClosure);
        }

        //@codeCoverageIgnoreEnd
        $stateMethod = new StateMethod(
            factoryReflection: $factoryReflection,
            closureReflection: $closureReflection,
            reflectionClass: $classReflection->getNativeReflection(),
        );

        return new PhpMethodReflection(
            initializerExprTypeResolver: $this->initializerExprTypeResolver,
            declaringClass: $classReflection,
            declaringTrait: null,
            reflection: $stateMethod->getReflection(),
            reflectionProvider: $this->reflectionProvider,
            attributeReflectionFactory: new AttributeReflectionFactory(
                initializerExprTypeResolver: $this->initializerExprTypeResolver,
                reflectionProviderProvider: new DirectReflectionProviderProvider(
                    $this->reflectionProvider,
                ),
            ),
            parser: $this->parser,
            templateTypeMap: new TemplateTypeMap([]),
            phpDocParameterTypes: [],
            phpDocReturnType: null,
            phpDocThrowType: null,
            deprecatedDescription: null,
            isDeprecated: false,
            isInternal: false,
            isFinal: false,
            isPure: null,
            asserts: Assertions::createEmpty(),
            acceptsNamedArguments: true,
            selfOutType: null,
            phpDocComment: null,
            phpDocParameterOutTypes: [],
            immediatelyInvokedCallableParameters: [],
            phpDocClosureThisTypeParameters: [],
            attributes: [],
        );
    }

    /**
     * @throws ShouldNotHappenException
     * @throws \PHPStan\Broker\ClassNotFoundException
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
                    stateClass: $stateClass,
                    stateNativeReflection: $stateNativeReflection,
                    method: $methodName,
                );
            }
        }

        throw new ShouldNotHappenException("Class $proxyClassName has no method $methodName");
    }
}
