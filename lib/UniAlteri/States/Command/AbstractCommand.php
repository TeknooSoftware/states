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

use spec\Gaufrette\Adapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use UniAlteri\States\Command\Parser\AbstractParser;
use UniAlteri\States\Command\Writer\AbstractWriter;

/**
 * Class ClassCreate
 * Command to create a new empty stated class
 *
 * @package     States
 * @subpackage  Command
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class AbstractCommand extends Command
{
    /**
     * @var callable
     */
    protected $adapter;

    /**
     * @var callable
     */
    protected $factory;

    /**
     * @param string $name
     * @param callable $adapter
     * @param callable $factory
     */
    public function __construct($name = null, $adapter = null, $factory = null)
    {
        $this->adapter = $adapter;
        $this->factory = $factory;
        parent::__construct($name);
    }

    /**
     * @param string $name
     * @param callable $adapter
     * @param string $destinationPath
     * @return AbstractWriter|AbstractParser
     */
    protected function getParserOrWriter($name, $adapter, $destinationPath)
    {
        $factory = $this->factory;
        return $factory($name, $adapter, $destinationPath);
    }
}