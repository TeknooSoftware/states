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
use Teknoo\Immutable\ImmutableTrait;
use Teknoo\States\Automated\AutomatedInterface;

/**
 * Abstract implementation of AssertionInterface.
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractAssertion implements AssertionInterface
{
    use ImmutableTrait;

    /**
     * List of stated to enable if the assertion is valid.
     * @var array<int, string>
     */
    private array $statesList;

    private ?AutomatedInterface $proxy = null;

    /**
     * @param array<int, string>|string $statesList
     */
    public function __construct(array | string $statesList)
    {
        $this->uniqueConstructorCheck();

        $this->statesList = (array) $statesList;
    }

    /*
     * Return the proxy linked to the check of this assertions.
     */
    protected function getProxy(): ?AutomatedInterface
    {
        return $this->proxy;
    }

    /*
     * Abstract method to implement into final class to proccess to the check of this assertions.
     */
    abstract protected function process(AutomatedInterface $proxy): void;

    public function check(AutomatedInterface $proxy): AssertionInterface
    {
        $that = clone $this;
        $that->proxy = $proxy;

        $that->process($proxy);

        return $that;
    }

    /**
     * @throws \Teknoo\States\Proxy\Exception\StateNotFound
     */
    public function isValid(): AssertionInterface
    {
        if (!$this->proxy instanceof AutomatedInterface) {
            throw new RuntimeException('Error, the proxy is not a valid AutomatedInterface instance');
        }

        foreach ($this->statesList as $state) {
            $this->proxy->enableState($state);
        }

        return $this;
    }
}
