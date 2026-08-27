<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class ComparisonDiagnosticPolicyMatchFixtures
{
    public function armBeforeDefault(int $value): int
    {
        return match (true) {
            $value != '' => 1,
            default => 0,
        };
    }

    public function lastArmWithoutDefault(int $value): int
    {
        return match (true) {
            $value === 0 => 0,
            $value != '' => 1,
        };
    }
}
