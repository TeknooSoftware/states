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
 * @subpackage  Loader
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Loader;

/**
 * Interface IncludedPathManagementInterface
 * @package UniAlteri\States\Loader
 * @api
 *
 * Interface to define manager object to manage set_included_path and unit test this section
 */
interface IncludePathManagerInterface
{
    /**
     * Sets the include_path configuration option
     * @param array|string[] $paths (paths must be split into an array)
     * @return array|string[] old paths
     * @throws Exception\IllegalArgument if the argument $paths is not an array of string
     */
    public function setIncludePath($paths);

    /**
     * Gets the current include_path configuration option
     * @return array|string[] (paths must be split into an array)
     */
    public function getIncludePath();
}