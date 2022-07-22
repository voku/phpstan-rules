<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionBooleanAndRule;
use voku\PHPStan\Rules\IfConditionTernaryOperatorRule;

/**
 * @extends RuleTestCase<IfConditionBooleanAndRule>
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
            ]
        );
    }
}
