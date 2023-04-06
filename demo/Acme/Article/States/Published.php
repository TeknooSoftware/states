<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license
 * that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 */
namespace demo\Acme\Article\States;

use demo\Acme\Article\Article;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State Published
 * State for a published article.
 *
 *
 * @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richard@teknoo.software)
 * @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richard@teknoo.software>
 * @mixin Article
 */
class Published implements StateInterface
{
    use StateTrait;

    public function getFormattedBody()
    {
        /**
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
        /**
         * Fake method not callable in public scope.
         */
        return function (): void {
        };
    }
}
