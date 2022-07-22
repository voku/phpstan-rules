<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\ElseIfConditionBasicRule;
use voku\PHPStan\Rules\IfConditionBasicRule;

/**
 * @extends RuleTestCase<ElseIfConditionBasicRule>
 */
final class ElseIfConditionBasicRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ElseIfConditionBasicRule([\stdClass::class]);
    }

    public function testElseIfConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/ElseIfConditionsBasicFixtures.php',
            ],
            [
                [
                    'ElseIf_: Use a function e.g. `count($foo) > 0` instead of `$foo`.',
                    14,
                ],
                [
                    'ElseIf_: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    21
                ],
                [
                    'ElseIf_: Use a function e.g. `count($foo) === 0` instead of `!$foo`.',
                    29,
                ],
            ]
        );
    }
}
