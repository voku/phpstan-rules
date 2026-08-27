<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test\fixtures;

final class ComparisonDiagnosticPolicyFixtures
{
    public function constantFalse(int $value): bool
    {
        return $value == '';
    }

    public function constantTrue(int $value): bool
    {
        return $value != '';
    }

    public function lastElseIf(int $value): bool
    {
        if ($value === 0) {
            return false;
        } elseif ($value != '') {
            return true;
        }

        return false;
    }

    public function switchCaseBeforeDefault(int $value): bool
    {
        switch (true) {
            case $value != '':
                return true;
            default:
                return false;
        }
    }

    /**
     * @param int<1, max> $value
     */
    public function phpDocOnlyConstant(int $value): bool
    {
        return $value == 0;
    }
}
