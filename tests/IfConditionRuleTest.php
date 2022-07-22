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
                    'Equal: Insane comparison between 0.0 and \'\'',
                    148,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    148,
                ],
                [
                    'Equal: Insane comparison between \'\' and 0.0',
                    148,
                ],
                [
                    'NotEqual: Please do not use empty-string check for numeric values. e.g. `0 != \'\'` is not working with >= PHP 8.',
                    152,    
                ],
                [
                    'NotEqual: Possible insane comparison between \'\' and 0.0',
                    152,
                ],
                [
                    'Equal: Insane comparison between 0 and \'\'',
                    156,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    156,    
                ],
                [
                    'Equal: Insane comparison between \'\' and 0',
                    156,    
                ],
                [
                    'NotEqual: Please do not use empty-string check for numeric values. e.g. `0 != \'\'` is not working with >= PHP 8.',
                    160,
                ],
                [
                    'NotEqual: Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    160,
                ],
                [
                    'NotEqual: Possible insane comparison between \'\' and 0',
                    160,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    164,
                ],
                [
                    'Equal: Insane comparison between \'\' and 0',
                    164,
                ],
                [
                    'Equal: Insane comparison between 0 and \'\'',
                    164,
                ],
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    168,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and 0|null',
                    168,
                ],
                [
                    'Equal: Insane comparison between 0 and \'0foo\'',
                    175,
                ],
                [
                    'Equal: Insane comparison between \'0foo\' and 0',  
                    175,
                ],
                [
                    'Identical: Insane comparison between 0 and \'0foo\'',
                    178,
                ],
                [
                    'Identical: Insane comparison between \'0foo\' and 0',
                    178,
                ],
                [
                    'NotEqual: Possible insane comparison between \'0foo\' and 1',
                    181,
                ],
                [
                    'Equal: Possible insane comparison between \'3\' and true',
                    184,
                ],
                [
                    'Equal: Do not compare boolean and string.',
                    184,
                ],
                [
                    'Equal: Possible insane comparison between \'0.000\' and 3|null',
                    196,
                ],
                [
                    'Equal: Possible insane comparison between null and 0|3',
                    204,
                ],
                [
                    'NotEqual: Insane comparison between 1 and \'1\'',
                    207,
                ],
                [
                    'NotEqual: Insane comparison between \'1\' and 1',
                    207,
                ],
                [
                    'NotIdentical: Insane comparison between 1 and 1',
                    210,
                ],
                [
                    'NotIdentical: Insane comparison between 1 and 1',
                    210,
                ],
                [
                    'Equal: Possible insane comparison between \'0\' and 0',
                    213,
                ],
                [
                    'Identical: Insane comparison between 0 and \'0\'',
                    216,
                ],
                [
                    'Identical: Insane comparison between \'0\' and 0',
                    216,
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
}
