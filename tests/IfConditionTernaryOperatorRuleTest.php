<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionTernaryOperatorRule;

/**
 * @extends RuleTestCase<IfConditionTernaryOperatorRule>
 */
final class IfConditionTernaryOperatorRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionTernaryOperatorRule([\stdClass::class]);
    }

    public function testIfConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsTernaryOperatorRuleFixtures.php',
            ],
            [
                [
                    'Ternary: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    7,
                ],
                [
                    'Ternary: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    8,
                ],
                [
                    'Ternary: Use a function e.g. `count($foo) > 0` instead of `$foo`.',
                    13,
                ],
                [
                    'Ternary: Non-empty array is never empty.',
                    16,
                ],
                [
                    'Ternary: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    65,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    72,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    75,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    78,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    81,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    84,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    87,
                ],
                [
                    'Ternary: Condition between 0 and 1 are falsy, please do not mix types.',
                    90,
                ],
                [
                    'Ternary: Condition between 1 and 0 are falsy, please do not mix types.',
                    90,
                ],
                [
                    'Ternary: Insane comparison between 0 and 1.',
                    90,
                ],
                [
                    'Ternary: Insane comparison between 1 and 0.',
                    90,
                ],
                [
                    'Ternary: Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    90,
                ],
                [
                    'Ternary: Condition between 0 and 1 are falsy, please do not mix types.',
                    93,
                ],
                [
                    'Ternary: Condition between 1 and 0 are falsy, please do not mix types.',
                    93,
                ],
                [
                    'Ternary: Insane comparison between 0 and 1.',
                    93,
                ],
                [
                    'Ternary: Insane comparison between 1 and 0.',
                    93,
                ],
                [
                    'Ternary: Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    93,
                ],
                [
                    'Ternary: Insane comparison between 0 and 1.',
                    96,
                ],
                [
                    'Ternary: Insane comparison between 1 and 0.',
                    96,
                ],
                [
                    'Ternary: Insane comparison between 0 and 1.',
                    99,
                ],
                [
                    'Ternary: Insane comparison between 1 and 0.',
                    99,
                ],
            ]
        );
    }
}
