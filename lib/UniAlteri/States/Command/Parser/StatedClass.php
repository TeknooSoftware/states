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

use Gaufrette\Filesystem;
use UniAlteri\States\Loader\FinderInterface;
use UniAlteri\States\Loader\LoaderInterface;

/**
 * Class StatedClass
 * Parser to analyze the structure of a stated class
 *
 * @package     States
 * @subpackage  Command
 * @copyright   Copyright (c) 2009-2014 Uni Alteri (http://agence.net.ua)
 * @link        http://teknoo.it/states Project website
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class StatedClass extends AbstractParser
{
    /**
     * @var Factory
     */
    protected $factoryParser;

    /**
     * @var Proxy
     */
    protected $proxyParser;

    /**
     * @var State
     */
    protected $statesParser;

    /**
     * @param Filesystem $fileSystem
     * @param string     $path
     * @param Factory    $factoryParser
     * @param Proxy      $proxyParser
     * @param State      $statesParser
     */
    public function __construct($fileSystem, $path, $factoryParser, $proxyParser, $statesParser)
    {
        parent::__construct($fileSystem, $path);
        $this->factoryParser = $factoryParser;
        $this->proxyParser = $proxyParser;
        $this->statesParser = $statesParser;
    }

    /**
     * Check if this class has its own states folders
     * @return boolean
     */
    public function hasStatesFolder()
    {
        return in_array(
            FinderInterface::STATES_PATH,
            $this->listFiles()->getArrayCopy()
        );
    }

    /**
     * Check if this class has its own proxy (not mandatory by specifications of the library)
     * @return boolean
     */
    public function hasProxy()
    {
        return in_array(
            $this->getClassNameFile(),
            $this->listFiles()->getArrayCopy()
        );
    }

    /**
     * Check if this class has its own factory
     * @return boolean
     */
    public function hasFactory()
    {
        return in_array(
            LoaderInterface::FACTORY_FILE_NAME,
            $this->listFiles()->getArrayCopy()
        );
    }

    /**
     * Return the parser to analyze the factory of this class
     * @return Factory
     */
    public function getFactoryParser()
    {
        return $this->factoryParser;
    }

    /**
     * Return the parser to analyze the proxy of this class
     * @return Proxy
     */
    public function getProxyParser()
    {
        return $this->proxyParser;
    }

    /**
     * Return the parser to analyze states of this class
     * @return State
     */
    public function getStatesParser()
    {
        return $this->statesParser;
    }
}
