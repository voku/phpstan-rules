<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionBooleanAndRule;
use voku\PHPStan\Rules\IfConditionBooleanNotRule;

/**
 * @extends RuleTestCase<IfConditionBooleanNotRule>
 */
final class IfConditionBooleanNotRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionBooleanNotRule([\stdClass::class]);
    }

    public function testIfConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsBooleanNotFixtures.php',
            ],
            [
                [
                    'Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    7,
                ],
            ]
        );
    }
}
