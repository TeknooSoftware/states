<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @package     States
 * @subpackage  Demo
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace demo\UniAlteri\Article\States;

use UniAlteri\States\States;

class Published extends States\AbstractState
{
    /**
     * Get the body and transform BBCode to HTML
     * @return string
     */
    public function getFormattedBody()
    {
        $body = $this->_getAttribute('body');
        return str_replace(
            array(
                '[br]',
                '[b]',
                '[/b]'
            ),
            array(
                '<br/>',
                '<strong>',
                '</strong>'
            ),
            $body
        );
    }

    /**
     * Fake method not callable in public scope
     */
    protected function _getDate()
    {

    }
}