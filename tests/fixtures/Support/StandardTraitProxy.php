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

namespace Teknoo\Tests\Support;

use AllowDynamicProperties;
use ArrayAccess;
use Countable;
use SeekableIterator;
use Teknoo\States\Proxy;
use Teknoo\States\Proxy\ArrayAccessTrait;
use Teknoo\States\Proxy\IteratorTrait;
use Teknoo\States\Proxy\MagicCallTrait;
use Teknoo\States\Proxy\ProxyTrait;
use Teknoo\States\Proxy\SerializableTrait;
use Teknoo\States\State\StateInterface;

/**
 * Class StandardTraitProxy
 * To build an specific instance of the class Proxy\Standard to test this default class.
 * By default, the class Proxy\Integrated uses '\Teknoo\States\Factory\StandardStartupFactory' as startup factory.
 * But, in the test, we will use '\Teknoo\Tests\Support\MockStartupFactory' to unit testing only the proxy.
 *
 * This extends support implements also all supported standard interface to tests implementation provided by the trait Proxy.
 * To avoid errors in the usage of this lib, these interfaces are not defined with released proxies.
 * You must implement these interface, according to your needs, in your derived proxies like in this class.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[AllowDynamicProperties]
class StandardTraitProxy extends MotherProxy implements
    Proxy\ProxyInterface,
    ArrayAccess,
    SeekableIterator,
    Countable
{
    use ProxyTrait;
    use MagicCallTrait;
    use ArrayAccessTrait;
    use IteratorTrait;
    use SerializableTrait;

    /**
     * Property to test behavior of proxy when a method in a state want access to a public property.
     *
     * @var mixed
     */
    public $publicProperty = 'value1';

    /**
     * Property to test behavior of proxy when a method in a state want access to a protected property.
     *
     * @var mixed
     */
    protected $protectedProperty = 'value1';

    /**
     * Property to test behavior of proxy when a method in a state want access to a private property.
     *
     * @var mixed
     */
    private string $privateProperty = 'value1';

    /**
     * Initialize the proxy.
     */
    public function __construct()
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeStateProxy();
    }

    /**
     * Method to test behavior of proxy when a method in a state want access to a public method.
     */
    public function publicMethodToCall(): string
    {
        return 'fooBar';
    }

    /**
     * Method to test behavior of proxy when a method in a state want access to a protected method.
     */
    protected function protectedMethodToCall(): string
    {
        return 'fooBar';
    }

    /**
     * Method to test behavior of proxy when a method in a state want access to a private method.
     */
    private function privateMethodToCall(): string
    {
        return 'fooBar';
    }

    protected static function statesListDeclaration(): array
    {
        return [];
    }

    /**
     * To test a bad state injection
     */
    public function registerStateWithoutOriginal(string $stateName, StateInterface $state): Proxy\ProxyInterface
    {
        $this->states[$stateName] = $state;
        $this->activesStates[$stateName] = $state;

        return $this;
    }
}
