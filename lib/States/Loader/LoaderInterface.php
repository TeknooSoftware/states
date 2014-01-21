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

namespace UniAlteri\States\Loader;

interface LoaderInterface{
    /**
     * Method to add a path on the list of location where find class
     * @param string $path
     * @return $this
     */
    public function addIncludePath($path);

    /**
     * Register namespace with a location of class
     * @param string $namespace
     * @param string $path
     * @return $this
     */
    public function registerNamespace($namespace, $path);

    /**
     * Method called to load a class of the specific class, proxy and state.
     * @param string $className
     * @return boolean
     */
    public function loadClass($className);
}