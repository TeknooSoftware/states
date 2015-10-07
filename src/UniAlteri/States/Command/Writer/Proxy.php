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
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace UniAlteri\States\Command\Writer;

/**
  * Class Proxy
  * Writer to create or update a proxy.
  *
  * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
  *
  * @link        http://teknoo.it/states Project website
  *
  * @license     http://teknoo.it/states/license/mit         MIT License
  * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
  * @author      Richard Déloge <r.deloge@uni-alteri.com>
  */
 class Proxy extends AbstractWriter
 {
     /**
      * Protected method to generate the php code for the proxy.
      *
      * @param string $className
      * @param string $namespace
      * @param bool $isIntegrated
      *
      * @return string
      */
     protected function generateProxy($className, $namespace, $isIntegrated)
     {
         $proxyClassName = 'Proxy\\Standard';
         if (!empty($isIntegrated)) {
             $proxyClassName = 'Proxy\\Integrated';
         }

         return <<<EOF
<?php

namespace $namespace\\$className;

use UniAlteri\\States\\Proxy;

/**
 * Proxy $className
 * Proxy class of the stated class $className
 *
 * @package     $namespace
 */
class $className extends $proxyClassName
{
}
EOF;
     }

     /**
      * Method to create a new standard proxy for the defined stated class.
      *
      * @param string $className
      * @param string $namespace
      *
      * @return bool
      */
     public function createStandardProxy($className, $namespace)
     {
         $proxyCode = $this->generateProxy($className, $namespace, false);
         $proxyFileName = $className.'.php';
         if (0 < $this->write($proxyFileName, $proxyCode)) {
             return true;
         } else {
             return false;
         }
     }

     /**
      * Method to create a new integrated proxy for the defined stated class.
      *
      * @param string $className
      * @param string $namespace
      *
      * @return bool
      */
     public function createIntegratedProxy($className, $namespace)
     {
         $proxyCode = $this->generateProxy($className, $namespace, true);
         $proxyFileName = $className.'.php';
         if (0 < $this->write($proxyFileName, $proxyCode)) {
             return true;
         } else {
             return false;
         }
     }
 }
