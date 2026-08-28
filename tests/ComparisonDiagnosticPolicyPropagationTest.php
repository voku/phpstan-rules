<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionBooleanNotRule;
use voku\PHPStan\Rules\IfConditionMatchRule;
use voku\PHPStan\Rules\IfConditionSwitchCaseRule;
use voku\PHPStan\Rules\IfConditionTernaryOperatorRule;

/**
 * Regression coverage for #79: the duplicate-native policy belongs to every rule that republishes
 * diagnostics for the same loose-comparison node, not only IfConditionRule.
 *
 * @extends RuleTestCase<Rule>
 */
final class ComparisonDiagnosticPolicyPropagationTest extends RuleTestCase
{
    private const FIXTURE = __DIR__ . '/fixtures/DuplicateNativeComparisonPropagationFixtures.php';
    private const MATCH_FIXTURE = __DIR__ . '/fixtures/DuplicateNativeComparisonPropagationMatchFixtures.php';

    /**
     * @var Rule|null
     */
    private $ruleUnderTest;

    protected function getRule(): Rule
    {
        if ($this->ruleUnderTest === null) {
            throw new \LogicException('The rule under test has to be selected by the test method.');
        }

        return $this->ruleUnderTest;
    }

    public function testTernaryRuleUsesDuplicateNativePolicy(): void
    {
        $this->ruleUnderTest = new IfConditionTernaryOperatorRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            false,
            true,
            true
        );

        $this->assertGenericNativeOverlapIsSuppressed([self::FIXTURE]);
    }

    public function testSwitchRuleUsesDuplicateNativePolicy(): void
    {
        $this->ruleUnderTest = new IfConditionSwitchCaseRule(
            [],
            false,
            $this->createReflectionProvider(),
            false,
            false,
            true,
            true
        );

        $this->assertGenericNativeOverlapIsSuppressed([self::FIXTURE]);
    }

    public function testBooleanNotRuleUsesDuplicateNativePolicy(): void
    {
        $this->ruleUnderTest = new IfConditionBooleanNotRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            false,
            true,
            true
        );

        $this->assertGenericNativeOverlapIsSuppressed([self::FIXTURE]);
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testMatchRuleUsesDuplicateNativePolicy(): void
    {
        $this->ruleUnderTest = new IfConditionMatchRule(
            [],
            false,
            $this->createReflectionProvider(),
            false,
            false,
            true,
            true
        );

        $this->assertGenericNativeOverlapIsSuppressed([self::MATCH_FIXTURE]);
    }

    /**
     * @param array<int, string> $files
     */
    private function assertGenericNativeOverlapIsSuppressed(array $files): void
    {
        $messages = [];
        foreach ($this->gatherAnalyserErrors($files) as $error) {
            $messages[] = $error->getMessage();
        }

        static::assertNotSame([], $messages, 'The extension-specific diagnostics must remain visible.');

        foreach ($messages as $message) {
            static::assertStringNotContainsString('Condition between ', $message);
        }
    }
}
