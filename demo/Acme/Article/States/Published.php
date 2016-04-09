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
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace demo\Acme\Article\States;

use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

/**
 * State Published
 * State for a published article.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Published implements StateInterface
{
    use StateTrait;

    /**
     * Get the body and transform BBCode to HTML.
     *
     * @return string
     */
    public function getFormattedBody()
    {
        $body = $this->getAttribute('body');

        return str_replace(
            array(
                '[br]',
                '[b]',
                '[/b]',
            ),
            array(
                '<br/>',
                '<strong>',
                '</strong>',
            ),
            $body
        );
    }

    /**
     * Fake method not callable in public scope.
     */
    protected function getDate()
    {
    }
}
