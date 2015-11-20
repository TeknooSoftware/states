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
 *
 * Mock factory file to test loader behavior when the factory class is missing but its initialize method
 * throws an exception
 * This factory is included from namespace definitions registered into loader.
 * The path is not computed from the class's name following PSR-0 recommendations
 */

namespace Teknoo\Tests\Support\Loader\Class3Phar;

use Teknoo\States\Factory\FactoryInterface;
use Teknoo\Tests\Support;

/**
 * Class FactoryClass
 * Mock factory file to test loader behavior when the factory class is missing but its initialize method
 * throws an exception
 * This factory is included from namespace definitions registered into loader.
 * The path is not computed from the class's name following PSR-0 recommendations.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License

 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Factory extends Support\MockFactory
{
    /**
     * {@inheritdoc}
     */
    protected function initialize(string $statedClassName): FactoryInterface
    {
        throw new \Exception('test');
    }
}
