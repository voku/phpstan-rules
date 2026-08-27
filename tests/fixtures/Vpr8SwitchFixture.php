<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class Vpr8SwitchFixture
{
    public function check(int $value): string
    {
        switch (true) {
            case 4 > $value:
                return 'small';
            default:
                return 'big';
        }
    }
}
