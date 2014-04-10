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
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\States\Proxy;

/**
 * Class Standard
 * Variant of default Proxy class to use in stated class when no proxy are defined in these classes.
 *
 * A stated object is a proxy, configured for its stated class, with its different stated objects.
 * It is a proxy because, by default, all calls are redirected to enabled states.
 * $this in all methods of the stated object (also of states' methods) points the proxy object.
 *
 * The library creates an alias with the proxy class name and this default proxy
 * to simulate a dedicated proxy to this class
 *
 * This proxy is a variant of the default proxy to allow developer to create an instance a stated class
 * like another class with the operator new
 *
 * @package     States
 * @subpackage  Proxy
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Integrated extends Standard
{
    /**
     * Class name of the factory to use in set up to initialize this object
     * @var string
     */
    protected static $_startupFactoryClassName = '\UniAlteri\States\Factory\StandardStartupFactory';

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
     * @api
     * @throws Exception\IllegalFactory
     * @throws Exception\UnavailableFactory
     */
    protected function _initializeObjectWithFactory()
    {
        if (!class_exists(static::$_startupFactoryClassName, false)) {
            throw new Exception\UnavailableFactory('Error, the startup factory is not available');
        }

        $interfacesImplementedArray = array_flip(
            class_implements(static::$_startupFactoryClassName)
        );

        if (!isset($interfacesImplementedArray['UniAlteri\States\Factory\StartupFactoryInterface'])) {
            throw new Exception\IllegalFactory('Error, the startup factory does not implement the startup interface');
        }

        call_user_func_array(
            array(static::$_startupFactoryClassName, 'forwardStartup'),
            array(
                $this
            )
        );
    }
}