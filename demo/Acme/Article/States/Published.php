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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     1.0.2
 */

namespace demo\Acme\Article\States;

use UniAlteri\States\States;

/**
 * State Published
 * State for a published article.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Published implements States\StateInterface
{
    use States\StateTrait;

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
