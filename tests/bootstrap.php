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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
defined('RUN_CLI_MODE')
    || define('RUN_CLI_MODE', true);

defined('PHPUNIT')
    || define('PHPUNIT', true);

defined('TK_STATES_TEST_PATH')
    || define('TK_STATES_TEST_PATH', __DIR__);

ini_set('memory_limit', '32M');

require_once __DIR__.'/../vendor/autoload.php';

date_default_timezone_set('UTC');

error_reporting(E_ALL | E_STRICT);

//Prevent error
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__.'/Support'),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $item) {
    chmod($item, 0755);
}
