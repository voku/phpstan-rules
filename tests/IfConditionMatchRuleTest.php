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
     * @requires PHP 8.1
     */
    public function testEnumMatchIsAllowed(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/MatchTestMembershipLevel.php',
                __DIR__ . '/fixtures/MatchTestStatus.php',
            ],
            []
        );
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
                    'Match_: Do not compare objects directly, stdClass and \'\' found.',
                    22,
                ],
                [
                    'Match_: Do not compare objects directly, DateTimeImmutable and \'2032-03-04\' found.',
                    57,
                ],
                [
                    'Match_: Do not compare objects directly, DateTimeImmutable and \'2032-03-04\' found.',
                    61,
                ],
                [
                    'Match_: Do not compare objects directly, DateTimeImmutable and \'2032-03-04\' found.',
                    65,
                ],
                [
                    'Match_: Do not compare objects directly, DateTimeImmutable|null and \'2013-04-05\' found.',
                    93,
                ],
                [
                    'Match_: Do not compare objects directly, DateTimeImmutable and DateTimeImmutable|null found.',
                    105,
                ],
                [
                    'Match_: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    51,
                ],
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
                    'Match_: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    269,
                ],
                [
                    'Match_: Insane comparison between true and false.',
                    247,
                ],
            ]
        );
    }
}
