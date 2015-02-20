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
 * @version     1.0.3
 */

namespace UniAlteri\Tests\Support;

use UniAlteri\States\DI;
use UniAlteri\States\DI\Exception;
use UniAlteri\States\DI\InjectionClosureInterface;
use UniAlteri\States\Proxy\ProxyInterface;

/**
 * Class MockInjectionClosure
 * Mock injection closure to tests the trait state and proxies behaviors.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MockInjectionClosure implements DI\InjectionClosureInterface
{
    /**
     * @var \Closure
     */
    protected $closure = null;

    /**
     * @var ProxyInterface
     */
    protected $proxy = null;

    /**
     * @var array
     */
    protected $properties = array();

    /**
     * To register a DI container for this object.
     *
     * @param DI\ContainerInterface $container
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        //Not used in tests

        return $this;
    }

    /**
     * To return the DI Container used for this object.
     *
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        //Not used in tests
    }

    /**
     * Execute the closure.
     *
     * @param array $args
     *
     * @return mixed
     */
    public function invoke(array &$args)
    {
        //Simulate the behavior of a real injection closure class : call the closure with args
        return \call_user_func_array($this->closure, $args);
    }


    /**
     * Return the closure contained into this.
     *
     * @param \Closure $closure
     *
     * @return $this
     */
    public function setClosure($closure)
    {
        $this->closure = $closure;

        return $this;
    }

    /**
     * Return the closure contained into this.
     *
     * @return \Closure
     */
    public function getClosure()
    {
        return $this->closure;
    }

    /**
     * To define the proxy linked with this closure
     *
     * @param ProxyInterface $proxy
     *
     * @return $this
     */
    public function setProxy(ProxyInterface $proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * To return the proxy used into $this.
     *
     * @return \Closure
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * To allow the closure to save a static property,
     * to allow developer to not use "static" key word into the closure.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function saveProperty($name, $value)
    {
        //Simulate real property management
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * Remove a static property.
     *
     * @param string $name
     *
     * @return $this
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function deleteProperty($name)
    {
        //Simulate real property management
        if (isset($this->properties[$name])) {
            unset($this->properties[$name]);
        }

        return $this;
    }

    /**
     * Return to the closure a static property.
     *
     * @param string $name
     *
     * @return mixed
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function getProperty($name)
    {
        //Simulate real property management
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        }

        return;
    }

    /**
     * Check if a static property is stored.
     *
     * @param string $name
     *
     * @return boolean
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function testProperty($name)
    {
        //Simulate real property management
        return isset($this->properties[$name]);
    }
}
