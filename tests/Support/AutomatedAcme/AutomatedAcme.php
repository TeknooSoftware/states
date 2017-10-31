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

namespace Teknoo\Tests\Support\AutomatedAcme;

use Teknoo\States\Automated\Assertion\AssertionInterface;
use Teknoo\States\Automated\Assertion\Property;
use Teknoo\States\Automated\AutomatedInterface;
use Teknoo\States\Automated\AutomatedTrait;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;
use Teknoo\Tests\Support\AutomatedAcme\States\State1;
use Teknoo\Tests\Support\AutomatedAcme\States\State2;

/**
 * Class AutomatedAcme.
 *
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class AutomatedAcme implements ProxyInterface, AutomatedInterface
{
    use ProxyTrait,
        AutomatedTrait;

    /**
     * For AssertionTest.
     *
     * @var mixed
     */
    protected $foo;

    /**
     * For AssertionTest.
     *
     * @var mixed
     */
    protected $foo1;

    /**
     * For AssertionTest.
     *
     * @var mixed
     */
    protected $foo2;

    /**
     * AutomatedAcme constructor.
     */
    public function __construct()
    {
        $this->initializeProxy();
    }

    /**
     * @return array
     */
    protected static function statesListDeclaration(): array
    {
        return [
            State1::class,
            State2::class,
        ];
    }

    /**
     * @param mixed $foo
     *
     * @return self
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;

        return $this;
    }

    /**
     * @param mixed $foo1
     *
     * @return self
     */
    public function setFoo1($foo1)
    {
        $this->foo1 = $foo1;

        return $this;
    }

    /**
     * @param mixed $foo2
     *
     * @return self
     */
    public function setFoo2($foo2)
    {
        $this->foo2 = $foo2;

        return $this;
    }

    /**
     * @return AssertionInterface[]
     */
    public function listAssertions(): array
    {
        return [
            (new Property([State1::class]))
                ->with('foo', new Property\IsEqual('bar')),
            (new Property([State2::class]))
                ->with('foo1', new Property\IsEqual('bar1'))
                ->with('foo2', new Property\IsNull()),
        ];
    }

    /**
     * Return the list of enabled states. Present only for debug and tests
     */
    public function listEnabledStates(): array
    {
        if (!empty($this->activesStates) && \is_array($this->activesStates)) {
            return \array_keys($this->activesStates);
        } else {
            return [];
        }
    }
}
