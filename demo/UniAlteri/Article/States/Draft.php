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
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace demo\UniAlteri\Article\States;

use UniAlteri\States\States;

class Draft extends States\AbstractState
{
    /**
     * Publish this article
     */
    public function publishing()
    {
        $this->_setAttribute('is_published', true);
        //Switch to Published State, so this state will be not available for next operations
        $this->disableState('Draft');
        $this->enableState('Published');
    }

    /**
     * Define the title of this article
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_setAttribute('title', $title);
    }

    /**
     * Define the body of this article
     * @param string $body
     */
    public function setBody($body)
    {
        $this->_setAttribute('body', $body);
    }

    /**
     * Get the body source
     * @return string
     */
    public function getBodySource()
    {
        return $this->_getAttribute('body');
    }
}