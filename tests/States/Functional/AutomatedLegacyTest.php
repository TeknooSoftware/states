<?php

/*
 * States.
 *
 * LICENSE
 *
 * This source file is subject to the 3-Clause BSD license
 * it is available in LICENSE file at the root of this package
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richard@teknoo.software so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 *
 * @link        https://teknoo.software/libraries/states Project website
 *
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */

declare(strict_types=1);

namespace Teknoo\Tests\States\Functional;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use ReflectionClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use Teknoo\States\Automated\AutomatedTrait;
use Teknoo\Tests\States\Functional\AutomatedTest;
use Teknoo\Tests\Support\AutomatedAcme\AutomatedAcmeLegacy;

/**
 * Class AutomatedTest.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[CoversTrait(AutomatedTrait::class)]
#[IgnoreDeprecations]
class AutomatedLegacyTest extends AutomatedTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $rc = new ReflectionClass(AutomatedAcmeLegacy::class);
        $rc->setStaticPropertyValue('loadedStatesCaches', []);
    }

    /**
     * @return AutomatedAcmeLegacy
     */
    public function buildInstance()
    {
        return new AutomatedAcmeLegacy();
    }

    public function testUpdateStates(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . AutomatedAcmeLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testUpdateStates();
    }

    public function testPreventCacheWhenUpdateStateInState(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . AutomatedAcmeLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testPreventCacheWhenUpdateStateInState();
    }

    public function testDeprecation()
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . AutomatedAcmeLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        $this->buildInstance()->disableAllStates();
    }
}
