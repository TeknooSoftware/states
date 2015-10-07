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
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\Tests\States\Loader;

use UniAlteri\States\Loader;
use UniAlteri\States\Loader\Exception;

/**
 * Class IncludePathManagerTest
 * Tests the excepted behavior of standard include path manager implementing
 * the interface \UniAlteri\States\Loader\IncludePathManagerInterface.
 *
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class IncludePathManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * To restore include path to continue test.
     *
     * @var null
     */
    protected $backupIncludePath = null;

    /**
     * Prepare environment before test.
     */
    protected function setUp()
    {
        $this->backupIncludePath = get_include_path();
        parent::setUp();
    }

    /**
     * Clean environment after test.
     */
    protected function tearDown()
    {
        set_include_path($this->backupIncludePath);
        parent::tearDown();
    }

    /**
     * Return object for test.
     *
     * @return Loader\IncludePathManager
     */
    protected function getManagementObject()
    {
        return new Loader\IncludePathManager();
    }

    /**
     * Test exception of the object.
     */
    public function testSetIncludePathBadPaths()
    {
        try {
            $this->getManagementObject()->setIncludePath(new \stdClass());
        } catch (Exception\IllegalArgument $e) {
            return;
        } catch (\Exception $e) {
            return;
        }

        $this->fail('Error, the manager must throw an exception if $paths is not an array of string');
    }

    /**
     * Test set include path.
     */
    public function testSetIncludePath()
    {
        $manager = $this->getManagementObject();

        $this->assertEquals(
            explode(PATH_SEPARATOR, get_include_path()),
            $manager->setIncludePath(
                array(
                    __DIR__,
                    dirname(__DIR__),
                )
            )
        );

        $this->assertEquals(__DIR__.PATH_SEPARATOR.dirname(__DIR__), get_include_path());
    }

    /**
     * Test set include path.
     */
    public function testSetIncludePathWithArrayObject()
    {
        $manager = $this->getManagementObject();

        $array = new \ArrayObject(
            array(
                __DIR__,
                dirname(__DIR__),
            )
        );
        $this->assertEquals(
            explode(PATH_SEPARATOR, get_include_path()),
            $manager->setIncludePath(
                $array
            )
        );

        $this->assertEquals(__DIR__.PATH_SEPARATOR.dirname(__DIR__), get_include_path());
    }

    /**
     * Test get include path.
     */
    public function testGetIncludePath()
    {
        $this->assertEquals(
            $this->getManagementObject()->getIncludePath(),
            explode(PATH_SEPARATOR, get_include_path())
        );
    }
}
