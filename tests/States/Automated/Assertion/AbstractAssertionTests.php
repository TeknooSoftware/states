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

namespace Teknoo\Tests\States\Automated\Assertion;

use Teknoo\States\Automated\Assertion\AbstractAssertion;

/**
 * Class AbstractAssertionTests.
 *
 * @covers \Teknoo\States\Automated\Assertion\AbstractAssertion
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
abstract class AbstractAssertionTests extends \PHPUnit\Framework\TestCase
{
    /**
     * @return AbstractAssertion
     */
    abstract public function buildInstance();

    public function testCheckWithBadProxy(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildInstance()->check(new \stdClass());
    }

    public function testIsValidWithoutProxyThrowAnException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->buildInstance()->isValid();
    }
}
