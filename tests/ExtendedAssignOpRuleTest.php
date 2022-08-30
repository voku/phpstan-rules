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
                    'Concat: Do not cast objects magically, please use `__toString` here, voku\PHPStan\Rules\Test\fixtures\MyId and \'My favorite…\' found.',
                    86,
                ],
            ]
        );
    }
}
