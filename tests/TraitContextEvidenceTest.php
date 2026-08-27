<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionRule;

/**
 * Executable VPR-4 evidence for the current trait boundary.
 *
 * The same source expression is analysed in each using-class context. Two int consumers therefore
 * reproduce the same comparison findings on the same trait line, while a string consumer produces
 * different extension advice from that line. #74 owns changing this behavior to contextual trait
 * deferral; this test makes the pre-fix divergence reproducible instead of leaving it in prose.
 *
 * @extends RuleTestCase<IfConditionRule>
 */
final class TraitContextEvidenceTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionRule([], $this->createReflectionProvider());
    }

    public function testTraitComparisonIsPublishedPerUsingClassContext(): void
    {
        $fixture = __DIR__ . '/fixtures/TraitContext/';
        $errors = $this->gatherAnalyserErrors([
            $fixture . 'LooseComparisonTrait.php',
            $fixture . 'IntTraitConsumerOne.php',
            $fixture . 'IntTraitConsumerTwo.php',
            $fixture . 'StringTraitConsumer.php',
        ]);

        $intGenericLines = [];
        $intAdviceLines = [];
        $stringAdviceLines = [];

        foreach ($errors as $error) {
            $message = $error->getMessage();

            if (\strpos($message, "Condition between '' and int are falsy") !== false) {
                $intGenericLines[] = $error->getLine();
            }

            if (\strpos($message, 'double negative integer conditions') !== false) {
                $intAdviceLines[] = $error->getLine();
            }

            if (\strpos($message, 'double negative string conditions') !== false) {
                $stringAdviceLines[] = $error->getLine();
            }
        }

        static::assertGreaterThanOrEqual(
            2,
            \count($intGenericLines),
            'Two int consumers should expose the current per-use duplicate generic finding.'
        );
        static::assertCount(
            1,
            \array_unique($intGenericLines),
            'The duplicate findings must originate from the same trait expression.'
        );
        static::assertGreaterThanOrEqual(
            2,
            \count($intAdviceLines),
            'The extension-specific int advice is currently published per int consumer too.'
        );
        static::assertNotSame(
            [],
            $stringAdviceLines,
            'A string consumer must demonstrate that the same trait expression is analysed in a different type context.'
        );
        static::assertSame(
            $intGenericLines[0],
            $stringAdviceLines[0],
            'Context-dependent diagnostics should point to the same source expression in the trait.'
        );
    }
}
