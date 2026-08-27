<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\IfConditionRule;

/**
 * The comparisons whose result changed between PHP 7 and PHP 8, plus the "double negative"
 * comparisons, are the headline feature of this package - and they were only covered indirectly,
 * inside a 500-line fixture whose expectations are mostly about something else.
 *
 * Here every case is one method in the fixture and the full error list is asserted with the
 * *default* configuration, so a change that drops one of these checks fails with a message that
 * names the case.
 *
 * @extends RuleTestCase<IfConditionRule>
 */
final class Php8ComparisonSemanticsTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new IfConditionRule([], $this->createReflectionProvider(), false, false);
    }

    public function testPhp8ComparisonSemantics(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/Php8ComparisonSemanticsFixtures.php',
            ],
            [
                // int == '' - true on PHP 7, false on PHP 8
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    23,
                ],
                [
                    'Equal: Condition between \'\' and int are falsy, please do not mix types.',
                    23,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and int.',
                    23,
                ],

                // int != ''
                [
                    'NotEqual: Please do not use empty-string check for numeric values. e.g. `0 != \'\'` is not working with >= PHP 8.',
                    31,
                ],
                [
                    'NotEqual: Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    31,
                ],
                [
                    'NotEqual: Condition between \'\' and int are falsy, please do not mix types.',
                    31,
                ],
                [
                    'NotEqual: Possible insane comparison between \'\' and int.',
                    31,
                ],

                // int == 'foo' - true on PHP 7, false on PHP 8
                [
                    'Equal: Condition between \'foo\' and int are falsy, please do not mix types.',
                    41,
                ],
                [
                    'Equal: Possible insane comparison between \'foo\' and int.',
                    41,
                ],

                // int == '0foo' - true on PHP 7, false on PHP 8
                [
                    'Equal: Condition between \'0foo\' and int are falsy, please do not mix types.',
                    49,
                ],
                [
                    'Equal: Possible insane comparison between \'0foo\' and int.',
                    49,
                ],

                // float == ''
                [
                    'Equal: Please do not use empty-string check for numeric values. e.g. `0 == \'\'` is not working with >= PHP 8.',
                    54,
                ],
                [
                    'Equal: Condition between \'\' and float are falsy, please do not mix types.',
                    54,
                ],
                [
                    'Equal: Possible insane comparison between \'\' and float.',
                    54,
                ],

                // string != '' - a non-constant string must not produce an "insane comparison"
                [
                    'NotEqual: Please do not use double negative string conditions. e.g. `(string)$foo != \'\'` is the same as `(string)$foo`.',
                    62,
                ],

                // int != 0
                [
                    'NotEqual: Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.',
                    72,
                ],

                // bool != false - reported as a boolean problem, not as an integer one
                [
                    'NotEqual: Please do not use double negative boolean conditions. e.g. `(bool)$foo != false` is the same as `(bool)$foo`.',
                    82,
                ],

                // int != null
                [
                    'NotEqual: Please do not use double negative null conditions. Use "!==" instead if needed.',
                    92,
                ],
                [
                    'NotEqual: Condition between null and int are falsy, please do not mix types.',
                    92,
                ],
                [
                    'NotEqual: Possible insane comparison between null and int.',
                    92,
                ],
            ]
        );
    }
}
