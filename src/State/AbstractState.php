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
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\States\State;

/**
 * Class AbstractState
 * Standard  implementation of the state interface, representing states entities in stated class.
 *
 * Default implementation of the state interface, representing states entities in stated class.
 * A trait implementation has been chosen to allow developer to write theirs owns factory, extendable from any class.
 *
 * Objects implementing this interface must
 * return a usable closure via the method getClosure() for the required method. This method must able to be rebinded
 * by the Closure api (The proxy use \Closure::call() to rebind self and $this). These objects must also provide a
 * \ReflectionMethod instance for theirs state's methods and check also if the proxy instance can access to a private
 * or protected method.
 *
 * State's methods are not directly used by the proxy instance. They are a builder to create the closure, they must
 * return them self the closure. So, writing state differs from previous version, example :
 *
 *      <method visibility> function <method name>(): \Closure
 *      {
 *          return function($arg1, $arg2) {
 *              //your code
 *          };
 *      }
 *      method visibility : public/protected/private, visibility used in the proxy instance, for your method
 *      method name: a string, used in the proxy, for your method.
 *
 * Contrary to previous versions of this library, methods of states's object are not directly converted into a \Closure.
 * Since 7.0, \Closure created from the Reflection Api can not be rebinded to an another class (only rebind of $this
 * is permitted), so the feature \Closure::call() was not usable. Since 7.1, rebind $this for this special closure
 * is also forbidden.
 *
 * @see StateInterface
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractState implements StateInterface
{
    use StateTrait;
}
