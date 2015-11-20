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
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\Support;

use Teknoo\States\Proxy;

/**
 * Class StandardProxy
 * To build an specific instance of the class Proxy\Standard to test this default class.
 * By default, the class Proxy\Integrated uses '\Teknoo\States\Factory\StandardStartupFactory' as startup factory.
 * But, in the test, we will use '\Teknoo\Tests\Support\MockStartupFactory' to unit testing only the proxy.
 *
 * This extends support implements also all supported standard interface to tests implementation provided by the trait Proxy.
 * To avoid errors in the usage of this lib, these interfaces are not defined with released proxies.
 * You must implement these interface, according to your needs, in your derived proxies like in this class.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class StandardProxy extends Proxy\Standard implements
    \Serializable,
    \ArrayAccess,
    \SeekableIterator,
    \Countable
{
    use Proxy\MagicCallTrait;
    use Proxy\ArrayAccessTrait;
    use Proxy\IteratorTrait;
    use Proxy\SerializableTrait;

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
    private $privateProperty = 'value1';

    /**
     * Method to test behavior of proxy when a method in a state want access to a public method.
     *
     * @return string
     */
    public function publicMethodToCall()
    {
        return 'fooBar';
    }

    /**
     * Method to test behavior of proxy when a method in a state want access to a protected method.
     *
     * @return string
     */
    protected function protectedMethodToCall()
    {
        return 'fooBar';
    }

    /**
     * Method to test behavior of proxy when a method in a state want access to a private method.
     *
     * @return string
     */
    private function privateMethodToCall()
    {
        return 'fooBar';
    }
}
