<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\ExtendedAssignOpRule;

/**
 * @extends RuleTestCase<ExtendedAssignOpRule>
 */
final class ExtendedAssignOpRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ExtendedAssignOpRule($this->createReflectionProvider());
    }

    public function testExtendedBinaryOpRule(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/ExtendedAssignOpRuleFixtures.php',
            ],
            [
                [
                    'Plus: string (\'Hello World!\') in combination with non-string (6) is not allowed.',
                    10,
                ],
                [
                    'Plus: string (\'Hello World!\') in combination with non-string (array{}) is not allowed.',
                    13,
                ],
                [
                    'Concat: Do not compare objects directly, stdClass and \'Hello World!\' found.',
                    16,
                ],
                [
                    'Concat: string (\'Hello World!\') in combination with non-string (stdClass) is not allowed.',
                    16,
                ],
                [
                    'Plus: array (array{lall: int, foo: 1}) in combination with non-array (\'foo\') is not allowed.',
                    28,
                ],        
                [
                    'Plus: string (\'foo\') in combination with non-string (int) is not allowed.',
                    33,
                ],
                [
                    'Concat: Do not cast objects magically, please use `__toString` here, voku\PHPStan\Rules\Test\fixtures\MyId and \'My favorite…\' found.',
                    41,
                ],
            ]
        );
    }
}
