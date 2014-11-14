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
 * Class InjectionClosure
 * Default Injection Closure container to use to extract and manipulate all methods of a stated class
 * in the proxy. These containers implement also a "static" mechanism to allow developers to use
 * clean static var in these functions.
 *
 * @package     States
 * @subpackage  DI
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com> *
 * @api
 */
class InjectionClosure implements InjectionClosureInterface
{
    /**
     * DI Container to use with this closure
     * @var ContainerInterface
     */
    protected $diContainer = null;

    /**
     * Closure hosted by this object
     * @var \Closure
     */
    protected $closure = null;

    /**
     * All statics properties
     * @var \ArrayObject
     */
    protected $properties = null;

    /**
     * To create a closure for a state with a support of persistent vars. (aka "static")
     * @param \Closure $closure to initialize container with a closure
     */
    public function __construct(\Closure $closure=null)
    {
        $this->properties = new \ArrayObject();
        if ($closure instanceof \Closure) {
            $this->setClosure($closure);
        }
    }

    /**
     * To To register a DI container for this object
     * @param  ContainerInterface $container
     * @return $this
     */
    public function setDIContainer(ContainerInterface $container)
    {
        $this->diContainer = $container;

        return $this;
    }

    /**
     * To return the DI Container used for this object
     * @return ContainerInterface
     */
    public function getDIContainer()
    {
        return $this->diContainer;
    }

    /**
     * Execute the closure
     * @return mixed
     */
    public function __invoke()
    {
        return \call_user_func_array($this->closure, \func_get_args());
    }

    /**
     * To define the closure contained into $this
     * @param  \Closure                  $closure
     * @return $this
     * @throws Exception\InvalidArgument when $closure is not an instance of \Closure
     */
    public function setClosure($closure)
    {
        if (!$closure instanceof \Closure) {
            throw new Exception\InvalidArgument('Error, $closure is not an instance of \Closure');
        }

        $this->closure = $closure;

        return $this;
    }

    /**
     * To return the closure contained into $this
     * @return \Closure
     */
    public function getClosure()
    {
        return $this->closure;
    }

    /**
     * To test if the name of the static property is valid
     * @param  string                $name
     * @return boolean
     * @throws Exception\IllegalName
     */
    protected function validatingName($name)
    {
        if (0 == preg_match('#^[a-zA-Z_][a-zA-Z0-9_]*$#iS', $name)) {
            throw new Exception\IllegalName(
                sprintf('Illegal name for static property "%s"', $name)
            );
        }

        return true;
    }

    /**
     * To allow the closure to save a static property,
     * to allow developer to not use "static" key word into the closure
     * @param  string $name
     * @param  mixed  $value
     * @return $this
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function saveProperty($name, $value)
    {
        if (true === $this->validatingName($name)) {
            $this->properties->{$name} = $value;
        }

        return $this;
    }

    /**
     * To remove a static property
     * @param  string $name
     * @return $this
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function deleteProperty($name)
    {
        if (true === $this->validatingName($name) && isset($this->properties->{$name})) {
            unset($this->properties->{$name});
        }

        return $this;
    }

    /**
     * To return to the closure a static property
     * @param  string $name
     * @return mixed
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function getProperty($name)
    {
        if (true === $this->validatingName($name) && isset($this->properties->{$name})) {
            return $this->properties->{$name};
        }

        return null;
    }

    /**
     * To check if a static property is stored
     * @param  string  $name
     * @return boolean
     * @throw Exception\IllegalName if the name does not respect the pattern [a-zA-Z_][a-zA-Z0-9_]*
     */
    public function testProperty($name)
    {
        if (true === $this->validatingName($name) && isset($this->properties->{$name})) {
            return true;
        }

        return false;
    }
}
