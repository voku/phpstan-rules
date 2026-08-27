<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\InArrayLooseComparisonRule;

/**
 * @extends RuleTestCase<InArrayLooseComparisonRule>
 */
final class InArrayLooseComparisonRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new InArrayLooseComparisonRule();
    }

    public function testPhp8LooseComparisonHazardsAreReported(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/InArrayLooseComparisonFixtures.php'],
            [
                [
                    'FuncCall: Possible insane comparison between int and string. Use strict mode e.g. `in_array($needle, $haystack, true)`.',
                    14,
                ],
                [
                    'FuncCall: Possible insane comparison between string and int. Use strict mode e.g. `in_array($needle, $haystack, true)`.',
                    22,
                ],
                [
                    'FuncCall: Possible insane comparison between int and string. Use strict mode e.g. `in_array($needle, $haystack, true)`.',
                    30,
                ],
                [
                    'FuncCall: Possible insane comparison between int and string. Use strict mode e.g. `array_search($needle, $haystack, true)`.',
                    40,
                ],
                [
                    'FuncCall: Possible insane comparison between float and string. Use strict mode e.g. `in_array($needle, $haystack, true)`.',
                    48,
                ],
            ]
        );
    }

    public function testNonHazardsStaySilent(): void
    {
        $this->analyse([__DIR__ . '/fixtures/InArrayLooseComparisonValidFixtures.php'], []);
    }

    public function testIdentifierIsStable(): void
    {
        $errors = $this->gatherAnalyserErrors([__DIR__ . '/fixtures/InArrayLooseComparisonFixtures.php']);

        static::assertNotSame([], $errors);
        foreach ($errors as $error) {
            static::assertSame('voku.FuncCall', $error->getIdentifier());
        }
    }

    /**
     * @requires PHP 8.0
     */
    public function testNamedArgumentsAreResolvedByName(): void
    {
        $this->analyse(
            [__DIR__ . '/fixtures/InArrayLooseComparisonNamedArgumentsFixtures.php'],
            [
                [
                    'FuncCall: Possible insane comparison between int and string. Use strict mode e.g. `in_array($needle, $haystack, true)`.',
                    14,
                ],
            ]
        );
    }
}
