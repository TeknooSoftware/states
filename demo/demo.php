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
 * @copyright   Copyright (c) 2009-2019 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace demo;

error_reporting(E_ALL | E_STRICT);

defined('DS')
|| define('DS', DIRECTORY_SEPARATOR);

defined('SEPARATOR')
|| define('SEPARATOR', PHP_EOL.'--------------------------------------'.PHP_EOL);

defined('GREEN_COLOR')
|| define('GREEN_COLOR', "\033[0;32m");

defined('RED_COLOR')
|| define('RED_COLOR', "\033[0;31m");

defined('RESET_COLOR')
|| define('RESET_COLOR', "\033[0m");

//Loading lib States
return require_once dirname(__DIR__).DS.'vendor'.DS.'autoload.php';
