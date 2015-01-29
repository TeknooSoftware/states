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
 * @package     States
 * @subpackage  Command
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */
namespace UniAlteri\States\Command\Parser;

use UniAlteri\States\Loader\LoaderInterface;
use UniAlteri\States\Command;

/**
 * Class Factory
 * Parser to analyze factory
 *
 * @package     States
 * @subpackage  Command
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Factory extends AbstractParser
{
    /**
     * Test if the factory implements the good interface defined in the library States
     * @return bool
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function isValidFactory()
    {
        return $this->loadFile(LoaderInterface::FACTORY_FILE_NAME)
            ->implementsInterface('\UniAlteri\States\Factory\FactoryInterface');
    }

    /**
     * Test if the factory is a subclass of the standard factory implemented in the library States
     * @return bool
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function isStandardFactory()
    {
        return $this->loadFile(LoaderInterface::FACTORY_FILE_NAME)
            ->isSubclassOf('\UniAlteri\States\Factory\Standard');
    }

    /**
     * Test if the factory is a subclass of the integrated factory implemented in the library States
     * @return bool
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function isIntegratedFactory()
    {
        return $this->loadFile(LoaderInterface::FACTORY_FILE_NAME)
            ->isSubclassOf('\UniAlteri\States\Factory\Integrated');
    }

    /**
     * Test if the factory use of the default implementation of this library States provided by the trait
     * \UniAlteri\States\Factory\TraitFactory
     * @return bool
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function useTraitFactory()
    {
        return in_array(
            'UniAlteri\States\Factory\FactoryTrait',
            $this->loadFile(LoaderInterface::FACTORY_FILE_NAME)->getTraitNames()
        );
    }
}
