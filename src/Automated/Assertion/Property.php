<?php

declare(strict_types=1);

/**
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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\States\Automated\Assertion;

use Teknoo\States\Automated\Assertion\Property\ConstraintInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSet;
use Teknoo\States\Automated\AutomatedInterface;

/**
 * Class Assertion
 * Implementation of AssertionInterface to determine states list from stated class instance's values.
 * All assertions defined with the method with() must be valid to get the assertion as valid.
 * Constraints on value must be defined by ConstraintInterface instance via the method "with".
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Property extends AbstractAssertion
{
    /**
     * @var array|callable
     */
    private $constraints = [];

    /**
     * To register a constraint on a property. $exceptedValue must be a ConstraintInstance value.
     * Several constraints types are already defined into Teknoo\States\Automated\Assertion\Property.
     *
     * @param string $property
     * @param ConstraintInterface $exceptedValue
     *
     * @return Property
     */
    public function with(string $property, ConstraintInterface $exceptedValue): Property
    {
        $that = clone $this;
        $that->constraints[$property][] = $exceptedValue;

        return $that;
    }

    /**
     * {@inheritdoc}
     * @throws \Teknoo\States\Proxy\Exception\StateNotFound
     */
    protected function process(AutomatedInterface $proxy): void
    {
        if (empty($this->constraints)) {
            parent::isValid();
            return;
        }

        [$property] = \array_keys($this->constraints);
        $constraints = \array_shift($this->constraints);

        $proxy->checkProperty($property, new ConstraintsSet($constraints, $this));
    }

    /**
     * @return AssertionInterface
     * @throws \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function isValid(): AssertionInterface
    {
        if (empty($this->constraints)) {
            return parent::isValid();
        }

        $this->process($this->getProxy());

        return $this;
    }
}
