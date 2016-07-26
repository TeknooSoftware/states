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
namespace demo\Acme\Multiple\Post\States;

use Teknoo\States\State\AbstractState;

/**
 * State Deleted
 * Default State for a post message.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class StateDefault extends AbstractState
{
    /**
     * Return the name of the post, or empty if no body has been defined.
     *
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
     * Define the title of this post.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Define the body of this post.
     *
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
}
