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
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

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
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
class AutomatedAcme implements AutomatedInterface
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
        $this->initializeStateProxy();
    }

    protected static function statesListDeclaration(): array
    {
        return [
            State1::class,
            State2::class,
        ];
    }

    /**
     * @return self
     */
    public function setFoo(mixed $foo)
    {
        $this->foo = $foo;

        return $this;
    }

    /**
     * @return self
     */
    public function setFoo1(mixed $foo1)
    {
        $this->foo1 = $foo1;

        return $this;
    }

    /**
     * @return self
     */
    public function setFoo2(mixed $foo2)
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
