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

namespace Teknoo\States\Automated;

use ReflectionClass;
use ReflectionAttribute;
use Teknoo\States\Attributes\AssertionInterface as AttrInterface;
use Teknoo\States\Attributes\Assertions;
use Teknoo\States\Automated\Assertion\AssertionInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;
use Teknoo\States\Automated\Exception\AssertionException;
use Teknoo\States\Proxy\ProxyInterface;

use function is_array;
use function is_iterable;
use function method_exists;

/**
 * Trait to implement in proxy of your stated classes to add automated behaviors.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @mixin AutomatedInterface
 * @mixin \Teknoo\States\Proxy\ProxyInterface
 */
trait AutomatedTrait
{
    /**
     * @var array<class-string, array<callable(ProxyInterface):AssertionInterface>>
     */
    private static array $listAttrAssertions = [];

    /**
     * @var array<AssertionInterface>|null
     */
    private ?array $compiledAssertions = null;

    /**
     * To get all validations rules needed by instances.
     * (Internal getter)
     *
     * @return AssertionInterface[]
     * protected function listAssertions(): array
     */

    /**
     * @param class-string $className
     * @param class-string $forClass
     */
    private function extractAttrAssertions(string $className, string $forClass): void
    {
        $reflectionClass = new ReflectionClass($className);
        $attributesList = $reflectionClass->getAttributes(AttrInterface::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($attributesList as $attribute) {
            /** @var ReflectionAttribute<AttrInterface> $attribute */
            self::$listAttrAssertions[$forClass][] = fn (ProxyInterface $proxy) => $attribute->newInstance()
                ->getAssertion($proxy);
        }

        $inheritsFromParents = true;

        $configs = $reflectionClass->getAttributes(Assertions::class);
        foreach ($configs as $config) {
            /** @var ReflectionAttribute<Assertions> $config */
            $inheritsFromParents = $config->newInstance()->inheritsFromParent;
        }

        if ($inheritsFromParents && $reflectionClass->getParentClass()) {
            $parentClassName = $reflectionClass->getParentClass()->getName();
            $this->extractAttrAssertions($parentClassName, $forClass);
        }
    }

    private function compileAssertions(): void
    {
        if (!isset(self::$listAttrAssertions[$this::class])) {
            self::$listAttrAssertions[$this::class] = [];

            $this->extractAttrAssertions($this::class, $this::class);
        }

        $this->compiledAssertions = [];
        assert(is_array(self::$listAttrAssertions[$this::class]));
        foreach (self::$listAttrAssertions[$this::class] as $assertionFactory) {
            $this->compiledAssertions[] = $assertionFactory($this);
        }

        if (method_exists($this, 'listAssertions')) {
            $objectAssertions = $this->listAssertions();
            if (!is_iterable($objectAssertions)) {
                throw new AssertionException('Error, listAssertions() must return an iterable of AssertionInterface');
            }

            foreach ($objectAssertions as $assertion) {
                if (!$assertion instanceof AssertionInterface) {
                    throw new AssertionException('Error, all assertions must implements AssertionInterface');
                }

                $this->compiledAssertions[] = $assertion;
            }
        }
    }

    /**
     * To iterate defined assertions and check if they implements the interface AssertionInterface.
     *
     * @return AssertionInterface[]
     */
    private function iterateAssertions(): iterable
    {
        if (null === $this->compiledAssertions) {
            $this->compileAssertions();
        }

        assert(is_array($this->compiledAssertions));
        yield from $this->compiledAssertions;
    }

    public function updateStates(): AutomatedInterface
    {
        $this->disableAllStates();
        foreach ($this->iterateAssertions() as $assertion) {
            $assertion->check($this);
        }

        return $this;
    }

    public function checkProperty(
        string $property,
        ConstraintsSetInterface $constraints
    ): AutomatedInterface {
        $value = $this->{$property} ?? null;

        $constraints->check($value);

        return $this;
    }
}
