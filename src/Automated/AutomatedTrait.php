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
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\Automated;

use Teknoo\States\Automated\Assertion\AssertionInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;

/**
 * Class AutomatedTrait
 * Trait to implement in proxy of your stated classes to add automated behaviors.
 *
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
     * @return \Generator|AssertionInterface[]
     */
    private function iterateAssertions()
    {
        foreach ($this->listAssertions() as $assertion) {
            if (!$assertion instanceof AssertionInterface) {
                throw new \RuntimeException('Error, all assertions must implements AssertionInterface');
            }

            yield $assertion;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateStates(): AutomatedInterface
    {
        $this->disableAllStates();
        foreach ($this->iterateAssertions() as $assertion) {
            $assertion->check($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function checkProperty(
        string $property,
        ConstraintsSetInterface $constraints
    ): AutomatedInterface {
        $value = $this->{$property} ?? null;

        $constraints->check($value);

        return $this;
    }
}
