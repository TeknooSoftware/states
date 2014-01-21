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
 * to license@centurion-project.org so we can send you a copy immediately.
 *
 * @category    States
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @version     $Id$
 */

/**
 * @category    States
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\DI;

interface InjectionClosureInterface{
    /**
     * Register a DI container for this object
     * @param \UniAlteri\States\DI\ContainerInterface $container
     */
    public function setDIContainer(\UniAlteri\States\DI\ContainerInterface $container);

    /**
     * Return the DI Container used for this object
     * @return \UniAlteri\States\DI\ContainerInterface
     */
    public function getDIContainer();

    /**
     * Execute the closure
     * @return mixed
     */
    public function __invoke();

    /**
     * Return the closure contained into this
     * @param \Closure $closure
     * @return $this
     */
    public function setClosure(\Closure $closure);

    /**
     * Return the closure contained into this
     * @return \Closure
     */
    public function getClosure();

    /**
     * To allow the closure to save a static property, to allow developer to not use "static" key word into the closure
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function saveStaticProperty($name, $value);

    /**
     * Remove a static property
     * @param string $name
     * @return $this
     */
    public function deleteStaticProperty($name);

    /**
     * Return to the closure a static property
     * @param string $name
     * @return mixed
     */
    public function getStaticProperty($name);

    /**
     * Check if a static property is stored
     * @param string $name
     * @return boolean
     */
    public function testStaticProperty($name);
}