<?php

/*
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

namespace Teknoo\States\Automated\Assertion;

use Teknoo\States\Automated\AutomatedInterface;

/**
 * Class Callback
 * Assertion implementation to delegated the validation to a callable (a callback or a closure) and
 * return enabled states.
 *
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
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

    /**
     * To register the callable (callback or closure) to execute to determine if this assertio is valid or not.
     *
     * @param callable $callback
     *
     * @return Callback|self
     */
    public function call(callable $callback): Callback
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function process(AutomatedInterface $proxy): void
    {
        if (!\is_callable($this->callback)) {
            throw new \RuntimeException('Error the callback is not callable');
        }

        ($this->callback)($proxy, $this);
    }
}
