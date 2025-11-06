<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
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
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Doctrine;

use AllowDynamicProperties;
use Closure;
use ProxyManager\Proxy\LazyLoadingInterface;
use Teknoo\States\Doctrine\AbstractStandardObject;
use Teknoo\States\Proxy\ArrayAccessTrait;
use Teknoo\States\Proxy\IteratorTrait;
use Teknoo\States\Proxy\MagicCallTrait;
use Teknoo\States\Proxy\SerializableTrait;

/**
 * Class StandardObject
 * To build an specific instance of the class StandardObject to test this default class.
 * By default, the class Proxy\Standard uses '\Teknoo\States\Factory\StandardStartupFactory' as startup factory.
 * But, in the test, we will use '\Teknoo\Tests\Support\MockStartupFactory' to unit testing only the proxy.
 *
 * This extends support implements also all supported standard interface to tests implementation provided by the trait Proxy.
 * To avoid errors in the usage of this lib, these interfaces are not defined with released proxies.
 * You must implement these interface, according to your needs, in your derived proxies like in this class.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[AllowDynamicProperties]
class
StandardObjectLegacy extends AbstractStandardObject implements
    \ArrayAccess,
    \SeekableIterator,
    \Countable,
    LazyLoadingInterface
{
    use ArrayAccessTrait;
    use MagicCallTrait;
    use IteratorTrait;
    use SerializableTrait;

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
     */
    private mixed $privateProperty = 'value1';

    /**
     * Method to update static::$_startupFactoryClassName to run some unit tests.
     */
    public static function defineStartupFactoryClassName(string $className): void
    {
        static::$startupFactoryClassName = $className;
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

    public function setProxyInitializer(?Closure $initializer = null): void
    {
        // TODO: Implement setProxyInitializer() method.
    }

    public function getProxyInitializer(): ?Closure
    {
        // TODO: Implement getProxyInitializer() method.
    }

    public function initializeProxy(): bool
    {
        return true;
    }

    public function isProxyInitialized(): bool
    {
        return false;
    }
}
