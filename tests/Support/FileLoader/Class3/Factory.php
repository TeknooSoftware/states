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
 * Mock factory file to test loader behavior when the factory class is valid but its method initialize throws an
 * exception and stated class loaded by loader with PSR-0 recommendations (file computed from namespace)
 * and not from namespace definitions)
 */

namespace Support\FileLoader\Class3;

use Teknoo\States\Factory\FactoryInterface;
use Teknoo\Tests\Support;

/**
 * Class FactoryClass
 * Mock factory file to test loader behavior when the factory class is valid but its method initialize throws an
 * exception and stated class loaded by loader with PSR-0 recommendations (file computed from namespace)
 * and not from namespace definitions).
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
     * Throw an exception to test if the loader return false in loading class.
     *
     * @param string $statedClassName
     * @param string $path
     *
     * @return bool|void
     *
     * @throws \Exception
     */
    protected function initialize(string $statedClassName): FactoryInterface
    {
        throw new \Exception('test');
    }
}
