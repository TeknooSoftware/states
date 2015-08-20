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
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Proxy;

/**
 * Trait MagicCallTrait
 * Trait to use PHP magic's calls (http://php.net/manual/en/language.oop5.magic.php) with stated classes
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/license/mit         MIT License
 * @license     http://teknoo.it/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @method mixed findMethodToCall($name, $args)
 */
trait MagicCallTrait
{
    /*******************
     * Methods Calling *
     *******************/

    /**
     * To invoke an object as a function.
     * @api
     *
     * @param mixed ...$args
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __invoke(...$args)
    {
        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /*******************
     * Data Management *
     *******************/

    /**
     * To get a property of the object.
     * @api
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __get(\string $name)
    {
        $args = [$name];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * To test if a property is set for the object.
     * @api
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __isset(\string $name)
    {
        $args = [$name];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * To update a property of the object.
     * @api
     *
     * @param string $name
     * @param string $value
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __set(\string $name, $value)
    {
        $args = [$name, $value];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * To remove a property of the object.
     * @api
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception\MethodNotImplemented if any enabled state implement the required method
     */
    public function __unset(\string $name)
    {
        $args = [$name];

        return $this->findMethodToCall(__FUNCTION__, $args);
    }

    /**
     * To transform the object to a string
     * You cannot throw an exception from within a __toString() method. Doing so will result in a fatal error.
     * @api
     *
     * @return mixed
     */
    public function __toString(): \string
    {
        try {
            $args = [];

            return $this->findMethodToCall(__FUNCTION__, $args);
        } catch (\Exception $e) {
            return '';
        }
    }
}
