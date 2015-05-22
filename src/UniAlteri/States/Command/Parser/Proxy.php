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
 * @version     1.1.1
 */

namespace UniAlteri\States\Command\Parser;

/**
 * Class Proxy
 * Parser to analyze proxy.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Proxy extends AbstractParser
{
    /**
     * Test if the proxy implements the good interface defined in the library States.
     *
     * @return bool
     *
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function isValidProxy()
    {
        return $this->loadFile($this->getClassNameFile())
            ->implementsInterface('\UniAlteri\States\Proxy\ProxyInterface');
    }

    /**
     * Test if the proxy is a subclass of the standard proxy implemented in the library States.
     *
     * @return bool
     *
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function isStandardProxy()
    {
        return $this->loadFile($this->getClassNameFile())
            ->isSubclassOf('\UniAlteri\States\Proxy\Standard')
            && !$this->loadFile($this->getClassNameFile())
            ->isSubclassOf('\UniAlteri\States\Proxy\Integrated');
    }

    /**
     * Test if the proxy is a subclass of the integrated proxy implemented in the library States.
     *
     * @return bool
     *
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function isIntegratedProxy()
    {
        return $this->loadFile($this->getClassNameFile())
            ->isSubclassOf('\UniAlteri\States\Proxy\Integrated');
    }

    /**
     * Test if the proxy use of the default implementation of this library States provided by the trait
     * \UniAlteri\States\Proxy\TraitProxy.
     *
     * @return bool
     *
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function useTraitProxy()
    {
        return in_array(
            'UniAlteri\States\Proxy\ProxyTrait',
            $this->loadFile($this->getClassNameFile())->getTraitNames()
        );
    }
}
