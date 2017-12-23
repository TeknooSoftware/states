<?php

declare(strict_types=1);

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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\States\State;

use Teknoo\States\Proxy\ProxyInterface;

/**
 * Interface StateInterface
 * Interface to define class representing states entities for a stated class.
 *
 * Objects implementing this interface must find, bind and execute closure via the method executeClosure() for the
 * required method. (Rebind must use \Closure::call() to rebind static, self and $this or rebindTo()).
 *
 * Objects must follow instruction passed to executeClosure() and manage the visibility of the method and not allow
 * executing a private method from an outside call.
 *
 * Result must be injected to the proxy by using the callback passed to executeClosure(). It's allowed to execute a
 * method without inject the result into the proxy instance to allow developers to call several methods. But you can
 * only inject one result by call. (Several implementations available at a same time is forbidden by the proxy
 * interface).
 *
 * Warning, you can not use the Reflection API to extract closure from a class's method, rebind is forbidden since 7.1
 * for self and $this, only for self for 7.0.
 *
 * Static method are not managed (a class can not have a state, only it's instance).
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
interface StateInterface
{
    /**
     * Const to get a closure into a public scope.
     */
    const VISIBILITY_PUBLIC = 'public';

    /**
     * Const to get a closure into a protected scope.
     */
    const VISIBILITY_PROTECTED = 'protected';

    /**
     * Const to get a closure into a private scope.
     */
    const VISIBILITY_PRIVATE = 'private';

    /**
     * To initialize this state.
     *
     * @api
     *
     * @param bool   $privateMode     : To know if the private mode is enable or not for this state, see isPrivateMode
     * @param string $statedClassName : To know the full qualified stated class name of the object owning this container
     */
    public function __construct(bool $privateMode, string $statedClassName);

    /**
     * To find, bind (with the passed proxy instance) and execute a closure of the required method, i
     * n the required scope (check from the visibility of the method) :
     *  Public method : Method always available
     *  Protected method : Method available only for this stated class's methods (method present in this state or
     *      another state) and its children
     *  Private method : Method available only for this stated class's method (method present in this state or another
     *      state) and not for its children.
     *
     * Result must be injected to the proxy by using the callback passed to executeClosure(). It's allowed to execute a
     * method without inject the result into the proxy instance to allow developers to call several methods. But you can
     * only inject one result by call. (Several implementations available at a same time is forbidden by the proxy
     * interface).
     *
     * Warning, you can not use the Reflection API to extract closure from a class's method, rebind is forbidden since
     * 7.1 for self and $this, only for self for 7.0.
     *
     * Static method are not managed (a class can not have a state, only it's instance).
     *
     * @param ProxyInterface $object the instance to use to bind with an object's scope the closure
     * @param string      $methodName
     * @param array       $arguments to pass to the closure
     * @param string      $requiredScope     self::VISIBILITY_PUBLIC|self::VISIBILITY_PROTECTED|self::VISIBILITY_PRIVATE
     * @param string|null $statedClassOrigin
     * @param callable $returnCallback Method to call if the closure has been found and called, to pass the result
     *
     * @return StateInterface
     */
    public function executeClosure(
        ProxyInterface $object,
        string &$methodName,
        array &$arguments,
        string &$requiredScope,
        string &$statedClassOrigin,
        callable &$returnCallback
    );
}
