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
        return new IfConditionRule([\stdClass::class], $this->createReflectionProvider(), true, true);
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
                    'NotEqual: Condition between \'test\' and \'\' are falsy, please do not mix types.',
                    13,
                ],
                [
                    'NotEqual: Please do not use double negative string conditions. e.g. `(string)$foo != \'\'` is the same as `(string)$foo`.',
                    13,
                ],
                [
                    'NotEqual: Condition between \'\' and \'test\' are falsy, please do not mix types.',
                    13,
                ],
                [
                    'NotEqual: Non-empty string is never empty.',
                    13,
                ],
                [
                    'NotEqual: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    19,
                ],
                [
                    'NotEqual: Do not compare objects directly, stdClass and \'\' found.',
                    19,
                ],
                [
                    'NotEqual: Condition between \'\' and stdClass are falsy, please do not mix types.',
                    19,
                ],
                [
                    'Equal: Do not compare boolean and integer.',
                    25,
                ],
                [
                    'Equal: Condition between 0 and bool are falsy, please do not mix types.',
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
                    'NotEqual: Condition between \'\' and bool are falsy, please do not mix types.',
                    31,
                ],
                [
                    'Equal: Condition between \'test\' and \'\' are falsy, please do not mix types.',
                    37,
                ],
                [
                    'Equal: Insane comparison between \'test\' and \'\'.',
                    37,
                ],
                [
                    'Equal: Condition between \'\' and \'test\' are falsy, please do not mix types.',
                    37
                ],
                [
                    'Equal: Non-empty string is always non-empty.',
                    37,
                ],
                [
                    'Equal: Insane comparison between \'\' and \'test\'.',
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
                    'Equal: Condition between \'2032-03-04\' and DateTimeImmutable are falsy, please do not mix types.',
                    55
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
                [
                    'BooleanAnd: Assignment is not allowed here.',
                    180
                ],
                [
                    'BooleanAnd: Assignment is not allowed here.',
                    183
                ],
                [
                    'Greater: Yoda condition is not allowed here.',
                    200
                ],
                [
                    'Equal: Condition between false and array are falsy, please do not mix types.',
                    215
                ],
                [
                    'Equal: Use a function e.g. `count($foo) === 0` instead of `$foo == false`.',
                    215
                ],
                [
                    'Identical: Use a function e.g. `count($foo) > 0` instead of `$foo == true`.',
                    221
                ],
                [
                    'Equal: Condition between false and array are falsy, please do not mix types.',
                    227
                ],
                [
                    'Equal: Use a function e.g. `count($foo) === 0` instead of `$foo == false`.',
                    227
                ],
                [
                    'Equal: Condition between false and non-empty-list<1> are falsy, please do not mix types.',
                    233
                ],
                [
                    'Equal: Do not compare boolean and non-empty-array.',
                    233
                ],
                [
                    'Equal: Condition between \'-1\' and int<0, 10> are falsy, please do not mix types.',
                    246
                ],
                [
                    'Equal: Condition between -1 and int<0, 10> are falsy, please do not mix types.',
                    249
                ],
                [
                    'Equal: Condition between null and array{1, 2, 3} are falsy, please do not mix types.',
                    255
                ],
                [
                    'Equal: Possible insane comparison between null and array{1, 2, 3}.',
                    255
                ],
                [
                    'Identical: Possible insane comparison between null and array{1, 2, 3}.',
                    258
                ],
                [
                    'Equal: Do not compare objects directly, DateTime and 123456 found.',
                    264
                ],
                [
                    'Equal: Condition between 123456 and DateTime are falsy, please do not mix types.',
                    264
                ],
                [
                    'Equal: Condition between 5.7 and true are falsy, please do not mix types.',
                    273
                ],
                [
                    'Equal: Possible insane comparison between 5.7 and true.',
                    273
                ],
                [
                    'Equal: Condition between true and 5.7 are falsy, please do not mix types.',
                    273
                ],
                [
                    'Equal: Condition between 5.7 and array{} are falsy, please do not mix types.',
                    276
                ],
                [
                    'Equal: Condition between true and array{1, 2, 3} are falsy, please do not mix types.',
                    282
                ],
                [
                    'Equal: Do not compare boolean and non-empty-array.',
                    282
                ],
                [
                    'Identical: Do not compare boolean and non-empty-array.',
                    285
                ],
                [
                    'Equal: Condition between \'closure\' and Closure(): 1 are falsy, please do not mix types.',
                    291
                ],
                [
                    'Equal: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    300
                ],
                [
                    'Equal: Do not compare objects directly, stdClass and \'null\' found.',
                    300,
                ],
                [
                    'Equal: Condition between \'null\' and stdClass are falsy, please do not mix types.',
                    300,
                ],
                [
                    'Identical: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    303,
                ],
                [
                    'Equal: Condition between 1 and resource|false are falsy, please do not mix types.',
                    309,
                ],
                [
                    'Equal: Yoda condition is not allowed here.',
                    318,
                ],
                [
                    'Equal: Condition between true and \'1\' are falsy, please do not mix types.',
                    318,
                ],
                [
                    'Equal: Do not compare boolean and string.',
                    318,
                ],
                [
                    'Equal: Condition between \'1\' and true are falsy, please do not mix types.',
                    318,
                ],
                [
                    'Identical: Yoda condition is not allowed here.',
                    321,
                ],
                [
                    'Identical: Do not compare boolean and string.',
                    321,
                ],
                [
                    'Identical: Insane comparison between true and \'1\'.',
                    321,
                ],
                [
                    'Identical: Insane comparison between \'1\' and true.',
                    321,
                ],
                [
                    'Equal: Yoda condition is not allowed here.',
                    327,
                ],
                [
                    'Equal: Condition between false and array{} are falsy, please do not mix types.',
                    327,
                ],
                [
                    'Equal: Use a function e.g. `count($foo) === 0` instead of `$foo == false`.',
                    327,
                ],
                [
                    'Identical: Yoda condition is not allowed here.',
                    330,
                ],
                [
                    'Identical: Use a function e.g. `count($foo) === 0` instead of `$foo == false`.',
                    330,
                ],
                [
                    'Equal: Condition between INF and true are falsy, please do not mix types.',
                    336,
                ],
                [
                    'Equal: Possible insane comparison between INF and true.',
                    336,
                ],
                [
                    'Equal: Condition between true and INF are falsy, please do not mix types.',
                    336,
                ],
                [
                    'Identical: Insane comparison between INF and false.',
                    339,
                ],
                [
                    'Identical: Insane comparison between false and INF.',
                    339,
                ],
                [
                    'Equal: Condition between NAN and \'NaN\' are falsy, please do not mix types.',
                    345,
                ],
                [
                    'Equal: Insane comparison between NAN and \'NaN\'.',
                    345,
                ],
                [
                    'Equal: Condition between \'NaN\' and NAN are falsy, please do not mix types.',
                    345,
                ],
                [
                    'Equal: Insane comparison between \'NaN\' and NAN.',
                    345,
                ],
                [
                    'Identical: Insane comparison between NAN and \'NaN\'.',
                    348,
                ],
                [
                    'Identical: Insane comparison between \'NaN\' and NAN.',
                    348,
                ],
                [
                    'Equal: Condition between true and 1.0E+27 are falsy, please do not mix types.',
                    355,
                ],
                [
                    'Equal: Condition between 1.0E+27 and true are falsy, please do not mix types.',
                    355,
                ],
                [
                    'Equal: Possible insane comparison between 1.0E+27 and true.',
                    355,
                ],
                [
                    'Identical: Insane comparison between true and 1.0E+27.',
                    358,
                ],
                [
                    'Identical: Insane comparison between 1.0E+27 and true.',
                    358,
                ],
                [
                    'Equal: Condition between false and \'function\' are falsy, please do not mix types.',
                    365,
                ],
                [
                    'Equal: Do not compare boolean and string.',
                    365,
                ],
                [
                    'Equal: Insane comparison between false and \'function\'.',
                    365,
                ],
                [
                    'Equal: Condition between \'function\' and false are falsy, please do not mix types.',
                    365,
                ],
                [
                    'Equal: Insane comparison between \'function\' and false.',
                    365,
                ],
                [
                    'Identical: Do not compare boolean and string.',
                    368,
                ],
                [
                    'Identical: Insane comparison between false and \'function\'.',
                    368,
                ],
                [
                    'Identical: Insane comparison between \'function\' and false.',
                    368,
                ],
                [
                    'Equal: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    374,
                ],
                [
                    'Equal: Do not compare objects directly, stdClass and class-string<stdClass> found.',
                    374,
                ],
                [
                    'Identical: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    377,
                ],
                [
                    'Equal: Condition between 3.14 and \'2023-10-01\' are falsy, please do not mix types.',
                    383,
                ],
                [
                    'Equal: Insane comparison between 3.14 and \'2023-10-01\'.',
                    383,
                ],
                [
                    'Equal: Condition between \'2023-10-01\' and 3.14 are falsy, please do not mix types.',
                    383,
                ],
                [
                    'Equal: Insane comparison between \'2023-10-01\' and 3.14.',
                    383,
                ],
                [
                    'Identical: Insane comparison between 3.14 and \'2023-10-01\'.',
                    386,
                ],
                [
                    'Identical: Insane comparison between \'2023-10-01\' and 3.14.',
                    386,
                ],
                [
                    'Equal: Condition between true and \'{"key": "value"}\' are falsy, please do not mix types.',
                    392,
                ],
                [
                    'Equal: Do not compare boolean and string.',
                    392,
                ],
                [
                    'Equal: Condition between \'{"key": "value"}\' and true are falsy, please do not mix types.',
                    392,
                ],
                [
                    'Equal: Possible insane comparison between \'{"key": "value"}\' and true.',
                    392,
                ],
                [
                    'Identical: Do not compare boolean and string.',
                    395,
                ],
                [
                    'Identical: Insane comparison between true and \'{"key": "value"}\'.',
                    395,
                ],
                [
                    'Identical: Insane comparison between \'{"key": "value"}\' and true.',
                    395,
                ],
                [
                    'Equal: Condition between true and \'/^pattern/\' are falsy, please do not mix types.',
                    411,
                ],
                [
                    'Equal: Do not compare boolean and string.',
                    411,
                ],
                [
                    'Equal: Condition between \'/^pattern/\' and true are falsy, please do not mix types.',
                    411,
                ],
                [
                    'Equal: Possible insane comparison between \'/^pattern/\' and true.',
                    411,
                ],
                [
                    'Identical: Do not compare boolean and string.',
                    414,
                ],
                [
                    'Identical: Insane comparison between true and \'/^pattern/\'.',
                    414,
                ],
                [
                    'Identical: Insane comparison between \'/^pattern/\' and true.',
                    414,
                ],
                [
                    'Equal: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    431,
                ],
                [
                    'Equal: Do not compare objects directly, stdClass and array{1, 2, 3} found.',
                    431,
                ],
                [
                    'Identical: Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.',
                    434,
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
                    'Equal: Condition between 0.0 and \'\' are falsy, please do not mix types.',
                    7,
                ],
                [
                    'Equal: Insane comparison between 0.0 and \'\'.',
                    7,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    7,
                ],
                [
                    'Equal: Condition between \'\' and 0.0 are falsy, please do not mix types.',
                    7,
                ],
                [
                    'Equal: Insane comparison between \'\' and 0.0.',
                    7,
                ],
                [
                    'NotEqual: Condition between 0.0 and \'\' are falsy, please do not mix types.',
                    11,
                ],
                [
                    'NotEqual: Please do not use empty-string check for numeric values. e.g. `0 != \'\'` is not working with >= PHP 8.',
                    11,
                ],
                [
                    'NotEqual: Condition between \'\' and 0.0 are falsy, please do not mix types.',
                    11,
                ],
                [
                    'NotEqual: Possible insane comparison between \'\' and 0.0.',
                    11,
                ],
                [
                    'Equal: Condition between 0 and \'\' are falsy, please do not mix types.',
                    15,
                ],
                [
                    'Equal: Insane comparison between 0 and \'\'.',
                    15,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    15,
                ],
                [
                    'Equal: Condition between \'\' and 0 are falsy, please do not mix types.',
                    15,
                ],
                [
                    'Equal: Insane comparison between \'\' and 0.',
                    15,
                ],
                [
                    'NotEqual: Condition between 0 and \'\' are falsy, please do not mix types.',
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
                    'NotEqual: Condition between \'\' and 0 are falsy, please do not mix types.',
                    19,
                ],
                [
                    'NotEqual: Possible insane comparison between \'\' and 0.',
                    19,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    23,
                ],
                [
                    'Equal: Condition between \'\' and 0 are falsy, please do not mix types.',
                    23,
                ],
                [
                    'Equal: Insane comparison between \'\' and 0.',
                    23,
                ],
                [
                    'Equal: Condition between 0 and \'\' are falsy, please do not mix types.',
                    23,
                ],
                [
                    'Equal: Insane comparison between 0 and \'\'.',
                    23,
                ],
                [
                    'Equal: Condition between 0|null and \'\' are falsy, please do not mix types.',
                    27,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    27,
                ],
                [
                    'Equal: Condition between \'\' and 0|null are falsy, please do not mix types.',
                    27,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and 0|null.',
                    27,
                ],
                [
                    'Equal: Condition between 0 and \'0foo\' are falsy, please do not mix types.',
                    34,
                ],
                [
                    'Equal: Insane comparison between 0 and \'0foo\'.',
                    34,
                ],
                [
                    'Equal: Condition between \'0foo\' and 0 are falsy, please do not mix types.',
                    34,
                ],
                [
                    'Equal: Insane comparison between \'0foo\' and 0.',
                    34,
                ],
                [
                    'Identical: Insane comparison between 0 and \'0foo\'.',
                    37,
                ],
                [
                    'Identical: Insane comparison between \'0foo\' and 0.',
                    37,
                ],
                [
                    'NotEqual: Condition between \'0foo\' and 1 are falsy, please do not mix types.',
                    40,
                ],
                [
                    'NotEqual: Possible insane comparison between \'0foo\' and 1.',
                    40,
                ],
                [
                    'NotEqual: Condition between 1 and \'0foo\' are falsy, please do not mix types.',
                    40,
                ],
                [
                    'Equal: Condition between \'3\' and true are falsy, please do not mix types.',
                    43,
                ],
                [
                    'Equal: Possible insane comparison between \'3\' and true.',
                    43,
                ],
                [
                    'Equal: Condition between true and \'3\' are falsy, please do not mix types.',
                    43,
                ],
                [
                    'Equal: Do not compare boolean and string.',
                    43,
                ],
                [
                    'Equal: Condition between 0|1 and true are falsy, please do not mix types.',
                    47,
                ],
                [
                    'Equal: Condition between true and 0|1 are falsy, please do not mix types.',
                    47,
                ],
                [
                    'Equal: Yoda condition is not allowed here.',
                    51,
                ],
                [
                    'Equal: Condition between \'0.000\' and 0|1 are falsy, please do not mix types.',
                    51,
                ],
                [
                    'Equal: Condition between 0|1 and \'0.000\' are falsy, please do not mix types.',
                    51,
                ],
                [
                    'Equal: Yoda condition is not allowed here.',
                    55,
                ],
                [
                    'Equal: Condition between \'0.000\' and 3|null are falsy, please do not mix types.',
                    55,
                ],
                [
                    'Equal: Possible insane comparison between \'0.000\' and 3|null.',
                    55,
                ],
                [
                    'Equal: Condition between 3|null and \'0.000\' are falsy, please do not mix types.',
                    55,
                ],
                [
                    'Equal: Yoda condition is not allowed here.',
                    59,
                ],
                [
                    'Equal: Yoda condition is not allowed here.',
                    63,
                ],
                [
                    'Equal: Condition between null and 0|3 are falsy, please do not mix types.',
                    63,
                ],
                [
                    'Equal: Possible insane comparison between null and 0|3.',
                    63,
                ],
                [
                    'Equal: Condition between 0|3 and null are falsy, please do not mix types.',
                    63,
                ],
                [
                    'NotEqual: Condition between 1 and \'1\' are falsy, please do not mix types.',
                    66,
                ],
                [
                    'NotEqual: Insane comparison between 1 and \'1\'.',
                    66,
                ],
                [
                    'NotEqual: Condition between \'1\' and 1 are falsy, please do not mix types.',
                    66,
                ],
                [
                    'NotEqual: Insane comparison between \'1\' and 1.',
                    66,
                ],
                [
                    'NotIdentical: Insane comparison between 1 and 1.',
                    69,
                ],
                [
                    'NotIdentical: Insane comparison between 1 and 1.',
                    69,
                ],
                [
                    'Equal: Condition between 0 and \'0\' are falsy, please do not mix types.',
                    72,
                ],
                [
                    'Equal: Condition between \'0\' and 0 are falsy, please do not mix types.',
                    72,
                ],
                [
                    'Equal: Possible insane comparison between \'0\' and 0.',
                    72,
                ],
                [
                    'Identical: Insane comparison between 0 and \'0\'.',
                    75,
                ],
                [
                    'Identical: Insane comparison between \'0\' and 0.',
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
                    'Equal: Condition between 0.0 and \'\' are falsy, please do not mix types.',
                    7,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    7,
                ],
                [
                    'Equal: Condition between \'\' and 0.0 are falsy, please do not mix types.',
                    7,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and 0.0.',
                    7,
                ],
                [
                    'NotEqual: Condition between 0.0 and \'\' are falsy, please do not mix types.',
                    11,
                ],
                [
                    'NotEqual: Insane comparison between 0.0 and \'\'.',
                    11,
                ],
                [
                    'NotEqual: Please do not use empty-string check for numeric values. e.g. `0 != \'\'` is not working with >= PHP 8.',
                    11,
                ],
                [
                    'NotEqual: Condition between \'\' and 0.0 are falsy, please do not mix types.',
                    11,
                ],
                [
                    'NotEqual: Insane comparison between \'\' and 0.0.',
                    11,
                ],
                [
                    'Equal: Condition between 0 and \'\' are falsy, please do not mix types.',
                    15,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    15,
                ],
                [
                    'Equal: Condition between \'\' and 0 are falsy, please do not mix types.',
                    15,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and 0.',
                    15,
                ],
                [
                    'NotEqual: Condition between 0 and \'\' are falsy, please do not mix types.',
                    19,
                ],
                [
                    'NotEqual: Insane comparison between 0 and \'\'.',
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
                    'NotEqual: Condition between \'\' and 0 are falsy, please do not mix types.',
                    19,
                ],
                [
                    'NotEqual: Insane comparison between \'\' and 0.',
                    19,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    23,
                ],
                [
                    'Equal: Condition between \'\' and 0 are falsy, please do not mix types.',
                    23,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and 0.',
                    23,
                ],
                [
                    'Equal: Condition between 0 and \'\' are falsy, please do not mix types.',
                    23,
                ],
                [
                    'Equal: Condition between 0|null and \'\' are falsy, please do not mix types.',
                    27,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    27,
                ],
                [
                    'Equal: Condition between \'\' and 0|null are falsy, please do not mix types.',
                    27,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and 0|null.',
                    27,
                ],
                [
                    'Equal: Condition between 0 and \'0foo\' are falsy, please do not mix types.',
                    34,
                ],
                [
                    'Equal: Condition between \'0foo\' and 0 are falsy, please do not mix types.',
                    34,
                ],
                [
                    'Equal: Possible insane comparison between \'0foo\' and 0.',
                    34,
                ],
                [
                    'Identical: Insane comparison between 0 and \'0foo\'.',
                    37,
                ],
                [
                    'Identical: Insane comparison between \'0foo\' and 0.',
                    37,
                ],
                [
                    'NotEqual: Condition between \'0foo\' and 1 are falsy, please do not mix types.',
                    40,
                ],
                [
                    'NotEqual: Possible insane comparison between \'0foo\' and 1.',
                    40,
                ],
                [
                    'NotEqual: Condition between 1 and \'0foo\' are falsy, please do not mix types.',
                    40,
                ],
                [
                    'Equal: Condition between \'3\' and true are falsy, please do not mix types.',
                    43,
                ],
                [
                    'Equal: Possible insane comparison between \'3\' and true.',
                    43,
                ],
                [
                    'Equal: Condition between true and \'3\' are falsy, please do not mix types.',
                    43,
                ],
                [
                    'Equal: Do not compare boolean and string.',
                    43,
                ],
                [
                    'Equal: Condition between 0|1 and true are falsy, please do not mix types.',
                    47,
                ],
                [
                    'Equal: Condition between true and 0|1 are falsy, please do not mix types.',
                    47,
                ],
                [
                    'Equal: Yoda condition is not allowed here.',
                    51,
                ],
                [
                    'Equal: Condition between \'0.000\' and 0|1 are falsy, please do not mix types.',
                    51,
                ],
                [
                    'Equal: Condition between 0|1 and \'0.000\' are falsy, please do not mix types.',
                    51,
                ],
                [
                    'Equal: Yoda condition is not allowed here.',
                    55,
                ],
                [
                    'Equal: Condition between \'0.000\' and 3|null are falsy, please do not mix types.',
                    55,
                ],
                [
                    'Equal: Possible insane comparison between \'0.000\' and 3|null.',
                    55,
                ],
                [
                    'Equal: Condition between 3|null and \'0.000\' are falsy, please do not mix types.',
                    55,
                ],
                [
                    'Equal: Yoda condition is not allowed here.',
                    59,
                ],
                [
                    'Equal: Yoda condition is not allowed here.',
                    63,
                ],
                [
                    'Equal: Condition between null and 0|3 are falsy, please do not mix types.',
                    63,
                ],
                [
                    'Equal: Possible insane comparison between null and 0|3.',
                    63,
                ],
                [
                    'Equal: Condition between 0|3 and null are falsy, please do not mix types.',
                    63,
                ],
                [
                    'NotEqual: Condition between 1 and \'1\' are falsy, please do not mix types.',
                    66,
                ],
                [
                    'NotEqual: Insane comparison between 1 and \'1\'.',
                    66,
                ],
                [
                    'NotEqual: Condition between \'1\' and 1 are falsy, please do not mix types.',
                    66,
                ],
                [
                    'NotEqual: Insane comparison between \'1\' and 1.',
                    66,
                ],
                [
                    'NotIdentical: Insane comparison between 1 and 1.',
                    69,
                ],
                [
                    'NotIdentical: Insane comparison between 1 and 1.',
                    69,
                ],
                [
                    'Equal: Condition between 0 and \'0\' are falsy, please do not mix types.',
                    72,
                ],
                [
                    'Equal: Condition between \'0\' and 0 are falsy, please do not mix types.',
                    72,
                ],
                [
                    'Equal: Possible insane comparison between \'0\' and 0.',
                    72,
                ],
                [
                    'Identical: Insane comparison between 0 and \'0\'.',
                    75,
                ],
                [
                    'Identical: Insane comparison between \'0\' and 0.',
                    75,
                ],
            ]
        );
    }
}
