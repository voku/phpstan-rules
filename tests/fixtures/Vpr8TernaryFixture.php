<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class Vpr8TernaryFixture
{
    public function check(int $value): string
    {
        return 4 > $value ? 'small' : 'big';
    }
}
