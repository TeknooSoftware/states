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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Automated\Assertion;

use Teknoo\States\Automated\Assertion\AbstractAssertion;

/**
 * Class AbstractAssertionTest.
 *
 * @covers \Teknoo\States\Automated\Assertion\AbstractAssertion
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractAssertionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return AbstractAssertion
     */
    abstract public function buildInstance();

    public function testCheckWithBadProxy()
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance()->check(new \stdClass());
    }

    public function testIsValidWithoutProxyThrowAnException()
    {
        $this->expectException(\RuntimeException::class);
        $this->buildInstance()->isValid();
    }
}
