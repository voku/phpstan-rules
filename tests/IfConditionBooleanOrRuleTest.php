<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionBooleanAndRule;
use voku\PHPStan\Rules\IfConditionBooleanOrRule;

/**
 * @extends RuleTestCase<IfConditionBooleanOrRule>
 */
final class IfConditionBooleanOrRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionBooleanOrRule([\stdClass::class]);
    }

    public function testIfConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsBooleanOrFixtures.php',
            ],
            [
                [
                    'Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    8,
                ],
            ]
        );
    }
}
