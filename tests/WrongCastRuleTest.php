<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\WrongCastRule;
use voku\PHPStan\Rules\Test\fixtures\WrongCastBaseClass;

/**
 * @extends RuleTestCase<WrongCastRule>
 */
final class WrongCastRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new WrongCastRule([WrongCastBaseClass::class]);
    }

    public function testWrongCastInsideCheckedClass(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/WrongCastBaseClass.php',
                __DIR__ . '/fixtures/WrongCastCheckedClass.php',
            ],
            [
                [
                    "Casting to int something that's string.",
                    14,
                ],
                [
                    "Casting to int something that's string.",
                    19,
                ],
            ]
        );
    }

    public function testNoErrorsForClassNotInCheckedList(): void
    {
        // Classes not extending WrongCastBaseClass should not trigger errors
        $this->analyse(
            [
                __DIR__ . '/fixtures/WrongCastUncheckedClass.php',
            ],
            []
        );
    }

    public function testWrongCastInsideSubCheckedClass(): void
    {
        // Even sub-subclasses of the checked base class should trigger errors
        $this->analyse(
            [
                __DIR__ . '/fixtures/WrongCastBaseClass.php',
                __DIR__ . '/fixtures/WrongCastCheckedClass.php',
                __DIR__ . '/fixtures/WrongCastSubCheckedClass.php',
            ],
            [
                [
                    "Casting to int something that's string.",
                    14,
                ],
                [
                    "Casting to int something that's string.",
                    19,
                ],
                [
                    "Casting to int something that's string.",
                    10,
                ],
            ]
        );
    }
}
