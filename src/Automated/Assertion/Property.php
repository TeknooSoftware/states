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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\Automated\Assertion;

use Teknoo\States\Automated\Assertion\Property\ConstraintInterface;
use Teknoo\States\Automated\Assertion\Property\ConstraintsSet;
use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\Automated\Exception\AssertionException;
use Teknoo\States\Exception\IllegalArgument;

use function array_keys;
use function array_shift;
use function is_numeric;
use function is_string;

/**
 * Implementation of AssertionInterface to determine states list from stated class instance's values.
 * All assertions defined with the method with() must be valid to get the assertion as valid.
 * Constraints on value must be defined by ConstraintInterface instance via the method "with".
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Property extends AbstractAssertion
{
    /**
     * @var array<string, array<ConstraintInterface>>
     */
    private array $constraints = [];

    /*
     * To register a constraint on a property. $exceptedValue must be a ConstraintInstance value.
     * Several constraints types are already defined into Teknoo\States\Automated\Assertion\Property.
     */
    public function with(string $property, ConstraintInterface $exceptedValue): Property
    {
        if (!is_string($property) || is_numeric($property)) {
            throw new IllegalArgument("Property $property is not a valid property name");
        }

        $that = clone $this;
        $that->constraints[$property][] = $exceptedValue;

        return $that;
    }

    /**
     * @throws \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function check(AutomatedInterface $proxy): AssertionInterface
    {
        $that = clone $this;
        $that->proxy = $proxy;

        if (empty($that->constraints)) {
            return $that->isValid();
        }

        //extract first constraint set
        [$property] = array_keys($that->constraints);
        $constraints = (array) array_shift($that->constraints);

        $proxy->checkProperty(
            (string) $property,
            new ConstraintsSet($constraints, $that)
        );

        return $that;
    }

    /**
     * @throws \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function isValid(): AssertionInterface
    {
        if (null === $this->proxy) {
            throw new AssertionException('The method "check" with a valid proxy was not called before');
        }

        if (empty($this->constraints)) {
            return parent::isValid();
        }

        return $this->check($this->proxy);
    }
}
