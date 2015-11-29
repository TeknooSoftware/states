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
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\States\Loader;

use Teknoo\States\DI;
use Teknoo\States\States;
use Teknoo\States\Proxy;

/**
 * Class FinderStandard
 * Default implementation of the finder. It is used with this library to find from each stated class
 * all states and the proxy.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @deprecated  Removed since 2.0 : Use FinderComposer instead of FinderStandard
 *
 * @api
 */
class FinderStandard implements FinderInterface
{
    /**
     * Current stated class's name.
     *
     * @var string
     */
    protected $statedClassName = null;

    /**
     * Folder/Phar of the stated class.
     *
     * @var string
     */
    protected $pathString = null;

    /**
     * DI Container to use with this finder.
     *
     * @var DI\ContainerInterface
     */
    protected $diContainer = null;

    /**
     * Default proxy class to use when there are no proxy class.
     *
     * @var string
     */
    protected $defaultProxyClassName = '\Teknoo\States\Proxy\Standard';

    /**
     * List of states already fetched by this finder.
     *
     * @var \ArrayObject
     */
    protected $statesNamesList = null;

    /**
     * Initialize finder.
     *
     * @param string $statedClassName
     * @param string $pathString
     */
    public function __construct($statedClassName, $pathString)
    {
        $this->statedClassName = $statedClassName;
        $this->pathString = $pathString;
    }

    /**
     * To register a DI container for this object.
     *
     * @param DI\ContainerInterface $container
     *
     * @return $this
     */
    public function setDIContainer(DI\ContainerInterface $container)
    {
        $this->diContainer = $container;

        return $this;
    }

    /**
     * To return the DI Containe.
     *
     * @throws Exception\IllegalProxy If the proxy class is not valid
     * @throws Exception\IllegalProxy If the proxy class is not validr used for this object.
     *
     * @return DI\ContainerInterface
     */
    public function getDIContainer()
    {
        return $this->diContainer;
    }

    /**
     * To get the canonical stated class name associated to this state.
     *
     * @return $this
     */
    public function getStatedClassName()
    {
        return $this->statedClassName;
    }

    /**
     * To list all available states of the stated class.
     *
     * @return string[]
     *
     * @throws Exception\UnavailablePath if the states' folder is not available
     * @throws Exception\UnReadablePath  if the states' folder is not readable
     */
    public function listStates()
    {
        if (!$this->statesNamesList instanceof \ArrayObject) {
            //Checks if states are stored into the standardized path
            $statesPath = $this->pathString.DIRECTORY_SEPARATOR.FinderInterface::STATES_PATH;
            if (!is_dir($statesPath)) {
                throw new Exception\UnavailablePath(
                    sprintf('Error, the path "%s" was not found', $statesPath)
                );
            }

            //Checks if the path is available, use error_reporting to not use @
            $oldErrorReporting = error_reporting(E_ALL & ~E_WARNING);
            $hD = opendir($statesPath);
            error_reporting($oldErrorReporting);
            if (false === $hD) {
                throw new Exception\UnReadablePath(
                    sprintf('Error, the path "%s" is not available', $statesPath)
                );
            }

            //Extracts all states (No check class exists)
            $statesNameArray = new \ArrayObject();
            while (false !== ($file = readdir($hD))) {
                switch ($file) {
                    case '.';
                    case '..';
                        break;
                    default:
                        if (strlen($file) - 4 == strrpos($file, '.php')) {
                            $stateName = substr($file, 0, -4);
                            $statesNameArray[] = $stateName;
                        }
                        break;
                }
            }

            closedir($hD);

            $this->statesNamesList = $statesNameArray;
        }

        return $this->statesNamesList;
    }

    /**
     * To load the required state object of the stated class.
     *
     * @param string $stateName
     *
     * @return string
     *
     * @throws Exception\UnReadablePath   if the stated file is not readable
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function loadState($stateName)
    {
        $stateClassName = $this->statedClassName.'\\'.FinderInterface::STATES_PATH.'\\'.$stateName;
        if (!class_exists($stateClassName, false)) {
            $statePath = $this->pathString
                            .DIRECTORY_SEPARATOR.FinderInterface::STATES_PATH
                            .DIRECTORY_SEPARATOR.$stateName.'.php';

            if (!is_readable($statePath)) {
                throw new Exception\UnReadablePath(
                    sprintf('Error, the state "%s" was not found', $stateName)
                );
            }

            include_once $statePath;
            $stateClassName = $this->statedClassName.'\\'.FinderInterface::STATES_PATH.'\\'.$stateName;
            if (!class_exists($stateClassName, false)) {
                throw new Exception\UnavailableState(
                    sprintf('Error, the state "%s" is not available', $stateName)
                );
            }
        }

        return $stateClassName;
    }

    /**
     * To return the list of parent php classes used by a state.
     *
     * @param string $stateName
     *
     * @return string[]
     *
     * @throws Exception\UnavailableState if the required state is not available
     */
    public function getStateParentsClassesNamesList($stateName)
    {
        $classNameList = [];

        //Get name of the parent class
        $parentClassName = get_parent_class($this->loadState($stateName));
        while (false !== $parentClassName) {
            $classNameList[] = $parentClassName;
            $parentClassName = get_parent_class($parentClassName);
        }

        return $classNameList;
    }

    /**
     * To load and build the required state object of the stated class.
     *
     * @param string $stateName
     *
     * @return States\StateInterface
     *
     * @throws Exception\UnReadablePath   if the state file is not readable
     * @throws Exception\UnavailableState if the required state is not available
     * @throws Exception\IllegalState     if the state object does not implement the interface
     */
    public function buildState($stateName)
    {
        //Load the state class if it is not already done
        $stateClassName = $this->loadState($stateName);

        $stateObject = new $stateClassName();
        if (!$stateObject instanceof States\StateInterface) {
            throw new Exception\IllegalState(
                sprintf(
                    'Error, the state "%s" does not implement the interface "States\StateInterface"',
                    $stateName
                )
            );
        }

        return $stateObject;
    }

    /**
     * To extract the class name from the stated class name with namespace.
     *
     * @param string $statedClassName
     *
     * @return string
     */
    protected function getClassedName($statedClassName)
    {
        $parts = explode('\\', $statedClassName);

        return array_pop($parts);
    }

    /**
     * To search and load the proxy class for this stated class.
     * If the class has not proxy, load the default proxy for this stated class.
     *
     * @return string
     *
     * @throws Exception\IllegalProxy If the proxy object does not implement Proxy/ProxyInterface
     */
    public function loadProxy()
    {
        //Build the class name
        $classPartName = $this->getClassedName($this->statedClassName);
        $proxyClassName = $this->statedClassName.'\\'.$classPartName;
        if (!class_exists($proxyClassName, false)) {
            //Build the class file path for the proxy (standardized into ProxyInterface)
            $proxyPath = $this->pathString.DIRECTORY_SEPARATOR.$classPartName.FinderInterface::PROXY_FILE_EXTENSION;

            if (!is_readable($proxyPath)) {
                //The stated class has not its own proxy, reuse the standard proxy, as an alias
                class_alias($this->defaultProxyClassName, $proxyClassName, true);
                class_alias($this->defaultProxyClassName, $this->statedClassName, false);

                return $proxyClassName;
            }

            include_once $proxyPath;
            if (!class_exists($proxyClassName, false)) {
                throw new Exception\IllegalProxy(
                    sprintf(
                        'Error, the proxy of "%s" must be called <StatedClassName>\'%s',
                        $this->statedClassName,
                        $classPartName
                    )
                );
            } else {
                //To access this class directly without repeat the stated class name
                class_alias($proxyClassName, $this->statedClassName, false);
            }
        }

        return $proxyClassName;
    }

    /**
     * To return the list of parents stated classes of the stated classes, library classes (Integrated proxy and
     * standard proxy are excluded).
     *
     * @return string[]
     *
     * @throws Exception\IllegalProxy If the proxy class is not valid
     */
    public function listParentsClassesNames()
    {
        //Build the class name
        $classPartName = $this->getClassedName($this->statedClassName);
        $proxyClassName = $this->statedClassName.'\\'.$classPartName;

        //Fetch parents classes and extract library classes
        if (class_exists($proxyClassName, false)) {
            $finalParentsClassesList = new \ArrayObject();

            $parentClassName = get_parent_class($proxyClassName);
            while (false !== $parentClassName && false === strpos($parentClassName, 'Teknoo\\States')) {
                if (class_exists($parentClassName, false)) {
                    $reflectionClassInstance = new \ReflectionClass($parentClassName);
                    if ($reflectionClassInstance->implementsInterface('Teknoo\\States\\Proxy\\ProxyInterface')
                        && false === $reflectionClassInstance->isAbstract()) {
                        $parentClassName = substr($parentClassName, 0, strrpos($parentClassName, '\\'));
                        $finalParentsClassesList[] = $parentClassName;
                    }
                }
                $parentClassName = get_parent_class($parentClassName);
            }

            return $finalParentsClassesList;
        }

        throw new Exception\IllegalProxy('Proxy class was not found');
    }

    /**
     * To load and build a proxy object for the stated class.
     *
     * @param array $arguments argument for proxy
     *
     * @return Proxy\ProxyInterface
     *
     * @throws Exception\IllegalProxy If the proxy object does not implement Proxy/ProxyInterface
     */
    public function buildProxy($arguments = null)
    {
        //Load the proxy if it is not already done
        $proxyClassName = $this->loadProxy();

        //Load an instance of this proxy and test if it implements the interface ProxyInterface
        $proxyObject = new $proxyClassName($arguments);
        if ($proxyObject instanceof Proxy\ProxyInterface) {
            return $proxyObject;
        }

        //Throw an error
        throw new Exception\IllegalProxy(
            sprintf('Error, the proxy of "%s" does not implement "Proxy\ProxyInterface"', $this->statedClassName)
        );
    }
}