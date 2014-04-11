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

use UniAlteri\States\Loader;
use UniAlteri\States\Loader\Exception;

/**
 * Class VirtualIncludePathManager
 * Mock Include Path manager to unit test Loader behavior
 *
 * @package     States
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class VirtualIncludePathManager implements Loader\IncludePathManagerInterface
{
    /**
     * Current included paths, not use current include path to avoid conflict with the testing environment
     * @var string[]
     */
    protected $_paths = array();

    /**
     * History of all change
     * @var array
     */
    protected $_allChangePaths = array();

    /**
     * Sets the include_path configuration option
     * @param string[] $paths (paths must be split into an array)
     * @return $this
     * @throws Exception\IllegalArgument if the argument $paths is not an array of string
     */
    public function setIncludePath($paths)
    {
        if (!is_array($paths) && !$paths instanceof \ArrayObject) {
            throw new Exception\IllegalArgument('Error, $paths is not an array of string');
        }

        //Simulate the behavior of a real manager
        $old = $this->_paths;
        $this->_paths = $paths;
        $this->_allChangePaths[] = $paths;
        return $old;
    }

    /**
     * Gets the current include_path configuration option
     * @return string[] (paths must be split into an array)
     */
    public function getIncludePath()
    {
        return $this->_paths;
    }

    /**
     * To reset history of changes
     * Method added, called by Unit TestCase to reinitialize the manager before each tests
     */
    public function resetAllChangePath()
    {
        $this->_allChangePaths = array();
    }

    /**
     * Get all change path
     * @return array
     */
    public function getAllChangePaths()
    {
        return $this->_allChangePaths;
    }
}