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
 * to richarddeloge@gmail.com so we can send you a copy immediately.
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

use Teknoo\States\State\AbstractState;

/**
 * State Draft
 * State for an article not published.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Draft extends AbstractState
{
    /**
     * Publish this article.
     */
    public function publishing()
    {
        $this->setAttribute('is_published', true);
        //Switch to Published State, so this state will be not available for next operations
        $this->disableState('Draft');
        $this->enableState(Published::class);
    }

    /**
     * Define the title of this article.
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->setAttribute('title', $title);
    }

    /**
     * Define the body of this article.
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->setAttribute('body', $body);
    }

    /**
     * Get the body source.
     *
     * @return string
     */
    public function getBodySource()
    {
        return $this->getAttribute('body');
    }
}
