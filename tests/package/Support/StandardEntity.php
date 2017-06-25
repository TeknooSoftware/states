<?php

/**
 * StatesBundle.
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
namespace Teknoo\Tests\UniversalPackage\States\Support;

use Teknoo\UniversalPackage\States\Entity\AbstractStandardEntity;
use Teknoo\States\Proxy\ArrayAccessTrait;
use Teknoo\States\Proxy\IteratorTrait;
use Teknoo\States\Proxy\MagicCallTrait;
use Teknoo\States\Proxy\SerializableTrait;

/**
 * Class StandardEntity
 * To build an specific instance of the class StandardEntity to test this default class.
 * By default, the class Proxy\Standard uses '\Teknoo\States\Factory\StandardStartupFactory' as startup factory.
 * But, in the test, we will use '\Teknoo\Tests\Support\MockStartupFactory' to unit testing only the proxy.
 *
 * This extends support implements also all supported standard interface to tests implementation provided by the trait Proxy.
 * To avoid errors in the usage of this lib, these interfaces are not defined with released proxies.
 * You must implement these interface, according to your needs, in your derived proxies like in this class.
 *
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class StandardEntity extends AbstractStandardEntity implements
    \Serializable,
    \ArrayAccess,
    \SeekableIterator,
    \Countable
{
    use ArrayAccessTrait,
        MagicCallTrait,
        IteratorTrait,
        SerializableTrait;

    /**
     * Class name of the factory to use during set up to initialize this object.
     * It is a virtual factory, it does nothing except logs actions.
     *
     * @var string
     */
    protected static $startupFactoryClassName = '\Teknoo\Tests\Support\MockStartupFactory';

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
     * Method to update static::$_startupFactoryClassName to run some unit tests.
     *
     * @param string $className
     */
    public static function defineStartupFactoryClassName($className)
    {
        static::$startupFactoryClassName = $className;
    }

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

    public static function statesListDeclaration(): array
    {
        return [];
    }
}
