<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionRule;

/**
 * Executable VPR-4 evidence for the current trait boundary.
 *
 * The same source expression is analysed independently in each using-class context. Two int consumers
 * therefore reproduce the same comparison findings on the same trait line, while a string consumer
 * produces different extension advice from that line. #74 owns changing this behavior to contextual
 * trait deferral; this test makes the pre-fix divergence reproducible instead of leaving it in prose.
 *
 * @extends RuleTestCase<IfConditionRule>
 */
final class TraitContextEvidenceTest extends RuleTestCase
{
    private const TRAIT_FIXTURE = __DIR__ . '/fixtures/TraitContext/LooseComparisonTrait.php';
    private const INT_CONSUMER_ONE_FIXTURE = __DIR__ . '/fixtures/TraitContext/IntTraitConsumerOne.php';
    private const INT_CONSUMER_TWO_FIXTURE = __DIR__ . '/fixtures/TraitContext/IntTraitConsumerTwo.php';
    private const STRING_CONSUMER_FIXTURE = __DIR__ . '/fixtures/TraitContext/StringTraitConsumer.php';
    private const TRAIT_COMPARISON_LINE = 11;

    protected function getRule(): Rule
    {
        return new IfConditionRule([], $this->createReflectionProvider());
    }

    public function testTraitComparisonIsPublishedPerUsingClassContext(): void
    {
        $intConsumerOneMessages = $this->traitMessagesByLine(self::INT_CONSUMER_ONE_FIXTURE);
        $intConsumerTwoMessages = $this->traitMessagesByLine(self::INT_CONSUMER_TWO_FIXTURE);
        $stringConsumerMessages = $this->traitMessagesByLine(self::STRING_CONSUMER_FIXTURE);

        self::assertMessageContains(
            $intConsumerOneMessages[self::TRAIT_COMPARISON_LINE] ?? [],
            "Condition between '' and int are falsy"
        );
        self::assertMessageContains(
            $intConsumerOneMessages[self::TRAIT_COMPARISON_LINE] ?? [],
            'double negative integer conditions'
        );

        self::assertMessageContains(
            $intConsumerTwoMessages[self::TRAIT_COMPARISON_LINE] ?? [],
            "Condition between '' and int are falsy"
        );
        self::assertMessageContains(
            $intConsumerTwoMessages[self::TRAIT_COMPARISON_LINE] ?? [],
            'double negative integer conditions'
        );

        self::assertMessageContains(
            $stringConsumerMessages[self::TRAIT_COMPARISON_LINE] ?? [],
            'double negative string conditions'
        );
        self::assertNoMessageContains(
            $stringConsumerMessages[self::TRAIT_COMPARISON_LINE] ?? [],
            'double negative integer conditions'
        );
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function traitMessagesByLine(string $consumerFixture): array
    {
        $messages = [];

        foreach ($this->gatherAnalyserErrors([self::TRAIT_FIXTURE, $consumerFixture]) as $error) {
            if ($error->getFilePath() !== self::TRAIT_FIXTURE) {
                continue;
            }

            $messages[$error->getLine()][] = $error->getMessage();
        }

        return $messages;
    }

    /**
     * @param array<int, string> $messages
     */
    private static function assertMessageContains(array $messages, string $needle): void
    {
        foreach ($messages as $actualMessage) {
            if (\strpos($actualMessage, $needle) !== false) {
                static::assertTrue(true);

                return;
            }
        }

        static::fail('No trait diagnostic contained: ' . $needle);
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
