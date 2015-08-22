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
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Loader;

/**
 * Class IncludePathManager
 * Object to manage set_included_path and unit test this section.
 * It used by the loader to configure include_path.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @deprecated  Removed since 2.0
 *
 * @api
 */
class IncludePathManager implements IncludePathManagerInterface
{
    /**
     * To set the include_path configuration option.
     *
     * @param array|string[] $paths (paths must be split into an array)
     *
     * @return array|string[] old paths
     *
     * @throws Exception\IllegalArgument if the argument $paths is not an array of string
     */
    public function setIncludePath($paths)
    {
        if (!is_array($paths) && !$paths instanceof \ArrayObject) {
            throw new Exception\IllegalArgument('Error, $paths is not an array of string');
        }

        if ($paths instanceof \ArrayObject) {
            //Convert to array
            $paths = $paths->getArrayCopy();
        }

        return explode(PATH_SEPARATOR, set_include_path(implode(PATH_SEPARATOR, $paths)));
    }

    /**
     * To get the current include_path configuration option.
     *
     * @return array|string[] (paths must be split into an array)
     */
    public function getIncludePath()
    {
        return explode(PATH_SEPARATOR, get_include_path());
    }
}
