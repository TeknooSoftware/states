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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     1.1.0
 */

namespace UniAlteri\Tests\Support;

use UniAlteri\States\Loader;
use UniAlteri\States\Loader\Exception;

/**
 * Class MockIncludePathManager
 * Mock Include Path manager to unit test Loader behavior.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MockIncludePathManager implements Loader\IncludePathManagerInterface
{
    /**
     * Current included paths, not use current include path to avoid conflict with the testing environment.
     *
     * @var string[]
     */
    protected $paths = array();

    /**
     * History of all change.
     *
     * @var array
     */
    protected $allChangePaths = array();

    /**
     * Sets the include_path configuration option.
     *
     * @param string[] $paths (paths must be split into an array)
     *
     * @return $this
     *
     * @throws Exception\IllegalArgument if the argument $paths is not an array of string
     */
    public function setIncludePath($paths)
    {
        if (!is_array($paths) && !$paths instanceof \ArrayObject) {
            throw new Exception\IllegalArgument('Error, $paths is not an array of string');
        }

        //Simulate the behavior of a real manager
        $old = $this->paths;
        $this->paths = $paths;
        $this->allChangePaths[] = $paths;

        return $old;
    }

    /**
     * Gets the current include_path configuration option.
     *
     * @return string[] (paths must be split into an array)
     */
    public function getIncludePath()
    {
        return $this->paths;
    }

    /**
     * To reset history of changes
     * Method added, called by Unit TestCase to reinitialize the manager before each tests.
     */
    public function resetAllChangePath()
    {
        $this->allChangePaths = array();
    }

    /**
     * Get all change path.
     *
     * @return array
     */
    public function getAllChangePaths()
    {
        return $this->allChangePaths;
    }
}
