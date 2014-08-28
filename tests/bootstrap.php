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
 * @subpackage  Tests
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.9
 */

defined('RUN_CLI_MODE')
    || define('RUN_CLI_MODE', true);

defined('PHPUNIT')
    || define('PHPUNIT', true);

defined('UA_STATES_TEST_PATH')
    || define('UA_STATES_TEST_PATH', __DIR__.DIRECTORY_SEPARATOR.'UniAlteri'.DIRECTORY_SEPARATOR.'Tests');

ini_set('memory_limit', '128M');

require_once (dirname(__DIR__).DIRECTORY_SEPARATOR.'autoloader_psr0.php');

//Update included Path for spl autoload
set_include_path(
    __DIR__
    .PATH_SEPARATOR
    .get_include_path()
);

date_default_timezone_set('Europe/Paris');

error_reporting(E_ALL | E_STRICT);
