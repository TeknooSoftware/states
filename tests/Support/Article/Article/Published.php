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
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

declare(strict_types=1);

namespace Teknoo\Tests\Support\Article\Article;

use Closure;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State Published
 * State for a published article
 * Copy from Demo for functional tests.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (richarddeloge@gmail.com)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 * @mixin Article
 */
class Published implements StateInterface
{
    use StateTrait;

    public function getFormattedBody()
    {
        /*
         * Get the body and transform BBCode to HTML.
         *
         * @return string
         */
        return function (): string|array {
            $body = $this->getAttribute('body');

            return str_replace(
                ['[br]', '[b]', '[/b]'],
                ['<br/>', '<strong>', '</strong>'],
                $body
            );
        };
    }

    protected function getDate()
    {
        /*
         * Fake method not callable in public scope.
         */
        return function (): void {
        };
    }
}
