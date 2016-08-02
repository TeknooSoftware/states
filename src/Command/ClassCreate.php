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
namespace Teknoo\States\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Teknoo\States\Proxy\ProxyInterface;

/**
  * Class ClassCreate
  * Command to create a new empty stated class.
  *
  *
  *
  * @link        http://teknoo.software/states Project website
  *
  * @license     http://teknoo.software/license/mit         MIT License
  * @author      Richard Déloge <richarddeloge@gmail.com>
  */
 class ClassCreate extends AbstractCommand
 {
     /**
      * Configures the current command.
      */
     protected function configure()
     {
         $this->setName('class:create')
            ->setDescription('Create a new empty stated class')
            ->addArgument(
                'className',
                 InputArgument::REQUIRED,
                 'Full qualified name of the new stated class, with its namespace'
             )
            ->addOption(
                'path',
                 'p',
                 InputOption::VALUE_REQUIRED,
                 'Path where localise the new stated class'
             )
            ->addOption(
                'mode',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Proxy mode (integrated or standard)',
                'integrated'
            );
     }

     /**
      * To generate the destination path from the destination option and the full class name (with namespace).
      *
      * @param string $destinationPath
      * @param string $fullClassName
      *
      * @return string
      */
     protected function defineDestinationPatch(string $destinationPath, string $fullClassName): string
     {
         $fullClassName = \trim($fullClassName, '\\');
         $fullClassNameParts = \explode('\\', $fullClassName);

         $fullClassNameFinal = \str_replace('\\', '/', $fullClassName);
         for ($i = \count($fullClassNameParts); $i > 0; --$i) {
             $pathToTest = \implode('/', \array_slice($fullClassNameParts, 0, $i));
             if (false !== \strpos($destinationPath, $pathToTest)) {
                 $fullClassNameFinal = \implode('/', \array_slice($fullClassNameParts, $i));
                 break;
             }
         }

         return \rtrim($destinationPath.DIRECTORY_SEPARATOR.$fullClassNameFinal, '/');
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
      *
      * @see    setCode()
      */
     protected function execute(InputInterface $input, OutputInterface $output)
     {
         $fullClassName = $input->getArgument('className');
         $destinationPath = \rtrim($input->getOption('path'), ' /');

         $mode = $input->getOption('mode');
         $integrated = true;
         if ('standard' == $mode) {
             $integrated = false;
         }

         $fullClassNameExploded = \explode('\\', $fullClassName);
         $className = \array_pop($fullClassNameExploded);
         $namespace = \implode('\\', $fullClassNameExploded);

         $destinationPath = $this->defineDestinationPatch($destinationPath, $fullClassName);

         $proxyWriter = $this->createWriter('Writer\Proxy', $destinationPath);
         if (true === $integrated) {
             $proxyWriter->createIntegratedProxy($className, $namespace);
         } else {
             $proxyWriter->createStandardProxy($className, $namespace);
         }

         $factoryWriter = $this->createWriter('Writer\Factory', $destinationPath);
         if (true === $integrated) {
             $factoryWriter->createIntegratedFactory($className, $namespace);
         } else {
             $factoryWriter->createStandardFactory($className, $namespace);
         }

         $stateWriter = $this->createWriter('Writer\State', $destinationPath);
         $stateWriter->createState($className, $namespace, ProxyInterface::DEFAULT_STATE_NAME);
     }
 }
