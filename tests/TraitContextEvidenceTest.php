<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionRule;

/**
 * Direct-rule regression for the trait deferral boundary owned by #74.
 *
 * Trait diagnostics are collected through TraitContextComparisonCollector and must not be published
 * eagerly by IfConditionRule for one using-class context.
 *
 * @extends RuleTestCase<IfConditionRule>
 */
final class TraitContextEvidenceTest extends RuleTestCase
{
    private const TRAIT_FIXTURE = __DIR__ . '/fixtures/TraitContext/LooseComparisonTrait.php';
    private const INT_CONSUMER_ONE_FIXTURE = __DIR__ . '/fixtures/TraitContext/IntTraitConsumerOne.php';
    private const INT_CONSUMER_TWO_FIXTURE = __DIR__ . '/fixtures/TraitContext/IntTraitConsumerTwo.php';
    private const STRING_CONSUMER_FIXTURE = __DIR__ . '/fixtures/TraitContext/StringTraitConsumer.php';

    protected function getRule(): Rule
    {
        return new IfConditionRule([], $this->createReflectionProvider());
    }

    public function testDirectRuleDefersEveryUsingClassContext(): void
    {
        foreach ([
            self::INT_CONSUMER_ONE_FIXTURE,
            self::INT_CONSUMER_TWO_FIXTURE,
            self::STRING_CONSUMER_FIXTURE,
        ] as $consumerFixture) {
            static::assertSame(
                [],
                $this->gatherAnalyserErrors([self::TRAIT_FIXTURE, $consumerFixture]),
                'IfConditionRule must not publish trait diagnostics before all using-class contexts are known.'
            );
        }
    }
}
