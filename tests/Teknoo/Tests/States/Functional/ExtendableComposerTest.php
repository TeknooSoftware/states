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
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\Tests\States\Functional;

use Teknoo\States\Di;
use Teknoo\States\Factory\FactoryInterface;
use Teknoo\States\Loader;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class ExtendableComposerTest extends ExtendableTest
{
    /**
     * Load the library State and retrieve its default loader from its bootstrap.
     *
     * @return \Teknoo\States\Loader\LoaderInterface
     */
    protected function getLoader()
    {
        if (null === $this->loader) {
            $this->loader = include 'Teknoo'.DS.'States'.DS.'bootstrap_composer.php';
        }

        //To share the Factory interface in all context for each test
        $diContainer = $this->loader->getDiContainer();
        if (!self::$factoryRegistry instanceof Di\Container) {
            self::$factoryRegistry = $diContainer->get(FactoryInterface::DI_FACTORY_REPOSITORY);
        } else {
            $diContainer->registerInstance(FactoryInterface::DI_FACTORY_REPOSITORY, self::$factoryRegistry);
        }

        return $this->loader;
    }
}
