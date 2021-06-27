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

namespace Teknoo\States\Automated\Assertion\Property;

use Teknoo\Immutable\ImmutableTrait;

use function array_key_exists;
use function is_array;

/**
 * Constraint to use with `Teknoo\States\Automated\Property` to check if a property is an array and has a required key
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class HasEmptyValueForKey extends AbstractConstraint
{
    use ImmutableTrait;

    private string $keyName;

    public function __construct(string $keyName)
    {
        $this->uniqueConstructorCheck();

        $this->keyName = $keyName;
    }

    public function check(mixed &$value): ConstraintInterface
    {
        if (is_array($value) && array_key_exists($this->keyName, $value) && empty($value[$this->keyName])) {
            $this->isValid($value);
        }

        return $this;
    }
}
