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
 * @subpackage  Demo
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     1.0.2
 */
namespace demo\Acme\Multiple\Post\States;

use UniAlteri\States\States;

/**
 * State Deleted
 * State for a post when a message is deleted
 *
 * @package     States
 * @subpackage  Demo
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Deleted extends States\AbstractState
{
    /**
     * Return the body of the post. Return always Message deleted
     * @return string
     */
    public function getMessage()
    {
        return 'Message deleted';
    }
}
