<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

namespace demo;

error_reporting(E_ALL);

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
