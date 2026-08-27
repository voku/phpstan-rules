<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\ElseIfConditionBasicRule;
use voku\PHPStan\Rules\IfConditionRule;

/**
 * VPR-3/VPR-5 regression contract.
 *
 * The assertions are message-level on purpose. A line can carry a native-overlap claim and useful
 * extension-only advice at the same time, so comparing only line-number sets would allow the exact
 * finding-loss bug this policy is meant to prevent.
 *
 * @extends RuleTestCase<Rule>
 */
final class ComparisonDiagnosticPolicyTest extends RuleTestCase
{
    private const COMPARISON_FIXTURE = __DIR__ . '/fixtures/ComparisonDiagnosticPolicyFixtures.php';
    private const MATCH_FIXTURE = __DIR__ . '/fixtures/ComparisonDiagnosticPolicyMatchFixtures.php';
    private const TRUTHINESS_FIXTURE = __DIR__ . '/fixtures/LastConditionTruthinessFixtures.php';

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

    public function testDuplicateSuppressionRemovesOnlyTheGenericNativeOverlapClaim(): void
    {
        $this->ruleUnderTest = new IfConditionRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            false,
            true,
            true
        );

        $messages = $this->messagesByLine([self::COMPARISON_FIXTURE]);

        self::assertNoMessageContains($messages[11] ?? [], 'Condition between ');
        self::assertMessageContains($messages[11] ?? [], 'empty-string check for numeric values');
        self::assertMessageContains($messages[11] ?? [], 'Possible insane comparison');

        self::assertNoMessageContains($messages[16] ?? [], 'Condition between ');
        self::assertMessageContains($messages[16] ?? [], 'double negative integer conditions');
        self::assertMessageContains($messages[16] ?? [], 'Possible insane comparison');
    }

    public function testDefaultDuplicateReportingKeepsTheExistingGenericClaim(): void
    {
        $this->ruleUnderTest = new IfConditionRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            true,
            true,
            true
        );

        $messages = $this->messagesByLine([self::COMPARISON_FIXTURE]);

        self::assertMessageContains($messages[11] ?? [], 'Condition between ');
        self::assertMessageContains($messages[16] ?? [], 'Condition between ');
    }

    public function testPhpDocConstantOverlapIsSuppressedWhenPhpDocTypesAreCertain(): void
    {
        $this->ruleUnderTest = new IfConditionRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            false,
            true,
            true
        );

        $messages = $this->messagesByLine([self::COMPARISON_FIXTURE]);

        self::assertNoMessageContains(
            $messages[45] ?? [],
            'Condition between '
        );
    }

    public function testPhpDocConstantOverlapFailsOpenWhenPhpDocTypesAreNotCertain(): void
    {
        $this->ruleUnderTest = new IfConditionRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            false,
            true,
            false
        );

        $messages = $this->messagesByLine([self::COMPARISON_FIXTURE]);

        self::assertMessageContains(
            $messages[45] ?? [],
            'Condition between ',
            'PHPStan uses the native type with treatPhpDocTypesAsCertain=false, so suppression must fail open.'
        );
    }

    public function testAlwaysTrueLastElseIfDropsOnlyTheTruthClaimWhenThePhpStanFlagIsOff(): void
    {
        $this->ruleUnderTest = new IfConditionRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            true,
            false,
            true
        );

        $messages = $this->messagesByLine([self::COMPARISON_FIXTURE]);

        self::assertNoMessageContains($messages[23] ?? [], 'Condition between ');
        self::assertMessageContains($messages[23] ?? [], 'double negative integer conditions');
        self::assertMessageContains($messages[23] ?? [], 'Possible insane comparison');
    }

    public function testAlwaysTrueLastElseIfIsReportedWhenThePhpStanFlagIsOn(): void
    {
        $this->ruleUnderTest = new IfConditionRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            true,
            true,
            true
        );

        $messages = $this->messagesByLine([self::COMPARISON_FIXTURE]);

        self::assertMessageContains($messages[23] ?? [], 'Condition between ');
    }

    public function testSwitchCaseBeforeDefaultIsNotTreatedAsALastCondition(): void
    {
        $this->ruleUnderTest = new IfConditionRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            true,
            false,
            true
        );

        $messages = $this->messagesByLine([self::COMPARISON_FIXTURE]);

        self::assertMessageContains($messages[33] ?? [], 'Condition between ');
    }

    /**
     * @requires PHP 8.0
     */
    public function testMatchArmBeforeDefaultIsNotTreatedAsALastCondition(): void
    {
        $this->ruleUnderTest = new IfConditionRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            true,
            false,
            true
        );

        $messages = $this->messagesByLine([self::MATCH_FIXTURE]);

        self::assertMessageContains($messages[12] ?? [], 'Condition between ');
    }

    /**
     * @requires PHP 8.0
     */
    public function testActualLastMatchArmHonorsThePhpStanFlag(): void
    {
        $this->ruleUnderTest = new IfConditionRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            true,
            false,
            true
        );

        $messages = $this->messagesByLine([self::MATCH_FIXTURE]);
        self::assertNoMessageContains($messages[21] ?? [], 'Condition between ');

        $this->ruleUnderTest = new IfConditionRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            true,
            true,
            true
        );

        $messages = $this->messagesByLine([self::MATCH_FIXTURE]);
        self::assertMessageContains($messages[21] ?? [], 'Condition between ');
    }

    public function testTruthinessLastConditionSuppressesAlwaysTrueButNeverAlwaysFalse(): void
    {
        $this->ruleUnderTest = new ElseIfConditionBasicRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            false,
            true
        );

        $messages = $this->messagesByLine([self::TRUTHINESS_FIXTURE]);

        self::assertNoMessageContains($messages[15] ?? [], 'Non-empty array is never empty.');
        self::assertMessageContains(
            $messages[28] ?? [],
            'Non-empty array is never empty.',
            'The last condition is always false here and must remain reportable.'
        );
    }

    public function testTruthinessLastConditionIsReportedWhenThePhpStanFlagIsOn(): void
    {
        $this->ruleUnderTest = new ElseIfConditionBasicRule(
            [],
            $this->createReflectionProvider(),
            false,
            false,
            true,
            true
        );

        $messages = $this->messagesByLine([self::TRUTHINESS_FIXTURE]);

        self::assertMessageContains($messages[15] ?? [], 'Non-empty array is never empty.');
        self::assertMessageContains($messages[28] ?? [], 'Non-empty array is never empty.');
    }

    /**
     * @param array<int, string> $files
     *
     * @return array<int, array<int, string>>
     */
    private function messagesByLine(array $files): array
    {
        $messages = [];
        foreach ($this->gatherAnalyserErrors($files) as $error) {
            $line = $error->getLine();
            $messages[$line][] = $error->getMessage();
        }

        return $messages;
    }

    /**
     * @param array<int, string> $messages
     */
    private static function assertMessageContains(array $messages, string $needle, string $message = ''): void
    {
        foreach ($messages as $actualMessage) {
            if (\strpos($actualMessage, $needle) !== false) {
                static::assertTrue(true);

                return;
            }
        }

        static::fail($message !== '' ? $message : 'No diagnostic contained: ' . $needle);
    }

    /**
     * @param array<int, string> $messages
     */
    private static function assertNoMessageContains(array $messages, string $needle): void
    {
        foreach ($messages as $actualMessage) {
            static::assertStringNotContainsString($needle, $actualMessage);
        }

        static::assertTrue(true);
    }
}
