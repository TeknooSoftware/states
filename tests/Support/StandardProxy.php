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
 * Class StandardProxy
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
}
