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

namespace Teknoo\States\PHPStan;

use PHPStan\Broker\Broker;
use PHPStan\Cache\Cache;
use PHPStan\Parser\Parser;
use PHPStan\Parser\FunctionCallStatementFinder;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Generic\TemplateTypeMap;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use Teknoo\States\PHPStan\Reflection\StateMethod;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\State\StateInterface;

use function array_pop;
use function class_exists;
use function explode;
use function implode;
use function is_callable;

/**
 * Extension for PHPStan to support methods defined in states in Stated class when they are called from the proxy
 * or another method in a state.
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
class MethodsClassExtension implements MethodsClassReflectionExtension, BrokerAwareExtension
{
    private Broker $broker;

    /**
     * @var array<ReflectionClass<object>>>
     */
    private array $proxyNativeReflection = [];

    public function __construct(
        private Parser $parser,
        private FunctionCallStatementFinder $functionCallStatementFinder,
        private Cache $cache
    ) {
    }

    public function setBroker(Broker $broker): void
    {
        $this->broker = $broker;
    }

    /**
     * @param ReflectionClass<object> $nativeReflection
     * @throws ReflectionException
     */
    private function checkIfManagedClass(ReflectionClass $nativeReflection): bool
    {
        if ($nativeReflection->isInterface()) {
            return false;
        }

        $className = $nativeReflection->getName();
        if ($nativeReflection->implementsInterface(ProxyInterface::class)) {
            $this->proxyNativeReflection[$className] = $nativeReflection;

            return true;
        }

        if (!$nativeReflection->implementsInterface(StateInterface::class)) {
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
        $listDeclarationReflection = $this->proxyNativeReflection[$className]->getMethod('statesListDeclaration');
        $listClosure = $listDeclarationReflection->getClosure(null);

        if (!is_callable($listClosure)) {
            return [];
        }

        return $listClosure();
    }

    /**
     * @param ReflectionClass<object> $nativeReflection
     * @throws ReflectionException
     */
    private function checkMethod(ReflectionClass $nativeReflection, string $methodName): bool
    {
        $className = $nativeReflection->getName();

        foreach ($this->listStateClassFor($className) as $stateClass) {
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
        $nativeReflection = $classReflection->getNativeReflection();

        if (!$this->checkIfManagedClass($nativeReflection)) {
            return false;
        }

        return $this->checkMethod($nativeReflection, $methodName);
    }

    /**
     * @param ReflectionClass<object> $stateClassReflection
     * @param ReflectionClass<object> $nativeProxyReflection
     * @throws \PHPStan\Broker\ClassNotFoundException
     * @throws ReflectionException
     */
    private function getMethodReflection(
        ReflectionClass $stateClassReflection,
        ReflectionClass $nativeProxyReflection,
        string $method
    ): MethodReflection {
        $factoryReflection = $stateClassReflection ->getMethod($method);
        /** @var \Closure $factoryClosure */
        $factoryClosure = $factoryReflection->getClosure($stateClassReflection->newInstanceWithoutConstructor());
        $stateClosure = $factoryClosure();

        //To use the original \ReflectionClass api and not "BetterReflectionClass" whome not implements all the api.
        $realNativeProxyReflection = new ReflectionClass($nativeProxyReflection->getName());
        $stateClosure = $stateClosure->bindTo(
            $realNativeProxyReflection->newInstanceWithoutConstructor(),
            $realNativeProxyReflection->getName()
        );

        $closureReflection = new ReflectionFunction($stateClosure);

        return new PhpMethodReflection(
            $this->broker->getClass($nativeProxyReflection->getName()),
            null,
            new StateMethod($factoryReflection, $closureReflection),
            $this->broker,
            $this->parser,
            $this->functionCallStatementFinder,
            $this->cache,
            new TemplateTypeMap([]),
            [],
            null,
            null,
            null,
            false,
            false,
            false,
            null
        );
    }

    /**
     * @throws ShouldNotHappenException
     * @throws \PHPStan\Broker\ClassNotFoundException
     * @throws ReflectionException
     */
    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        $nativeReflection = $classReflection->getNativeReflection();

        if (!$this->checkIfManagedClass($nativeReflection)) {
            $className = $nativeReflection->getName();
            throw new ShouldNotHappenException("Class $className is not managed by this extension");
        }

        $className = $nativeReflection->getName();

        foreach ($this->listStateClassFor($className) as $stateClass) {
            $stateClassReflection = new ReflectionClass($stateClass);
            if ($stateClassReflection->hasMethod($methodName)) {
                return $this->getMethodReflection(
                    $stateClassReflection,
                    $this->proxyNativeReflection[$className],
                    $methodName
                );
            }
        }

        $className = $nativeReflection->getName();
        throw new ShouldNotHappenException("Class $className has no method $methodName");
    }
}
