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
 */

namespace UniAlteri\States\Command\Writer;

use UniAlteri\States\Loader\LoaderInterface;

/**
  * Class Factory
  * Writer to create or update a factory.
  *
  * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
  *
  * @link        http://teknoo.it/states Project website
  *
  * @license     http://teknoo.it/states/license/mit         MIT License
  * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
  * @author      Richard Déloge <r.deloge@uni-alteri.com>
  */
 class Factory extends AbstractWriter
 {
     /**
      * Protected method to generate the php code for the factory.
      *
      * @param string $className
      * @param string $namespace
      * @param bool $isIntegrated
      *
      * @return string
      */
     protected function generateFactory($className, $namespace, $isIntegrated)
     {
         $factoryExtendsNamespace = 'UniAlteri\\States\\Factory\\Standard';
         $factoryExtendsClassName = 'Standard';
         if (!empty($isIntegrated)) {
             $factoryExtendsNamespace = 'UniAlteri\\States\\Factory\\Integrated';
             $factoryExtendsClassName = 'Integrated';
         }

         $factoryClassName = LoaderInterface::FACTORY_CLASS_NAME;

         return <<<EOF
<?php

namespace $namespace\\$className;

use $factoryExtendsNamespace;

/**
 * Class Factory
 * Factory of the stated class $className
 *
 * @package     $namespace
 */
class $factoryClassName extends $factoryExtendsClassName
{
}
EOF;
     }

     /**
      * Method to create a new standard factory for the defined stated class.
      *
      * @param string $className
      * @param string $namespace
      *
      * @return bool
      */
     public function createStandardFactory($className, $namespace)
     {
         $factoryCode = $this->generateFactory($className, $namespace, false);
         $factoryFileName = LoaderInterface::FACTORY_FILE_NAME;
         if (0 < $this->write($factoryFileName, $factoryCode)) {
             return true;
         } else {
             return false;
         }
     }

     /**
      * Method to create a new integrated factory for the defined stated class.
      *
      * @param string $className
      * @param string $namespace
      *
      * @return bool
      */
     public function createIntegratedFactory($className, $namespace)
     {
         $factoryCode = $this->generateFactory($className, $namespace, true);
         $factoryFileName = LoaderInterface::FACTORY_FILE_NAME;
         if (0 < $this->write($factoryFileName, $factoryCode)) {
             return true;
         } else {
             return false;
         }
     }
 }
