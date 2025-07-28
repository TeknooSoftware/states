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

use Teknoo\States\Automated\Assertion\AssertionInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;
use Teknoo\States\Automated\Exception\AssertionException;

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
     * To get all validations rules needed by instances.
     * (Internal getter)
     *
     * @return AssertionInterface[]
     */
    abstract protected function listAssertions(): array;

    /**
     * To iterate defined assertions and check if they implements the interface AssertionInterface.
     *
     * @return AssertionInterface[]
     */
    private function iterateAssertions(): iterable
    {
        foreach ($this->listAssertions() as $assertion) {
            if (!$assertion instanceof AssertionInterface) {
                throw new AssertionException('Error, all assertions must implements AssertionInterface');
            }

            yield $assertion;
        }
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
