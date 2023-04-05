<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\Support\Article\Article;

use Teknoo\States\State\AbstractState;

/**
 * State StateDefault
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin Article
 */
class StateDefault extends AbstractState
{
    public function getTitle()
    {
        /*
         * Return the title of this article.
         *
         * @return string
         */
        return fn() => $this->getAttribute('title');
    }

    /**
     * To know if the article is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        /*
         * Return the title of this article.
         *
         * @return string
         */
        return function (): bool {
            $isPublished = $this->getAttribute('is_published');

            return !empty($isPublished);
        };
    }

    public function returnStaticClosure()
    {
        return static function(): void {
        };
    }
}
