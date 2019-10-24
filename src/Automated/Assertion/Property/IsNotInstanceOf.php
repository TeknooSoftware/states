<?php

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
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\States\Automated\Assertion\Property;

use Teknoo\Immutable\ImmutableTrait;

/**
 * Constraint to use with Teknoo\States\Automated\Property to check if a property
 * is not an instance of the excepted class name. *
 *
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class IsNotInstanceOf extends AbstractConstraint
{
    use ImmutableTrait;

    /**
     * @var string
     */
    private $exceptedClassName;

    /**
     * IsNotInstanceOf constructor.
     *
     * @param string $exceptedClassName
     */
    public function __construct(string $exceptedClassName)
    {
        $this->uniqueConstructorCheck();

        $this->exceptedClassName = $exceptedClassName;
    }

    /**
     * {@inheritdoc}
     */
    public function check(&$value): ConstraintInterface
    {
        if (!$value instanceof $this->exceptedClassName) {
            $this->isValid($value);
        }

        return $this;
    }
}
