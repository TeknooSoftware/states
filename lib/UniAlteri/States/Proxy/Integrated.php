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
 * @subpackage  Proxy
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Proxy;

/**
 * Class Standard
 * @package UniAlteri\States\Proxy
 * Variant of default Proxy class to use in stated class when no proxy are defined in these classes.
 *
 * A stated object is a proxy, configured for its stated class, with its different states objects.
 * It is a proxy because, by default, all calls are redirected to enabled states.
 * $this in all methods of the stated object (also of states'methods) points the proxy object.
 *
 * The library create an alias with the class's proxy name and this default proxy
 * to simulate a dedicated proxy to this class
 *
 * This proxy is a variant of the default proxy to allow developer to instanciate a stated class like another class
 * wit the operator new
 */
class Integrated extends Standard
{
    /**
     * Class name of the factory to use during set up to initialize this object
     * @var string
     */
    protected static $_startupFactoryClassName = null;

    /**
     * Factory'id to use for the current stated class
     * @var string
     */
    protected static $_factoryIdentifier = null;

    /**
     * Default constructor used to initialize the stated object with its factory
     * @throws Exception\IllegalFactory
     * @throws Exception\UnavailableFactory
     */
    public function __construct()
    {
        $this->_initializeProxy();
        $this->_initializeObjectWithFactory();
    }

    /**
     * Method called by constructor to initialize this object from the stated class's factory
     * @throws Exception\IllegalFactory
     * @throws Exception\UnavailableFactory
     */
    protected function _initializeObjectWithFactory()
    {
        if (!class_exists(static::$_startupFactoryClassName, false)) {
            throw new Exception\UnavailableFactory('Error, the startup factory is not available');
        }

        $interfacesImplementedArray = array_flip(
            class_implements(static::$_startupFactoryClassName, false)
        );

        if (!isset($interfacesImplementedArray['\UniAlteri\States\Factory\StartupFactoryInterface'])) {
            throw new Exception\IllegalFactory('Error, the startup factory does not implement the startup interface');
        }

        call_user_func_array(
            array(static::$_startupFactoryClassName, 'forwardStartup'),
            array(
                static::$_factoryIdentifier,
                $this
            )
        );
    }
}