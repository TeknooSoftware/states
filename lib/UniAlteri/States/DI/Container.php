<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @subpackage  DI
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.9
 */

namespace UniAlteri\States\DI;

/**
 * Class Container
 * Default Dependency Injection Container, built on Pimple, used in this library.
 *
 * @package     States
 * @subpackage  DI
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com> *
 */
class Container extends \Pimple\Container implements ContainerInterface
{
    /**
     * Test if the identifier respects the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     * @param  string                $name
     * @return bool
     * @throws Exception\IllegalName when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    protected function validateName($name)
    {
        if (is_string($name) && 1 == preg_match('#^[a-zA-Z_][a-zA-Z0-9_\-]*#iS', $name)) {
            return true;
        }

        throw new Exception\IllegalName('Error, the identifier is invalid');
    }

    /**
     * To support the object cloning : All registries must be cloning, but not their values
     */
    public function __clone()
    {
        /**
     * Do nothing, Pimple uses standard arrays, they are automatically cloned by php (but not theirs values)
     */
    }

    /**
     * Call an entry of the container to retrieve an instance
     *
     * @param  string                    $name : identifier of the instance
     * @return mixed
     * @throws Exception\InvalidArgument if the identifier is not defined
     * @throws Exception\IllegalName     when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function get($name)
    {
        $this->validateName($name);

        try {
            return $this[$name];
        } catch (\InvalidArgumentException $e) {
            throw new Exception\InvalidArgument($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Register a new shared object into container (the same object is returned at each call)
     * @param  string                   $name
     * @param  object|callable|string   $instance
     * @return $this
     * @throws Exception\ClassNotFound  if $instance is a non-existent class name
     * @throws Exception\IllegalService if the $instance is not an invokable object, or a function, or an existent class
     * @throws Exception\IllegalName    when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function registerInstance($name, $instance)
    {
        $this->validateName($name);

        if (\is_string($instance)) {
            //Load the class and build a new object of this class
            if (\class_exists($instance, false)) {
                $this[$name] = new $instance();
            } else {
                throw new Exception\ClassNotFound(sprintf('The class "%s" is not available', $instance));
            }
        } elseif (is_object($instance)) {
            //For callables and objects, register them
            $this[$name] = $instance;
        } else {
            throw new Exception\IllegalService(
                sprintf('Error, the instance for "%s" is illegal', $name)
            );
        }

        return $this;
    }

    /**
     * Register a new service into container (a new instance is returned at each call)
     * @param  string                   $name     : interface name, class name, alias
     * @param  object|callable|string   $instance
     * @return $this
     * @throws Exception\ClassNotFound  if $instance is a non-existent class name
     * @throws Exception\IllegalService if the $instance is not an invokable object, or a function, or an existent class
     * @throws Exception\IllegalName    when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function registerService($name, $instance)
    {
        $this->validateName($name);

        if (\is_object($instance)) {
            //Add the object as service into container
            if (\method_exists($instance, '__invoke')) {
                $this[$name] = $this->factory($instance);
            } else {
                throw new Exception\IllegalService(
                    sprintf('Error, the service for "%s" is not an invokable object', $name)
                );
            }
        } elseif (\is_string($instance)) {
            //Class, check if it is loaded
            if (\class_exists($instance, false)) {
                //Write a new closure to build a new instance of this class, and use it as service
                $this[$name] = $this->factory(function () use ($instance) {
                    return new $instance();
                });
            } else {
                throw new Exception\ClassNotFound(
                    sprintf('The class "%s" is not available', $instance)
                );
            }
        } else {
            throw new Exception\IllegalService(
                sprintf('Error, the service for "%s" is illegal', $name)
            );
        }

        return $this;
    }

    /**
     * Test if an entry is already registered
     * @param  string                $name
     * @return boolean
     * @throws Exception\IllegalName when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function testEntry($name)
    {
        $this->validateName($name);

        return isset($this[$name]);
    }

    /**
     * Remove an entry from the container
     * @param  string                $name
     * @return $this
     * @throws Exception\IllegalName when the identifier does not respect the pattern [a-zA-Z_][a-zA-Z0-9_\-]*
     */
    public function unregister($name)
    {
        $this->validateName($name);
        unset($this[$name]);

        return $this;
    }

    /**
     * Configure the container from an array (provided by an INI file or other)
     * @param  array|\ArrayObject        $params
     * @return mixed
     * @throws Exception\InvalidArgument when $params is not an array or an ArrayAccess object
     */
    public function configure($params)
    {
        if (!is_array($params) && !$params instanceof \ArrayAccess) {
            throw new Exception\InvalidArgument('Error, $params must be an array or an ArrayAccess');
        }

        if (isset($params['services'])) {
            foreach ($params['services'] as $name => $instance) {
                $this->registerService($name, $instance);
            }
        }

        if (isset($params['instances'])) {
            foreach ($params['instances'] as $name => $instance) {
                $this->registerInstance($name, $instance);
            }
        }
    }

    /**
     * List all entries of this container
     * @return string[]
     */
    public function listDefinitions()
    {
        return $this->keys();
    }
}
