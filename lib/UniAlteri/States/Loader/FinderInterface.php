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
 * @project     States
 * @category    DI
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @license     http://agence.net.ua/states/license/new-bsd     New BSD License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     $Id$
 */

namespace UniAlteri\States\Loader;
use \UniAlteri\States\DI;

interface FinderInterface
{
    /**
     * Name of Finder (service to find and load elements of stated class)
     */
    const DI_FINDER_NAME = 'FinderLoader';

    /**
     * Folder where stored states of the stated class
     */
    const STATES_PATH = 'States';

    /**
     * PHP File of Proxy into each stated class
     */
    const PROXY_FILE_NAME = 'Proxy.php';

    /**
     * PHP File of Factory into each stated class
     */
    const FACTORY_FILE_NAME = 'Factory.php';

    /**
     * Suffix name of the Proxy PHP Class of each Stated Class (The pattern is <statedClassName>[Suffix]
     */
    const PROXY_SUFFIX_CLASS_NAME = 'Proxy';

    /**
     * Suffix name of the Factory PHP Class of each Stated Class (The pattern is <statedClassName>[Suffix]
     */
    const FACTORY_SUFFIX_CLASS_NAME = 'Factory';

    /**
     * Register a DI container for this object
     * @param \UniAlteri\States\DI\ContainerInterface $container
     */
    public function setDIContainer(DI\ContainerInterface $container);

    /**
     * Return the DI Container used for this object
     * @return \UniAlteri\States\DI\ContainerInterface
     */
    public function getDIContainer();

    /**
     * List all available state object of the stated class
     * @return string[]
     */
    public function listStates();

    /**
     * Load and build the required state object of the stated class
     * @param string $stateName
     * @return \UniAlteri\States\States\StateInterface
     */
    public function loadState($stateName);

    /**
     * Load and build a proxy object of the stated class
     * @return \UniAlteri\States\Proxy\ProxyInterface
     */
    public function loadProxy();
}