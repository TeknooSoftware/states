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
 */

namespace UniAlteri\States\Loader;

use Composer\Autoload\ClassLoader;

/**
 * Class FinderComposer
 * Default implementation of the finder. It is used with this library to find from each stated class
 * all states and the proxy. It needs an instance of the Composer Loader to find php classes and load them.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @internal
 */
class FinderComposer extends FinderStandard implements FinderInterface
{
    /**
     * Default proxy class to use when there are no proxy class.
     *
     * @var string
     */
    protected $defaultProxyClassName = '\UniAlteri\States\Proxy\Standard';

    /**
     * @var ClassLoader
     */
    private $composerInstance;

    /**
     * Initialize finder.
     *
     * @param string      $statedClassName
     * @param string      $pathString
     * @param ClassLoader $composerInstance
     */
    public function __construct($statedClassName, $pathString, ClassLoader $composerInstance = null)
    {
        $this->statedClassName = $statedClassName;
        $this->pathString = $pathString;
        $this->composerInstance = $composerInstance;
    }

    /**
     * Check if a required class exists, and if not, try to load it via composer and recheck.
     * Can not use directly autoloader with class_exists. Sometimes it's behavior is non consistent
     * with spl_autoload_register.
     *
     * @param string $className
     *
     * @return bool
     */
    private function testClassExists($className)
    {
        if (class_exists($className, false)) {
            return true;
        }

        return $this->composerInstance->loadClass($className) && class_exists($className, false);
    }

    /**
     * To load the required state object of the stated class.
     *
     * @internal
     *
     * @param string $stateName
     *
     * @return string
     *
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function loadState($stateName)
    {
        $stateClassName = $this->statedClassName.'\\'.FinderInterface::STATES_PATH.'\\'.$stateName;
        if (!$this->testClassExists($stateClassName)) {
            throw new Exception\UnavailableState(
                sprintf('Error, the state "%s" is not available', $stateName)
            );
        }

        return $stateClassName;
    }

    /**
     * To search and load the proxy class for this stated class.
     * If the class has not proxy, load the default proxy for this stated class.
     *
     * @internal
     *
     * @return string
     */
    public function loadProxy()
    {
        //Build the class name
        $classPartName = $this->getClassedName($this->statedClassName);
        $proxyClassName = $this->statedClassName.'\\'.$classPartName;

        if (!$this->testClassExists($proxyClassName)) {
            //The stated class has not its own proxy, reuse the standard proxy, as an alias
            class_alias($this->defaultProxyClassName, $proxyClassName, true);
            class_alias($this->defaultProxyClassName, $this->statedClassName, false);
        } else {
            //To access this class directly without repeat the stated class name
            if (!class_exists($this->statedClassName, false)) {
                class_alias($proxyClassName, $this->statedClassName, false);
            }
        }

        return $proxyClassName;
    }
}
