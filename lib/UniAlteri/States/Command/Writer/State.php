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

namespace UniAlteri\States\Command\Writer;
use UniAlteri\States\Loader\FinderInterface;
use UniAlteri\States\Proxy\ProxyInterface;

/**
 * Class State
 * Writer to create or update a state
 *
 * @package     States
 * @subpackage  Command
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
 class State extends AbstractWriter
 {
     /**
      * Generator to build the php code for the new state of the stated class
      *
      * @param string $className
      * @param string $namespace
      * @param string $stateName
      * @return string
      */
     protected function generateState($className, $namespace, $stateName)
     {
         return <<<EOF
<?php

namespace $namespace\States;

use UniAlteri\States\States;

/**
 * State $stateName
 * State for the stated class $className
 *
 * @package     $namespace
 * @subpackage  States
 */
class $stateName extends States\AbstractState
{
}
EOF;
     }

     /**
      * Method to create the default and mandatory state for the defined stated class
      * @param string $className
      * @param string $namespace
      * @return bool
      */
     public function createDefaultState($className, $namespace)
     {
        return $this->createState($className, $namespace, ProxyInterface::DEFAULT_STATE_NAME);
     }

     /**
      * Method to create a new state for the defined stated class
      * @param string $className
      * @param string $namespace
      * @param string $stateName
      * @return bool
      */
     public function createState($className, $namespace, $stateName)
     {
         $stateCode = $this->generateState($className, $namespace, $stateName);
         $stateFileName = $className.DIRECTORY_SEPARATOR.FinderInterface::STATES_PATH.DIRECTORY_SEPARATOR.$stateName.'.php';

         if (0 < $this->write($stateFileName, $stateCode)) {
             return true;
         } else {
             return false;
         }
     }
 }