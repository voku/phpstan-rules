<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class DuplicateNativeComparisonPropagationFixtures
{
    public function ternary(int $value): string
    {
        return $value == '' ? 'a' : 'b';
    }

    public function switchCase(int $value): string
    {
        switch (true) {
            case $value == '':
                return 'a';
            default:
                return 'b';
        }
    }

    public function booleanNot(int $value): bool
    {
        return !($value == '');
    }

    /**
     * @return string
     */
    public function matchCase(int $value): string
    {
        return match (true) {
            $value == '' => 'a',
            default => 'b',
        };
    }
}
