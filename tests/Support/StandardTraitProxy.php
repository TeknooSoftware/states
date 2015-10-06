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
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\Tests\Support;

use UniAlteri\States\Proxy;

/**
 * Class StandardTraitProxy
 * To build an specific instance of the class Proxy\Standard to test this default class.
 * By default, the class Proxy\Integrated uses '\UniAlteri\States\Factory\StandardStartupFactory' as startup factory.
 * But, in the test, we will use '\UniAlteri\Tests\Support\MockStartupFactory' to unit testing only the proxy.
 *
 * This extends support implements also all supported standard interface to tests implementation provided by the trait Proxy.
 * To avoid errors in the usage of this lib, these interfaces are not defined with released proxies.
 * You must implement these interface, according to your needs, in your derived proxies like in this class.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class StandardTraitProxy extends MotherProxy implements Proxy\ProxyInterface,
    \Serializable,
    \ArrayAccess,
    \SeekableIterator,
    \Countable
{
    use Proxy\ProxyTrait;
    use Proxy\MagicCallTrait;
    use Proxy\ArrayAccessTrait;
    use Proxy\IteratorTrait;
    use Proxy\SerializableTrait;

    /**
     * Property to test behavior of proxy when a method in a state want access to a public property
     * @var mixed
     */
    public $publicProperty = 'value1';

    /**
     * Property to test behavior of proxy when a method in a state want access to a protected property
     * @var mixed
     */
    protected $protectedProperty = 'value1';

    /**
     * Property to test behavior of proxy when a method in a state want access to a private property
     * @var mixed
     */
    private $privateProperty = 'value1';

    /**
     * Initialize the proxy.
     */
    public function __construct()
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
    }

    /**
     * Method to test behavior of proxy when a method in a state want access to a public method
     * @return string
     */
    public function publicMethodToCall()
    {
        return 'fooBar';
    }

    /**
     * Method to test behavior of proxy when a method in a state want access to a protected method
     * @return string
     */
    protected function protectedMethodToCall()
    {
        return 'fooBar';
    }

    /**
     * Method to test behavior of proxy when a method in a state want access to a private method
     * @return string
     */
    private function privateMethodToCall()
    {
        return 'fooBar';
    }
}
