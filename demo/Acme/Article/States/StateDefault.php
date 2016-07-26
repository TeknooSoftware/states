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
 * State StateDefault.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class StateDefault extends AbstractState
{
    /**
     * Return the title of this article.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getAttribute('title');
    }

    /**
     * To know if the article is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        $isPublished = $this->getAttribute('is_published');

        return !empty($isPublished);
    }
}
