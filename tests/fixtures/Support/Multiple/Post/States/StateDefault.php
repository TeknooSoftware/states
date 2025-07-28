<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\Support\Multiple\Post\States;

use Teknoo\States\State\AbstractState;

/**
 * State Deleted
 * Default State for a post message
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
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
        return function ($title): static {
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
        return function ($body): void {
            $this->body = $body;
        };
    }
}
