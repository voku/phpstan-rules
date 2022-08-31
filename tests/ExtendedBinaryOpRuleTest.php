<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\ExtendedBinaryOpRule;

/**
 * @extends RuleTestCase<ExtendedBinaryOpRule>
 */
final class ExtendedBinaryOpRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ExtendedBinaryOpRule();
    }

    public function testExtendedBinaryOpRule(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/ExtendedBinaryOpRuleFixtures.php',
            ],
            [
                [
                    'Plus: string (\'Hello World!\') in combination with non-string (6) is not allowed.',
                    7,
                ],
                [
                    'Plus: string (\'Hello World!\') in combination with non-string (array{}) is not allowed.',
                    9
                ],
                [
                    'Mul: string (\'Hello World!\') in combination with non-string (stdClass) is not allowed.',
                    11
                ],
                [
                    'Equal: string (\'\') in combination with non-string (int) is not allowed.',
                    15
                ],
            ]
        );
    }
}
