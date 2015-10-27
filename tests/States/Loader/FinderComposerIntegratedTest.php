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
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace Teknoo\Tests\States\Loader;

use Teknoo\States\Loader;
use Teknoo\States\Factory;
use Teknoo\Tests\Support;

/**
 * Class FinderComposerIntegratedTest
 * Tests the excepted behavior of integrated finder implementing the interface \Teknoo\States\Loader\FinderInterface.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2015 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @covers Teknoo\States\Loader\FinderComposerIntegrated
 * @covers Teknoo\States\Loader\FinderComposer
 */
class FinderComposerIntegratedTest extends FinderComposerTest
{
    /**
     * Initialize the integrated finder for test with mock objects.
     *
     * @param string $statedClassName
     * @param string $pathString
     *
     * @return Loader\FinderComposer
     */
    protected function initializeFinder($statedClassName, $pathString)
    {
        $this->finder = new Loader\FinderComposerIntegrated($statedClassName, $pathString, $this->getClassLoaderMock());

        return $this->finder;
    }

    /**
     * Initialize the startup factory to run this tests with the integrated finder (use the integrated proxy).
     */
    public function testBuildProxyDefault()
    {
        $this->initializeFinder('Class2', $this->statedClass2Path);
        Factory\StandardStartupFactory::registerFactory(
            'Teknoo\States\Proxy\Integrated',
            new Support\MockFactory(
                'my\Stated\Class', $this->finder,
                new \ArrayObject([])
            )
        );

        $proxy = $this->finder->buildProxy();
        $this->assertInstanceOf('\Teknoo\States\Proxy\ProxyInterface', $proxy);
        $this->assertInstanceOf('\Teknoo\States\Proxy\Standard', $proxy);
        $this->assertInstanceOf('Class2\\Class2', $proxy);
    }
}
