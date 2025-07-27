<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
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
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\PHPStan\Reflection\Exception;

use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Teknoo\States\PHPStan\Reflection\Exception\MissingReflectionMethodException;

/**
 * Class MissingReflectionMethodExceptionTest.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversClass(MissingReflectionMethodException::class)]
class MissingReflectionMethodExceptionTest extends TestCase
{
    public function testExceptionInheritance(): void
    {
        $exception = new MissingReflectionMethodException();

        $this->assertInstanceOf(LogicException::class, $exception);
        $this->assertInstanceOf(MissingReflectionMethodException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'Test exception message';
        $exception = new MissingReflectionMethodException($message);

        $this->assertEquals($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Test exception message';
        $code = 123;
        $exception = new MissingReflectionMethodException($message, $code);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }

    public function testExceptionWithPrevious(): void
    {
        $previous = new LogicException('Previous exception');
        $exception = new MissingReflectionMethodException('Test message', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
