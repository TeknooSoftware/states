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
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\Tests\Support;

use UniAlteri\States\DI;
use UniAlteri\States\Proxy;
use UniAlteri\States\Loader;

class VirtualFinder implements Loader\FinderInterface
{
    /**
     * @var DI\Container
     */
    protected $_container = null;

    /**
     * To not return the default state
     * @var bool
     */
    public $ignoreDefaultState = false;

    /**
     * Initialize finder
     * @param string $statedClassName
     * @param string $pathString
     */
    public function __construct($statedClassName, $pathString)
    {

    }

    /**
     * Register a DI container for this object
     * @param \UniAlteri\States\DI\ContainerInterface $container
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * Return the DI Container used for this object
     * @return \UniAlteri\States\DI\ContainerInterface
     */
    public function getDIContainer(){
        return $this->_container;
    }

    /**
     * List all available state object of the stated class
     * @return string[]
     */
    public function listStates()
    {
        if (empty($this->ignoreDefaultState)) {
            return array(
                'VirtualState1',
                Proxy\ProxyInterface::DEFAULT_STATE_NAME,
                'VirtualState2',
                'VirtualState3'
            );
        } else {
            return array(
                'VirtualState1',
                'VirtualState2',
                'VirtualState3'
            );
        }
    }

    /**
     * Load and build the required state object of the stated class
     * @param string $stateName
     * @return \UniAlteri\States\States\StateInterface
     */
    public function loadState($stateName)
    {
        return new VirtualState();
    }

    /**
     * Load and build a proxy object of the stated class
     * @param array $arguments argument for proxy
     * @return \UniAlteri\States\Proxy\ProxyInterface
     */
    public function loadProxy($arguments=null)
    {
        return new VirtualProxy($arguments);
    }
}