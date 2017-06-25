<?php

/**
 * StatesBundle.
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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\UniversalPackage\States\Document;

use Teknoo\States\Proxy\ProxyTrait;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Trait StandardTrait
 * Trait adapt standard proxies to doctrine.
 *
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
trait StandardTrait
{
    use ProxyTrait;

    /**
     * Doctrine does not call the construction and create a new instance without it.
     * This callback reinitialize proxy.
     *
     * @MongoDB\PostLoad()
     */
    public function postLoadDoctrine()
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
        //Update states
        $this->updateState();
    }

    /**
     * Method overloaded by States Lifecycle to update automatically states from
     * configuration.
     *
     * {@inheritdoc}
     */
    public function updateState()
    {
        return $this;
    }
}
