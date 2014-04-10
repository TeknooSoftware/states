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

use \UniAlteri\States\DI;
use \UniAlteri\States\DI\Exception;

class VirtualInjectionClosure implements DI\InjectionClosureInterface
{
    /**
     * @var \Closure
     */
    protected $_closure = null;

    /**
     * @var array
     */
    protected $_properties = array();

    /**
     * Register a DI container for this object
     * @param DI\ContainerInterface $container
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
    }

    /**
     * Return the DI Container used for this object
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
    }

    /**
     * Execute the closure
     * @return mixed
     */
    public function __invoke()
    {
        return \call_user_func_array($this->_closure, \func_get_args());
    }

    /**
     * Return the closure contained into this
     * @param \Closure $closure
     * @return $this
     */
    public function setClosure($closure)
    {
        $this->_closure = $closure;
        return $this;
    }

    /**
     * Return the closure contained into this
     * @return \Closure
     */
    public function getClosure()
    {
        return $this->_closure;
    }

    /**
     * To allow the closure to save a static property,
     * to allow developer to not use "static" key word into the closure
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function saveProperty($name, $value)
    {
        $this->_properties[$name] = $value;
        return $this;
    }

    /**
     * Remove a static property
     * @param string $name
     * @return $this
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function deleteProperty($name)
    {
        if (isset($this->_properties[$name])) {
            unset($this->_properties[$name]);
        }

        return $this;
    }

    /**
     * Return to the closure a static property
     * @param string $name
     * @return mixed
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function getProperty($name)
    {
        if (isset($this->_properties[$name])) {
            return $this->_properties[$name];
        }

        return null;
    }

    /**
     * Check if a static property is stored
     * @param string $name
     * @return boolean
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function testProperty($name)
    {
        return isset($this->_properties[$name]);
    }
}