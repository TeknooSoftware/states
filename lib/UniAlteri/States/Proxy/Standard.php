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
 * Default Proxy class to use in stated class when no proxy are defined in these classes.
 *
 * A stated object is a proxy, configured for its stated class, with its differents states objects.
 * It is a proxy because, by default, all calls are redirected to enabled states.
 * $this in all methods of the stated object (also of states'methods) points the proxy object.
 *
 * The library create an alias with the class's proxy name and this default proxy
 * to simulate a dedicated proxy to this class
 */
class Standard implements ProxyInterface
{
    use TraitProxy;

    /**
     * Class name of the factory to use during set up to initialize this object
     * @var string
     */
    protected static $startupFactoryClassName = null;

    /**
     * Factory'id to use for the current stated class
     * @var string
     */
    protected static $_factoryIdentifier = null;

    /**
     * Default constructor used to initialize the stated object with its factory
     * @param array $params
     */
    public function __construct($params = array())
    {
        $this->_initializeObjectWithFactory();
    }

    /**
     * Method called by constructor to initialize this object from the stated class's factory
     */
    protected function _initializeObjectWithFactory()
    {

    }
}