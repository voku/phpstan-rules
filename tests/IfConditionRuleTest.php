<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionRule;

/**
 * @extends RuleTestCase<IfConditionRule>
 */
final class IfConditionRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionRule([\stdClass::class]);
    }

    public function testIfConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsFixtures.php',
            ],
            [
                [
                    'Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    7,
                ],
                [
                    'Please do not use double negative string conditions. e.g. `(string)$foo != \'\'` is the same as `(string)$foo`.',
                    13,
                ],
                [
                    'Non-empty string is never empty.',
                    13,
                ],
                [
                    'Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    19
                ],
                [
                    'Do not compare objects directly.',
                    19,
                ],
                [
                    'Do not compare boolean and integer.',
                    25,
                ],
                [
                    'Do not compare boolean and string.',
                    31,
                ],
                [
                    'Please do not use double negative boolean conditions. e.g. `(bool)$foo != false` is the same as `(bool)$foo`.',
                    31,
                ],
                [
                    'Non-empty string is always non-empty.',
                    37
                ],
                [
                    'Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    44
                ],
                [
                    'Do not compare objects directly.',
                    44
                ],
                [
                    'Do not compare objects directly.',
                    49
                ],
                [
                    'Do not compare objects directly.',
                    52
                ],
                [
                    'Do not compare objects directly.',
                    55
                ],
                [
                    'Do not compare objects directly.',
                    72
                ],
                [
                    'Do not compare objects directly.',
                    87
                ],
            ]
        );
    }

    /**
     * @requires PHP 8.1
     */
    public function testIfConditions81(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsFixtures81.php',
            ],
            [
                [
                    'Do not compare objects directly.',
                    12
                ],
                [
                    'Do not compare objects directly.',
                    12
                ],
            ]
        );
    }
}
