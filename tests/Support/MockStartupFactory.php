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

use UniAlteri\States\Factory\FactoryInterface;
use UniAlteri\States\Factory\Exception;
use UniAlteri\States\Factory\StartupFactoryInterface;
use UniAlteri\States\Proxy\ProxyInterface;

/**
 * Class MockStartupFactory
 * Mock startup factory to test integrated proxy behavior.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License

 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MockStartupFactory implements StartupFactoryInterface
{
    /**
     * Proxy to initialize called with forwardStartup
     * Public to allow testCase to check its value to confirm the behavior of the integrated proxy.
     *
     * @var ProxyInterface
     */
    public static $calledProxyObject = null;

    /**
     * {@inheritdoc}
     */
    public static function forwardStartup(ProxyInterface $proxyObject, string $stateName = null): FactoryInterface
    {
        self::$calledProxyObject = $proxyObject;

        return new MockFactory(
            'my\Stated\Class',
            new MockFinder('my\Stated\Class', 'path\to\stated\class'),
            new \ArrayObject([])
        );
    }
}
