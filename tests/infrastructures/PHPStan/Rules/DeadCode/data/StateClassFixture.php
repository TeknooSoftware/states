<?php

declare(strict_types=1);

namespace Teknoo\Tests\States\PHPStan\Rules\DeadCode\data;

use Closure;
use Teknoo\States\State\StateInterface;
use Teknoo\States\State\StateTrait;

class StateClassFixture implements StateInterface
{
    use StateTrait;

    private function hiddenStateMethod(): Closure
    {
        return function (): int {
            return 42;
        };
    }
}
