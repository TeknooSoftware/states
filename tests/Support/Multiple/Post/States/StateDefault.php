<?php

/**
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\Tests\Support\Multiple\Post\States;

use Teknoo\States\State\AbstractState;

/**
 * State Deleted
 * Default State for a post message
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 * @mixin Post
 */
class StateDefault extends AbstractState
{
    public function getTitle()
    {
        /*
         * Return the name of the post, or empty if no body has been defined.
         *
         * @return string
         */
        return function () {
            if (!empty($this->title)) {
                return $this->title;
            }

            return '';
        };
    }

    public function setTitle()
    {
        /*
         * Define the title of this post.
         *
         * @param string $title
         *
         * @return $this
         */
        return function ($title) {
            $this->title = $title;

            return $this;
        };
    }

    public function setBody()
    {
        /*
         * Define the body of this post.
         *
         * @param string $body
         *
         * @return $this
         */
        return function ($body) {
            $this->body = $body;
        };
    }
}
