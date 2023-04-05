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

namespace Teknoo\States\Automated\Assertion;

use Teknoo\Immutable\ImmutableTrait;
use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\Automated\Exception\AssertionException;
use Teknoo\States\Proxy\Exception\StateNotFound;

/**
 * Abstract implementation of AssertionInterface.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
abstract class AbstractAssertion implements AssertionInterface
{
    use ImmutableTrait;

    /**
     * List of stated to enable if the assertion is valid.
     * @var array<int, string>
     */
    private readonly array $statesList;

    protected ?AutomatedInterface $proxy = null;

    /**
     * @param array<int, string>|string $statesList
     */
    public function __construct(array | string $statesList)
    {
        $this->uniqueConstructorCheck();

        $this->statesList = (array) $statesList;
    }

    /**
     * @throws StateNotFound
     * @throws AssertionException
     */
    public function isValid(): AssertionInterface
    {
        if (!$this->proxy instanceof AutomatedInterface) {
            throw new AssertionException('Error, the proxy is not a valid AutomatedInterface instance');
        }

        foreach ($this->statesList as $state) {
            $this->proxy->enableState($state);
        }

        return $this;
    }
}
