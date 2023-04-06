<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\Support;

use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * Class MockOnlyPublic
 * Mock class to test the default trait State behavior with public methods.
 * All methods have not a description to check the state's behavior with these methods.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class MockOnlyPublic implements StateInterface
{
    use StateTrait;

    /**
     * Standard Method 1.
     */
    public function standardMethod1()
    {
        return fn($a=0, $b=0): float|int|array => $a+$b;
    }

    /**
     * Final Method 2.
     */
    final public function finalMethod2()
    {
        return fn($a=0, $b=0): float|int|array => $a+$b;
    }

    public static function staticMethod3()
    {
        return fn($a=0, $b=0): float|int|array => $a+$b;
    }

    public function standardMethod4()
    {
        return fn($a=0, $b=0): float|int|array => $a+$b;
    }

    public function methodBuilderNoReturnClosure(): void
    {
    }
}
