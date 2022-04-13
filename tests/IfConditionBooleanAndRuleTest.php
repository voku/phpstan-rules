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

    public function testIfConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsBooleanAndFixtures.php',
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
