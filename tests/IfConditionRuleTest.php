<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionRule;

/**
 * @extends RuleTestCase<IfConditionRule>
 */
final class IfConditionRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionRule([\stdClass::class]);
    }

    public function testIfConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsFixtures.php',
            ],
            [
                [
                    'Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    7,
                ],
                [
                    'Please do not use double negative string conditions. e.g. `(string)$foo != \'\'` is the same as `(string)$foo`.',
                    13,
                ],
                [
                    'Non-empty string is never empty.',
                    13,
                ],
                [
                    'Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    19
                ],
                [
                    'Do not compare objects directly, stdClass and \'\' found.',
                    19,
                ],
                [
                    'Do not compare boolean and integer.',
                    25,
                ],
                [
                    'Do not compare boolean and string.',
                    31,
                ],
                [
                    'Please do not use double negative boolean conditions. e.g. `(bool)$foo != false` is the same as `(bool)$foo`.',
                    31,
                ],
                [
                    'Non-empty string is always non-empty.',
                    37
                ],
                [
                    'Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    44
                ],
                [
                    'Do not compare objects directly, DateTimeImmutable and \'2032-03-04\' found.',
                    49
                ],
                [
                    'Do not compare objects directly, DateTimeImmutable and \'2032-03-04\' found.',
                    52
                ],
                [
                    'Do not compare objects directly, DateTimeImmutable and \'2032-03-04\' found.',
                    55
                ],
                [
                    'Do not compare objects directly, DateTimeImmutable and \'2032-03-04\' found.',
                    72
                ],
                [
                    'Do not compare objects directly, DateTimeImmutable|null and \'2013-04-05\' found.',
                    87
                ],
                [
                    'Do not compare objects directly, DateTimeImmutable|null and DateTimeImmutable found.',
                    97
                ],
                [
                    'Do not compare objects directly, DateTimeImmutable and DateTimeImmutable|null found.',
                    97
                ],
                [
                    'Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    148
                ],
                [
                    'Possible insane comparison between \'\' and 0.0',
                    148
                ],
                [
                    'Please do not use empty-string check for numeric values. e.g. `0 != \'\'` is not working with >= PHP 8.',
                    152,
                ],
                [
                    'Possible insane comparison between \'\' and 0.0',
                    152,
                ],
                [
                    'Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    156,
                ],
                [
                    'Possible insane comparison between \'\' and 0',
                    156,
                ],
                [
                    'Please do not use empty-string check for numeric values. e.g. `0 != \'\'` is not working with >= PHP 8.',
                    160,
                ],
                [
                    'Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    160,
                ],
                [
                    'Possible insane comparison between \'\' and 0',
                    160,
                ],
                [
                    'Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    164,
                ],
                [
                    'Possible insane comparison between \'\' and 0',
                    164,
                ],
                [
                    'Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    168,
                ],
                [
                    'Possible insane comparison between \'\' and 0|null',
                    168,
                ],
                [
                    'Possible insane comparison between \'0foo\' and 0',
                    175,
                ],
                [
                    'Possible insane comparison between \'0foo\' and 0',
                    178,
                ],
                [
                    'Possible insane comparison between \'0foo\' and 1',
                    181,
                ],
            ]
        );
    }

    /**
     * @requires PHP 8.1
     */
    public function testIfConditions81(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsFixtures81.php',
            ],
            [
                [
                    'Do not compare objects directly, voku\PHPStan\Rules\Test\fixtures\BulbOn::ON and voku\PHPStan\Rules\Test\fixtures\BulbOn::OFF|voku\PHPStan\Rules\Test\fixtures\BulbOn::ON found.',
                    12
                ],
                [
                    'Do not compare objects directly, voku\PHPStan\Rules\Test\fixtures\BulbOn::OFF|voku\PHPStan\Rules\Test\fixtures\BulbOn::ON and voku\PHPStan\Rules\Test\fixtures\BulbOn::ON found.',
                    12
                ],
            ]
        );
    }
}
