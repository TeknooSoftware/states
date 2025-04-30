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

namespace Teknoo\States\Automated\Assertion;

use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\Automated\Exception\AssertionException;

use function is_callable;

/**
 * Assertion implementation to delegated the validation to a callable (a callback or a closure) and
 * return enabled states.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class Callback extends AbstractAssertion
{
    /**
     * Callable (callback or closure) to execute to determine if this assertio is valid or not.
     *
     * @var callable
     */
    private $callback;

    /*
     * To register the callable (callback or closure) to execute to determine if this assertio is valid or not.
     */
    public function call(callable $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    public function check(AutomatedInterface $proxy): AssertionInterface
    {
        if (!is_callable($this->callback)) {
            throw new AssertionException('Error the callback is not callable');
        }

        $that = clone $this;
        $that->proxy = $proxy;

        ($this->callback)($proxy, $that);

        return $that;
    }
}
