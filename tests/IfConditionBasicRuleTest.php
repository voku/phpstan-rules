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
                    'Use a function e.g. `count($foo) > 0` instead of `$foo`.',
                    7,
                ],
                [
                    'Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    17
                ],
                [
                    'Use a function e.g. `count($foo) > 0` instead of `$foo`.',
                    23
                ]
            ]
        );
    }
}
