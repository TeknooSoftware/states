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

namespace Teknoo\States\PHPStan\DI;

use PHPStan\Rules\DeadCode\UnusedPrivateMethodRule;
use PHPStan\Rules\LazyRegistry;

use function class_alias;
use function class_exists;
use function get_declared_classes;
use function str_ends_with;

// phpcs:disable PSR1.Files.SideEffects -- the alias below MUST resolve at class-load time
// (when PHPStan's scoped Nette is in memory) so this class can extend it; it cannot live in a
// separate eagerly-loaded file without hardcoding the build-randomized `_PHPStan_<hash>` prefix.

/*
 * PHPStan ships Nette under a build-randomized prefix (_PHPStan_<hash>\Nette\...).
 * Discover it at load time and alias the CompilerExtension base to a stable local FQCN
 * so this class can extend it without hardcoding the hash. When the container is built,
 * the scoped Nette classes are already loaded (bootstrapFiles run only afterwards).
 */
(static function (): void {
    $alias = __NAMESPACE__ . '\\NetteCompilerExtensionBase';
    if (class_exists($alias, false)) {
        return;
    }

    $suffix = '\\Nette\\DI\\CompilerExtension';
    foreach (get_declared_classes() as $declared) {
        if (str_ends_with($declared, $suffix)) {
            class_alias($declared, $alias);

            return;
        }
    }

    if (class_exists('Nette\\DI\\CompilerExtension')) { // source checkout / dev fallback
        class_alias('Nette\\DI\\CompilerExtension', $alias);
    }
})();

/**
 * Nette DI compiler extension removing the `phpstan.rules.rule` tag from PHPStan's built-in
 * UnusedPrivateMethodRule so it stops being collected by the rule registry. The rule crashes
 * on Teknoo State classes (state methods are hidden from reflection), so it is replaced by
 * UnusedPrivateMethodRuleDecorator which wraps it and skips StateInterface classes.
 *
 * The service definition itself is kept (untagged) so the decorator can inject it as `inner`.
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 */
final class DisableUnusedPrivateMethodRuleExtension extends NetteCompilerExtensionBase
{
    public function beforeCompile(): void
    {
        $builder = $this->getContainerBuilder();

        foreach ($builder->findByType(UnusedPrivateMethodRule::class) as $definition) {
            $tags = $definition->getTags();
            unset($tags[LazyRegistry::RULE_TAG]);
            $definition->setTags($tags);
        }
    }
}
