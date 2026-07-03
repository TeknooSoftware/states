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

namespace Teknoo\Tests\States\PHPStan\Rules\DeadCode;

use PHPStan\Rules\DeadCode\UnusedPrivateMethodRule;
use PHPStan\Rules\Methods\AlwaysUsedMethodExtensionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Teknoo\States\PHPStan\Rules\DeadCode\UnusedPrivateMethodRuleDecorator;

/**
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @extends RuleTestCase<UnusedPrivateMethodRuleDecorator>
 */
#[CoversClass(UnusedPrivateMethodRuleDecorator::class)]
class UnusedPrivateMethodRuleDecoratorTest extends RuleTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Booting PHPStan's container needs more than the suite-wide 64M cap set in
        // tests/bootstrap.php. Only raise it (never lower), so no other test is starved.
        if (self::normalizeMemoryLimit((string) ini_get('memory_limit')) < 512 * 1024 * 1024) {
            ini_set('memory_limit', '512M');
        }
    }

    private static function normalizeMemoryLimit(string $limit): int
    {
        if ('-1' === $limit) {
            return PHP_INT_MAX;
        }

        $value = (int) $limit;

        return match (strtoupper(substr($limit, -1))) {
            'G' => $value * 1024 * 1024 * 1024,
            'M' => $value * 1024 * 1024,
            'K' => $value * 1024,
            default => $value,
        };
    }

    protected function getRule(): Rule
    {
        $provider = new class implements AlwaysUsedMethodExtensionProvider {
            public function getExtensions(): array
            {
                return [];
            }
        };

        return new UnusedPrivateMethodRuleDecorator(
            new UnusedPrivateMethodRule($provider),
        );
    }

    public function testStateClassIsSkipped(): void
    {
        // A StateInterface class must be skipped entirely: the wrapped rule is never called,
        // so its unused private (state) method raises nothing.
        $this->analyse([__DIR__ . '/data/StateClassFixture.php'], []);
    }

    public function testPlainClassIsDelegatedToInnerRule(): void
    {
        // A non-State class is delegated to the wrapped rule, which still reports the
        // genuinely unused private method.
        $this->analyse(
            [__DIR__ . '/data/PlainClassFixture.php'],
            [
                [
                    'Method Teknoo\Tests\States\PHPStan\Rules\DeadCode\data\PlainClassFixture::neverCalled() is unused.',
                    14,
                ],
            ],
        );
    }
}
