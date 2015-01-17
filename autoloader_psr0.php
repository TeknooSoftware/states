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
 * @subpackage  Bootstraping
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.9
 *
 * Default bootstrap to load file of the lib Uni Alteri States
 */

//Shortcut for DIRECTORY_SEPARATOR
defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);

//Update included path to load files of the lib States
set_include_path(
    __DIR__.DS.'lib'
    .PATH_SEPARATOR
    .get_include_path()
);

//Use autoloader of composer is it is available
if (file_exists(__DIR__.'/vendor/autoload.php')) {
    require_once __DIR__.'/vendor/autoload.php';
} elseif(file_exists(__DIR__ . '/../../autoload.php')) {
    require_once __DIR__ . '/../../autoload.php';
} else {
    //Use  spl autoloader, UA States lib uses PSR-0 standards
    spl_autoload_register(
        function ($className) {
            //From PSR-0, performs the file name from the class name and namespace
            $filePath = str_replace(array('\\', '_'), '/', $className).'.php';
            //Get the list of include paths
            $includePathArray = explode(PATH_SEPARATOR, get_include_path());
            foreach ($includePathArray as $includePath) {
                //Check for each directory if the required file exist
                $path = $includePath.DS.$filePath;
                if (is_readable($path)) {
                    //class file found, load it
                    include_once($path);
                    $included = class_exists($className, false);
                    return $included;
                }
            }

            return false;
        },
        true
    );
}
