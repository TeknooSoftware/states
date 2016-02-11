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
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\States\Command\Parser;

use Teknoo\States\Loader\LoaderInterface;

/**
 * Class Factory
 * Parser to analyze factory.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Factory extends AbstractParser
{
    /**
     * Test if the factory implements the good interface defined in the library States.
     *
     * @return bool
     *
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function isValidFactory()
    {
        return $this->loadFile(LoaderInterface::FACTORY_FILE_NAME)
            ->implementsInterface('\Teknoo\States\Factory\FactoryInterface');
    }

    /**
     * Test if the factory is a subclass of the standard factory implemented in the library States.
     *
     * @return bool
     *
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function isStandardFactory()
    {
        return $this->loadFile(LoaderInterface::FACTORY_FILE_NAME)
            ->isSubclassOf('\Teknoo\States\Factory\Standard');
    }

    /**
     * Test if the factory is a subclass of the integrated factory implemented in the library States.
     *
     * @return bool
     *
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function isIntegratedFactory()
    {
        return $this->loadFile(LoaderInterface::FACTORY_FILE_NAME)
            ->isSubclassOf('\Teknoo\States\Factory\Integrated');
    }

    /**
     * Test if the factory use of the default implementation of this library States provided by the trait
     * \Teknoo\States\Factory\TraitFactory.
     *
     * @return bool
     *
     * @throws Exception\UnReadablePath when the path is not readable
     */
    public function useTraitFactory()
    {
        return in_array(
            'Teknoo\States\Factory\FactoryTrait',
            $this->loadFile(LoaderInterface::FACTORY_FILE_NAME)->getTraitNames()
        );
    }
}
