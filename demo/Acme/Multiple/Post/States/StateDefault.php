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
 * Default State for a post message
 *
 * @package     States
 * @subpackage  Demo
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class StateDefault extends States\AbstractState
{
    /**
     * Return the name of the post, or empty if no body has been defined
     * @return string
     */
    public function getTitle()
    {
        if (!empty($this->title)) {
            return $this->title;
        }

        return '';
    }

    /**
     * Define the title of this post
     * @param  string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Define the body of this post
     * @param  string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
}
