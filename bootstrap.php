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
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);

//Update included Path for spl autoload
set_include_path(
    __DIR__.DS.'lib'
    .PATH_SEPARATOR
    .__DIR__.DS.'vendor'.DS.'pimple'.DS.'pimple'.DS.'lib'
    .PATH_SEPARATOR
    .get_include_path()
);

//Use default spl autoloader, UA States lib use PSR-0 standards
spl_autoload_register(
    function ($className) {
        $path = str_replace(array('\\', '_'), '/', $className).'.php';
        $a = get_include_path();
        include_once($path);
        $included = class_exists($className, false);
        return $included;
    },
    true
);