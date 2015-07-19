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

namespace UniAlteri\Tests\States\Functional;

use UniAlteri\States\Di;
use UniAlteri\States\Exception\MethodNotImplemented;
use UniAlteri\States\Factory\FactoryInterface;
use UniAlteri\States\Loader;
use UniAlteri\Tests\Support\Extendable\Daughter\Daughter;
use UniAlteri\Tests\Support\Extendable\GrandDaughter\GrandDaughter;
use UniAlteri\Tests\Support\Extendable\Mother\Mother;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/states Project website
 *
 * @license     http://teknoo.it/states/license/mit         MIT License
 * @license     http://teknoo.it/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class ExtendableComposerTest extends ExtendableTest
{
    /**
     * Load the library State and retrieve its default loader from its bootstrap.
     *
     * @return \UniAlteri\States\Loader\LoaderInterface
     */
    protected function getLoader()
    {
        if (null === $this->loader) {
            $this->loader = include 'UniAlteri'.DS.'States'.DS.'bootstrap_composer.php';
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
