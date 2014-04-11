<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\Tests\Support;

use \UniAlteri\States\Factory;
use \UniAlteri\States\Factory\Exception;
use \UniAlteri\States\Proxy;

/**
 * Class MockStartupFactory
 * Mock startup factory to test integrated proxy behavior
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MockStartupFactory implements Factory\StartupFactoryInterface
{
    /**
     * Proxy to initialize called with forwardStartup
     * Public to allow testCase to check its value to confirm the behavior of the integrated proxy
     * @var Proxy\ProxyInterface
     */
    public static $calledProxyObject = null;

    /**
     * Find the factory to use for the new proxy object to initialize it with its container and states.
     * This method is called by the constructor of the stated object
     * @param Proxy\ProxyInterface $proxyObject
     * @param string $stateName
     * @return boolean
     * @throws Exception\InvalidArgument when $factoryIdentifier is not an object
     * @throws Exception\UnavailableFactory when the required factory was not found
     */
    public static function forwardStartup($proxyObject, $stateName=null)
    {
        self::$calledProxyObject = $proxyObject;
    }
}