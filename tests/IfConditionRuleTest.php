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
        return new IfConditionRule([\stdClass::class], $this->createReflectionProvider());
    }

    public function testIfConditions(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsFixtures.php',
            ],
            [
                [
                    'NotEqual: Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    7,
                ],
                [
                    'NotEqual: Please do not use double negative string conditions. e.g. `(string)$foo != \'\'` is the same as `(string)$foo`.',
                    13,
                ],
                [
                    'NotEqual: Non-empty string is never empty.',
                    13,
                ],
                [
                    'NotEqual: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    19
                ],
                [
                    'NotEqual: Do not compare objects directly, stdClass and \'\' found.',
                    19,
                ],
                [
                    'Equal: Do not compare boolean and integer.',
                    25,
                ],
                [
                    'NotEqual: Do not compare boolean and string.',
                    31,
                ],
                [
                    'NotEqual: Please do not use double negative boolean conditions. e.g. `(bool)$foo != false` is the same as `(bool)$foo`.',
                    31,
                ],
                [
                    'Equal: Insane comparison between \'test\' and \'\'',
                    37,
                ],
                [
                    'Equal: Non-empty string is always non-empty.',
                    37,
                ],
                [
                    'Equal: Insane comparison between \'\' and \'test\'',
                    37,
                ],
                [
                    'BooleanAnd: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    44
                ],
                [
                    'SmallerOrEqual: Do not compare objects directly, DateTimeImmutable and \'2032-03-04\' found.',
                    49
                ],
                [
                    'GreaterOrEqual: Do not compare objects directly, DateTimeImmutable and \'2032-03-04\' found.',
                    52
                ],
                [
                    'Equal: Do not compare objects directly, DateTimeImmutable and \'2032-03-04\' found.',
                    55
                ],
                [
                    'SmallerOrEqual: Do not compare objects directly, DateTimeImmutable and \'2032-03-04\' found.',
                    72
                ],
                [
                    'Smaller: Do not compare objects directly, DateTimeImmutable|null and \'2013-04-05\' found.',
                    87,
                ],
                [
                    'Smaller: Do not compare objects directly, DateTimeImmutable|null and DateTimeImmutable found.',
                    97,
                ],
                [
                    'Smaller: Do not compare objects directly, DateTimeImmutable and DateTimeImmutable|null found.',
                    97,
                ],
                [
                    'BooleanAnd: Do not compare boolean and integer.',
                    119,
                ],
                [
                    'BooleanAnd: Do not compare boolean and integer.',
                    122,
                ],
                [
                    'BooleanAnd: Condition between SplFixedArray<mixed>|null and false are always false.',
                    125
                ],
                [
                    'BooleanAnd: Condition between float|int|numeric-string and \'0\' are always false.',
                    148
                ],
                [
                    'BooleanAnd: Condition between float|int|numeric-string and \'\' are always false.',
                    151
                ],
                [
                    'BooleanAnd: Condition between float|int|numeric-string and false are always false.',
                    154
                ],
                [
                    'Concat: Do not cast objects magically, please use `__toString` here, voku\PHPStan\Rules\Test\fixtures\MyId and \'My favorite…\' found.',
                    162
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
                    'Greater: Do not compare objects directly, voku\PHPStan\Rules\Test\fixtures\BulbOn::ON and voku\PHPStan\Rules\Test\fixtures\BulbOn::OFF|voku\PHPStan\Rules\Test\fixtures\BulbOn::ON found.',
                    12
                ],
                [
                    'Greater: Do not compare objects directly, voku\PHPStan\Rules\Test\fixtures\BulbOn::OFF|voku\PHPStan\Rules\Test\fixtures\BulbOn::ON and voku\PHPStan\Rules\Test\fixtures\BulbOn::ON found.',
                    12
                ],
            ]
        );
    }

    /**
     * @requires PHP 8.0
     */
    public function testIssue26(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/AFoo.php',
            ],
            [
            ]
        );
    }
    
    /**
     * @requires PHP 8.0
     */
    public function testIfConditions80(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsFixtures80.php',
            ],
            [
                [
                    'Equal: Insane comparison between 0.0 and \'\'',
                    7,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    7,
                ],
                [
                    'Equal: Insane comparison between \'\' and 0.0',
                    7,
                ],
                [
                    'NotEqual: Please do not use empty-string check for numeric values. e.g. `0 != \'\'` is not working with >= PHP 8.',
                    11,
                ],
                [
                    'NotEqual: Possible insane comparison between \'\' and 0.0',
                    11,
                ],
                [
                    'Equal: Insane comparison between 0 and \'\'',
                    15,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    15,
                ],
                [
                    'Equal: Insane comparison between \'\' and 0',
                    15,
                ],
                [
                    'NotEqual: Please do not use empty-string check for numeric values. e.g. `0 != \'\'` is not working with >= PHP 8.',
                    19,
                ],
                [
                    'NotEqual: Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    19,
                ],
                [
                    'NotEqual: Possible insane comparison between \'\' and 0',
                    19,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    23,
                ],
                [
                    'Equal: Insane comparison between \'\' and 0',
                    23,
                ],
                [
                    'Equal: Insane comparison between 0 and \'\'',
                    23,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    27,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and 0|null',
                    27,
                ],
                [
                    'Equal: Insane comparison between 0 and \'0foo\'',
                    34,
                ],
                [
                    'Equal: Insane comparison between \'0foo\' and 0',
                    34,
                ],
                [
                    'Identical: Insane comparison between 0 and \'0foo\'',
                    37,
                ],
                [
                    'Identical: Insane comparison between \'0foo\' and 0',
                    37,
                ],
                [
                    'NotEqual: Possible insane comparison between \'0foo\' and 1',
                    40,
                ],
                [
                    'Equal: Possible insane comparison between \'3\' and true',
                    43,
                ],
                [
                    'Equal: Do not compare boolean and string.',
                    43,
                ],
                [
                    'Equal: Possible insane comparison between \'0.000\' and 3|null',
                    55,
                ],
                [
                    'Equal: Possible insane comparison between null and 0|3',
                    63,
                ],
                [
                    'NotEqual: Insane comparison between 1 and \'1\'',
                    66,
                ],
                [
                    'NotEqual: Insane comparison between \'1\' and 1',
                    66,
                ],
                [
                    'NotIdentical: Insane comparison between 1 and 1',
                    69,
                ],
                [
                    'NotIdentical: Insane comparison between 1 and 1',
                    69,
                ],
                [
                    'Equal: Possible insane comparison between \'0\' and 0',
                    72,
                ],
                [
                    'Identical: Insane comparison between 0 and \'0\'',
                    75,
                ],
                [
                    'Identical: Insane comparison between \'0\' and 0',
                    75,
                ],
            ]
        );
    }

    /**
     * @requires PHP < 8.0
     */
    public function testIfConditions74(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/IfConditionsFixtures74.php',
            ],
            [
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    7,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and 0.0',
                    7,
                ],
                [
                    'NotEqual: Insane comparison between 0.0 and \'\'',
                    11
                ],
                [
                    'NotEqual: Please do not use empty-string check for numeric values. e.g. `0 != \'\'` is not working with >= PHP 8.',
                    11,
                ],
                [
                    'NotEqual: Insane comparison between \'\' and 0.0',
                    11,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    15,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and 0',
                    15,
                ],
                [
                    'NotEqual: Insane comparison between 0 and \'\'',
                    19,
                ],
                [
                    'NotEqual: Please do not use empty-string check for numeric values. e.g. `0 != \'\'` is not working with >= PHP 8.',
                    19,
                ],
                [
                    'NotEqual: Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    19,
                ],
                [
                    'NotEqual: Insane comparison between \'\' and 0',
                    19,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    23,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and 0',
                    23,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    27,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and 0|null',
                    27,
                ],
                [
                    'Equal: Possible insane comparison between \'0foo\' and 0',
                    34,
                ],
                [
                    'Identical: Insane comparison between 0 and \'0foo\'',
                    37,
                ],
                [
                    'Identical: Insane comparison between \'0foo\' and 0',
                    37,
                ],
                [
                    'NotEqual: Possible insane comparison between \'0foo\' and 1',
                    40,
                ],
                [
                    'Equal: Possible insane comparison between \'3\' and true',
                    43,
                ],
                [
                    'Equal: Do not compare boolean and string.',
                    43,
                ],
                [
                    'Equal: Possible insane comparison between \'0.000\' and 3|null',
                    55,
                ],
                [
                    'Equal: Possible insane comparison between null and 0|3',
                    63,
                ],
                [
                    'NotEqual: Insane comparison between 1 and \'1\'',
                    66,
                ],
                [
                    'NotEqual: Insane comparison between \'1\' and 1',
                    66,
                ],
                [
                    'NotIdentical: Insane comparison between 1 and 1',
                    69,
                ],
                [
                    'NotIdentical: Insane comparison between 1 and 1',
                    69,
                ],
                [
                    'Equal: Possible insane comparison between \'0\' and 0',
                    72,
                ],
                [
                    'Identical: Insane comparison between 0 and \'0\'',
                    75,
                ],
                [
                    'Identical: Insane comparison between \'0\' and 0',
                    75,
                ],
            ]
        );
    }
}
