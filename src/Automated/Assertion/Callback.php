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

namespace Teknoo\States\Automated\Assertion;

use RuntimeException;
use Teknoo\States\Automated\AutomatedInterface;

use function is_callable;

/**
 * Assertion implementation to delegated the validation to a callable (a callback or a closure) and
 * return enabled states.
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
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
            throw new RuntimeException('Error the callback is not callable');
        }

        $that = clone $this;
        $that->proxy = $proxy;

        ($this->callback)($proxy, $that);

        return $that;
    }
}
