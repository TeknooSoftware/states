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
 * @package     Factory
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @version     $Id$
 */

/**
 * @category    States
 * @package     Factory
 * @copyright   Copyright (c) 2009-2013 Uni Alteri (http://uni-alteri.com)
 * @license     http://uni-alteri.com/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Factory;

interface FactoryInterface{
    const OBJECT_LOADER_KEY = 'objectLoader';

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
     * Build a new instance of an object
     * @param mixed $arguments
     * @param string $stateName to build an object with a specific class
     * @return \UniAlteri\States\ObjectInterface
     */
    public function build($arguments=null, $stateName=null);
}