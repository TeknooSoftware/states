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
use Teknoo\Tests\States\Functional\ExtendableTest;
use Teknoo\Tests\Support\Extendable\Daughter\DaughterLegacy;
use Teknoo\Tests\Support\Extendable\GrandDaughter\GrandDaughterLegacy;
use Teknoo\Tests\Support\Extendable\GrandGrandDaughter\GrandGrandDaughterLegacy;
use Teknoo\Tests\Support\Extendable\Mother\MotherLegacy;

/**
 * Class ArticleTest
 * Functional test number 1, from demo article.
 *
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
#[IgnoreDeprecations]
class ExtendableLegacyTest extends ExtendableTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $rc = new ReflectionClass(MotherLegacy::class);
        $rc->setStaticPropertyValue('loadedStatesCaches', []);
    }

    /**
     * @return MotherLegacy
     */
    public function buildMother()
    {
        return new MotherLegacy();
    }

    /**
     * @return DaughterLegacy
     */
    public function buildDaughter()
    {
        return new DaughterLegacy();
    }

    /**
     * @return GrandDaughterLegacy
     */
    public function buildGrandDaughter()
    {
        return new GrandDaughterLegacy();
    }

    /**
     * @return GrandGrandDaughterLegacy
     */
    public function buildGrandGrandDaughter()
    {
        return new GrandGrandDaughterLegacy();
    }

    public function testListAvailablesStatesMother(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testListAvailablesStatesMother();
    }

    public function testListAvailablesStatesDaughter(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testListAvailablesStatesDaughter();
    }

    public function testListAvailableStatesGrandDaughter(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testListAvailableStatesGrandDaughter();
    }

    public function testListAvailableStatesGrandGrandDaughter(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testListAvailableStatesGrandGrandDaughter();
    }

    public function testListMethodsByStatesMother(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testListMethodsByStatesMother();
    }

    public function testListMethodsByStatesDaughter(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testListMethodsByStatesDaughter();
    }

    public function testListMethodsByStatesGrandDaughter(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testListMethodsByStatesGrandDaughter();
    }

    public function testListMethodsByStatesGrandGrandDaughter(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testListMethodsByStatesGrandGrandDaughter();
    }

    public function testOverloadedState(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testOverloadedState();
    }

    public function testExtendedState(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testExtendedState();
    }

    public function testMotherCanCallPrivate(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testMotherCanCallPrivate();
    }

    public function testDaughterCanCallPrivateViaMotherMethod(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testDaughterCanCallPrivateViaMotherMethod();
    }

    public function testDaughterCanCallMotherProtected(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testDaughterCanCallMotherProtected();
    }

    public function testDaughterCanNotCallMotherPrivate(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testDaughterCanNotCallMotherPrivate();
    }

    public function testAccessToPrivateAttributeWithBindOfClosure(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testAccessToPrivateAttributeWithBindOfClosure();
    }

    public function testAccessToPrivateAttributeWithBindOfClosureFromGrandDaughter(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testAccessToPrivateAttributeWithBindOfClosureFromGrandDaughter();
    }

    public function testAccessToPrivateAttributeWithBindOfClosureFromGrandGrandDaughter(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testAccessToPrivateAttributeWithBindOfClosureFromGrandGrandDaughter();
    }

    public function testStatesLoadingWhenStatesListDeclarationIsNotImplementedInClass(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testStatesLoadingWhenStatesListDeclarationIsNotImplementedInClass();
    }

    public function testCallPrivateMethodFromAPublicMethodInParent(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testCallPrivateMethodFromAPublicMethodInParent();
    }

    public function testCallPrivateMethodFromAPublicMethod(): void
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        parent::testCallPrivateMethodFromAPublicMethod();
    }

    public function testDeprecation()
    {
        $this->expectUserDeprecationMessage(
            "Since teknoo/states 7.1.0, Method '" . MotherLegacy::class
            . "::statesListDeclaration()' is deprecated, use instead PHP attribute #[StateClass]"
        );

        $this->buildMother()->disableAllStates();
    }
}
