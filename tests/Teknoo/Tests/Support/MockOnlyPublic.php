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
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */

namespace Teknoo\Tests\Support;

use Teknoo\States\States;

/**
 * Class MockOnlyPublic
 * Mock class to test the default trait State behavior with public methods.
 * All methods have not a description to check the state's behavior with these methods.
 *
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.software/states Project website
 *
 * @license     http://teknoo.software/states/license/mit         MIT License
 * @license     http://teknoo.software/states/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class MockOnlyPublic extends States\AbstractState
{
    /**
     * To simulate a real state behavior.
     *
     * @param bool $initializeContainer initialize virtual di container for state
     */
    public function __construct($initializeContainer = true)
    {
        if (true === $initializeContainer) {
            $this->setDIContainer(new MockDIContainer());
            $this->getDIContainer()->registerService(
                States\StateInterface::INJECTION_CLOSURE_SERVICE_IDENTIFIER,
                function () {
                    return new MockInjectionClosure();
                }
            );
        }
    }

    /**
     * Standard Method 1.
     */
    public function standardMethod1()
    {
    }

    /**
     * Final Method 2.
     */
    final public function finalMethod2()
    {
    }

    public static function staticMethod3()
    {
    }

    public function standardMethod4()
    {
    }
}
