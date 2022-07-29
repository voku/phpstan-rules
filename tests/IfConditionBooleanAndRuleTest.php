<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionBooleanAndRule;

/**
 * @extends RuleTestCase<IfConditionBooleanAndRule>
 */
final class IfConditionBooleanAndRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionBooleanAndRule([\stdClass::class]);
    }

    public function testBooleanAndConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsBooleanAndFixtures.php',
            ],
            [
                [
                    'BooleanAndNode: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    8,
                ],
                [
                    'BooleanAndNode: Do not compare boolean and integer.',
                    20
                ],
                [
                    'BooleanAndNode: Do not compare boolean and integer.',
                    23
                ],
                [
                    'BooleanAndNode: Condition between SplFixedArray<mixed>|null and false are always false.',
                    26
                ],
                [
                    'BooleanAndNode: Condition between 1 and 0 are always false.',
                    42
                ],
                [
                    'BooleanAndNode: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    44
                ]
            ]
        );
    }
}
