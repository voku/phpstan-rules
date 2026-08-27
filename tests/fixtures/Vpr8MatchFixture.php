<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class Vpr8MatchFixture
{
    public function check(int $value): string
    {
        return match (true) {
            4 > $value => 'small',
            default => 'big',
        };
    }
}
