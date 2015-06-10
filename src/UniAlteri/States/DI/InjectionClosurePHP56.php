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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\DI;

/**
 * Class InjectionClosurePHP56
 * Injection Closure container used with PHP 5.6 (with Floc operator) to extract and manipulate all
 * methods of a stated class * in the proxy. These containers implement also a "static" mechanism to allow developers to use
 * clean static var in these functions.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @deprecated  Removed in version v2.0, useless with PHP7+ and \Closure::call
 *
 * @api
 */
class InjectionClosurePHP56 extends InjectionClosure
{
    /**
     * Execute the closure.
     *
     * @param array $args
     *
     * @return mixed
     */
    public function invoke(array &$args)
    {
        $closure = $this->closure;

        return $closure(...$args);
    }
}
