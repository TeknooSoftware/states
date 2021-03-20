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

namespace Teknoo\States\Automated;

use Teknoo\States\Automated\Assertion\Property\ConstraintsSetInterface;
use Teknoo\States\Proxy\ProxyInterface;

/**
 * Interface AutomatedInterface
 * Interface to implement automated stated class to enable or disable states according to validation rules defined
 * in your class.
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface AutomatedInterface extends ProxyInterface
{
    /**
     * Method called by the stated class instance itself to perform states changes according its validations rules.
     */
    public function updateStates(): AutomatedInterface;

    /**
     * Method to check the property of the object with the constraint and enable followed states if the constraint has
     * been respected
     */
    public function checkProperty(
        string $property,
        ConstraintsSetInterface $constraints
    ): AutomatedInterface;
}
