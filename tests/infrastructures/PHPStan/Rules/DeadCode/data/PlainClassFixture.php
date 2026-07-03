<?php

declare(strict_types=1);

namespace Teknoo\Tests\States\PHPStan\Rules\DeadCode\data;

class PlainClassFixture
{
    public function used(): int
    {
        return 1;
    }

    private function neverCalled(): int
    {
        return 2;
    }
}
