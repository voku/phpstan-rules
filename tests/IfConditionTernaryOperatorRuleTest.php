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
                    'Ternary: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    58,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    65,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    68,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    71,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    74,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    77,
                ],
                [
                    'Ternary: Insane comparison between 0 and 0.',
                    80,
                ],
                [
                    'Ternary: Condition between 0 and 1 are falsy, please do not mix types.',
                    83,
                ],
                [
                    'Ternary: Condition between 1 and 0 are falsy, please do not mix types.',
                    83,
                ],
                [
                    'Ternary: Insane comparison between 0 and 1.',
                    83,
                ],
                [
                    'Ternary: Insane comparison between 1 and 0.',
                    83,
                ],
                [
                    'Ternary: Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    83,
                ],
                [
                    'Ternary: Condition between 0 and 1 are falsy, please do not mix types.',
                    86,
                ],
                [
                    'Ternary: Condition between 1 and 0 are falsy, please do not mix types.',
                    86,
                ],
                [
                    'Ternary: Insane comparison between 0 and 1.',
                    86,
                ],
                [
                    'Ternary: Insane comparison between 1 and 0.',
                    86,
                ],
                [
                    'Ternary: Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    86,
                ],
                [
                    'Ternary: Insane comparison between 0 and 1.',
                    89,
                ],
                [
                    'Ternary: Insane comparison between 1 and 0.',
                    89,
                ],
                [
                    'Ternary: Insane comparison between 0 and 1.',
                    92,
                ],
                [
                    'Ternary: Insane comparison between 1 and 0.',
                    92,
                ],
            ]
        );
    }
}
