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

namespace UniAlteri\States\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use UniAlteri\States\Command\Parser\StatedClass;

/**
 * Class ClassInformation
 * Command to list class informations
 *
 * @package     States
 * @subpackage  Command
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
 class ClassInformation extends AbstractCommand
 {
     /**
      * Configures the current command.
      */
     protected function configure()
     {
         $this->setName('class:info')
             ->setDescription('Create a new empty stated class')
             ->addOption(
                 'path',
                 'p',
                 InputOption::VALUE_NONE,
                 'Path of the stated class'
             );
     }

     /**
      * Executes the current command.
      *
      * This method is not abstract because you can use this class
      * as a concrete class. In this case, instead of defining the
      * execute() method, you set the code to execute by passing
      * a Closure to the setCode() method.
      *
      * @param InputInterface  $input  An InputInterface instance
      * @param OutputInterface $output An OutputInterface instance
      *
      * @return null|int     null or 0 if everything went fine, or an error code
      *
      * @throws \LogicException When this abstract method is not implemented
      * @see    setCode()
      */
     protected function execute(InputInterface $input, OutputInterface $output)
     {
         $parser = new StatedClass($this->_adapter, $input->getOption('path'));
         $output->write('Proxy defined: '.$parser->hasProxy(), true);

         $proxyParser = $parser->getProxyParser();
         $output->write('Proxy is valid: '.$proxyParser->isValidProxy(), true);
         $output->write('Proxy is standard: '.$proxyParser->isStandardProxy(), true);
         $output->write('Proxy is integrated: '.$proxyParser->isIntegratedProxy(), true);
         $output->write('Factory defined: '.$parser->hasFactory(), true);

         $factoryParser = $parser->getFactoryParser();
         $output->write('Factory is valid: '.$factoryParser->isValidFactory(), true);
         $output->write('Factory is standard: '.$factoryParser->isStandardFactory(), true);
         $output->write('Factory is integrated: '.$factoryParser->isIntegratedFactory(), true);
         $output->write('States: '.implode(', ', $parser->getStatesParser()->listStates()->getArrayCopy()), true);
     }
 }