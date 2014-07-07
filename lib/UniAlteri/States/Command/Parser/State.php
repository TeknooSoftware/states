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
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 * @version     0.9.2
 */

namespace UniAlteri\States\Command\Parser;

use \UniAlteri\States\Command;
use UniAlteri\States\Loader\FinderInterface;

/**
 * Class State
 * Parser to analyze states
 *
 * @package     States
 * @subpackage  Command
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class State extends AbstractParser
{
    /**
     * List all defined states for this class
     * @return \string[]|\ArrayObject
     * @throws Exception\UnavailablePath when the path is not available
     */
    public function listStates()
    {
        $statesArray = $this->listFiles(FinderInterface::STATES_PATH);
        for ($i=0; $i<$statesArray->count(); $i++) {
            $statesArray[$i] = substr($statesArray[$i], 0, -4);
        }

        return $statesArray;
    }

    /**
     * Test if the state implements the good interface defined in the library States
     * @param string $stateName to test
     * @return bool
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function isValidState($stateName)
    {
        return $this->loadFile(FinderInterface::STATES_PATH.$stateName.'.php')
            ->implementsInterface('\UniAlteri\States\States\StateInterface');
    }

    /**
     * Test if the state is a subclass of the standard state implemented in the library States
     * @param string $stateName to test
     * @return bool
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function isStandardState($stateName)
    {
        return $this->loadFile(FinderInterface::STATES_PATH.$stateName.'.php')
            ->isSubclassOf('\UniAlteri\States\State\AbstractState');
    }

    /**
     * Test if the state use of the default implementation of this library States provided by the trait
     * @param string $stateName to test
     * \UniAlteri\States\States\TraitStates
     * @return bool
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function useTraitState($stateName)
    {
        return in_array(
            '\UniAlteri\States\States\TraitState',
            $this->loadFile(FinderInterface::STATES_PATH.$stateName.'.php')->getTraitNames()
        );
    }
}