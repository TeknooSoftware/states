<?php
/**
 * States
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @subpackage  Demo
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     1.0.1
 */
namespace demo\Acme\Article\States;

use UniAlteri\States\States;

/**
 * State Draft
 * State for an article not published
 *
 * @package     States
 * @subpackage  Demo
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Draft extends States\AbstractState
{
    /**
     * Publish this article
     */
    public function publishing()
    {
        $this->setAttribute('is_published', true);
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
        $this->setAttribute('title', $title);
    }

    /**
     * Define the body of this article
     * @param string $body
     */
    public function setBody($body)
    {
        $this->setAttribute('body', $body);
    }

    /**
     * Get the body source
     * @return string
     */
    public function getBodySource()
    {
        return $this->getAttribute('body');
    }
}
