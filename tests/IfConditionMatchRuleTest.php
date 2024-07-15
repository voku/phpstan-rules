<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionBooleanAndRule;
use voku\PHPStan\Rules\IfConditionMatchRule;
use voku\PHPStan\Rules\IfConditionSwitchCaseRule;


/**
 * @extends RuleTestCase<IfConditionBooleanAndRule>
 */
final class IfConditionMatchRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionMatchRule([\stdClass::class], true);
    }

    /**
     * @requires PHP 8.0
     */
    public function testIfConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionMatchRuleFixtures.php',
            ],
            [
                [
                    'Match_: Condition between true and false are always false.',
                    144,
                ],
                [
                    'Match_: Condition between true and false are always false.',
                    169,
                ],
                [
                    'Match_: Condition between true and false are always false.',
                    174,
                ],
                [
                    'Match_: Condition between true and false are always false.',
                    179,
                ],
                [
                    'Match_: Assignment is not allowed here.',
                    205,
                ],
                [
                    'Match_: Insane comparison between true and false.',
                    247,
                ],
            ]
        );
    }
}
