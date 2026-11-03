<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionBasicRule;

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
                    'If_: Use a function e.g. `count($foo) > 0` instead of `$foo`.',
                    7,
                ],
                [
                    'If_: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    17,
                ],
                [
                    'If_: Non-empty array is never empty.',
                    23,
                ],
                [
                    'If_: Use a function e.g. `count($foo) === 0` instead of `!$foo`.',
                    40,
                ],
                [
                    'If_: Non-empty array is never empty.',
                    52,
                ],
            ]
        );
    }

    public function testArrayTruthinessAutofix(): void
    {
        // The fix API is new in PHPStan 2.3.5; legacy analysis remains supported.
        if (!\method_exists(\PHPStan\Rules\RuleErrorBuilder::class, 'removeNode')) {
            static::markTestSkipped('PHPStan before 2.3.5 cannot execute rule fixes.');
        }

        $this->fix(
            __DIR__ . '/fixtures/IfArrayTruthinessFixFixtures.php',
            __DIR__ . '/fixtures/IfArrayTruthinessFixFixtures.php.fixed'
        );
    }

    public function testArrayOnlyUnionIsNeverMarkedFixable(): void
    {
        if (!\method_exists(\PHPStan\Rules\RuleErrorBuilder::class, 'removeNode')) {
            static::markTestSkipped('Fixability metadata requires PHPStan 2.3.5.');
        }

        $errors = $this->gatherAnalyserErrors([
            __DIR__ . '/fixtures/IfArrayUnionFixFixtures.php',
        ]);
        static::assertNotEmpty($errors, 'The union fixture must actually trigger our array diagnostic.');

        foreach ($errors as $error) {
            static::assertStringStartsWith('If_:', $error->getMessage());
            static::assertFalse($error->isFixable(), 'Union-of-arrays conditions must remain advisory.');
        }
    }
}
