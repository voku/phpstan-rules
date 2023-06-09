<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionBasicRule;
use voku\PHPStan\Rules\IfConditionRule;

/**
 * @extends RuleTestCase<IfConditionBasicRule>
 */
final class IfConditionBasicRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionBasicRule([\stdClass::class]);
    }

    public function testIfConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsBasicFixtures.php',
            ],
            [
                [
                    'If_: Use a function e.g. `count($foo) > 0` instead of `$foo`.',
                    7,
                ],
                [
                    'If_: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    17
                ],
                [
                    'If_: Non-empty array is never empty.',
                    23
                ],
                [
                    'If_: Use a function e.g. `count($foo) === 0` instead of `!$foo`.',
                    40
                ]
            ]
        );
    }
}
