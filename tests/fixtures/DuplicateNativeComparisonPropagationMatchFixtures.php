<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class DuplicateNativeComparisonPropagationMatchFixtures
{
    public function matchCase(int $value): string
    {
        return match (true) {
            $value == '' => 'a',
            default => 'b',
        };
    }
}
