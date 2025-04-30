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
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

namespace demo\Acme\Article\States;

use demo\Acme\Article\Article;
use Teknoo\States\State\AbstractState;

/**
 * State StateDefault.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     https://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin Article
 */
class StateDefault extends AbstractState
{
    public function getTitle()
    {
        /**
         * Return the title of this article.
         *
         * @return string
         */
        return fn () => $this->getAttribute('title');
    }

    /**
     * To know if the article is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        /**
         * Return the title of this article.
         *
         * @return string
         */
        return function (): bool {
            $isPublished = $this->getAttribute('is_published');

            return !empty($isPublished);
        };
    }
}
