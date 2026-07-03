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

namespace Teknoo\States\PHPStan\Rules\DeadCode;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassMethodsNode;
use PHPStan\Rules\DeadCode\UnusedPrivateMethodRule;
use PHPStan\Rules\Rule;
use Teknoo\States\State\StateInterface;

/**
 * Decorator to managed bugged UnusedPrivateMethodRule
 *
 * @copyright   Copyright (c) EIRL Richard Déloge (https://deloge.io - richard@deloge.io)
 * @copyright   Copyright (c) SASU Teknoo Software (https://teknoo.software - contact@teknoo.software)
 * @license     http://teknoo.software/license/bsd-3         3-Clause BSD License
 * @author      Richard Déloge <richard@teknoo.software>
 *
 * @implements Rule<ClassMethodsNode>
 */
final class UnusedPrivateMethodRuleDecorator implements Rule
{
    public function __construct(
        private readonly UnusedPrivateMethodRule $inner,
    ) {
    }

    public function getNodeType(): string
    {
        return $this->inner->getNodeType();
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node instanceof ClassMethodsNode) {
            $classReflection = $node->getClassReflection();

            if ($classReflection->implementsInterface(StateInterface::class)) {
                return [];
            }
        }

        return $this->inner->processNode($node, $scope);
    }
}
