<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
namespace demo\Acme\Article\States;

use demo\Acme\Article\Article;
use Teknoo\States\State\AbstractState;

/**
 * State Draft
 * State for an article not published.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin Article
 */
class Draft extends AbstractState
{
    public function publishing()
    {
        /**
         * Publish this article.
         */
        return function (): void {
            $this->setAttribute('is_published', true);
            //Switch to Published State, so this state will be not available for next operations
            $this->disableState(Draft::class);
            $this->enableState(Published::class);
        };
    }

    public function setTitle()
    {
        /**
         * Define the title of this article.
         *
         * @param string $title
         */
        return function ($title): void {
            $this->setAttribute('title', $title);
        };
    }

    public function setBody()
    {
        /**
         * Define the body of this article.
         *
         * @param string $body
         */
        return function ($body): void {
            $this->setAttribute('body', $body);
        };
    }


    public function getBodySource()
    {
        /**
         * Get the body source.
         *
         * @return string
         */
        return fn() => $this->getAttribute('body');
    }
}
