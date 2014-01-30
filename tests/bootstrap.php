<?php
/**
 * Tests
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

defined('RUN_CLI_MODE')
    || define('RUN_CLI_MODE', true);

defined('PHPUNIT')
    || define('PHPUNIT', true);

ini_set('memory_limit', '128M');

//Update included Path for spl autoload
set_include_path(
    __DIR__
    .PATH_SEPARATOR
    .dirname(__DIR__).DIRECTORY_SEPARATOR.'lib'
    .PATH_SEPARATOR
    .get_include_path()
);

//Use default spl autoloader, States lib use PSR-0 standard
spl_autoload_extensions('.php');
spl_autoload_register();
