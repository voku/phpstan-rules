<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionBooleanAndRule;
use voku\PHPStan\Rules\IfConditionSwitchCaseRule;


/**
 * @extends RuleTestCase<IfConditionBooleanAndRule>
 */
final class IfConditionSwitchCaseRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionSwitchCaseRule([\stdClass::class], true);
    }
    
    public function testIfConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionSwitchCaseRuleFixtures.php',
            ],
            [
                [
                    'Switch_: Do not compare boolean and integer.',
                    35,
                ],
                [
                    'Switch_: Do not compare boolean and string.',
                    43,
                ],
                [
                    'Switch_: Condition between true and false are always false.',
                    170,
                ],
                [
                    'Switch_: Condition between true and false are always false.',
                    199,
                ],
                [
                    'Switch_: Condition between true and false are always false.',
                    205,
                ],
                [
                    'Switch_: Condition between true and false are always false.',
                    211,
                ],
                [
                    'Switch_: Assignment is not allowed here.',
                    240,
                ],
                [
                    'Switch_: Use a function e.g. `count($foo) > 0` instead of `$foo`.',
                    291,
                ],
                [
                    'Switch_: Use a function e.g. `count($foo) > 0` instead of `$foo`.',
                    290,
                ],
            ]
        );
    }
}
