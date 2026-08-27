<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PhpParser\Node;
use PHPStan\Rules\LineRuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\StringType;
use PHPStan\Type\TypeCombinator;
use voku\PHPStan\Rules\IfConditionHelper;

/**
 * `IfConditionHelper` carries the whole comparison logic of this package, but every one of its
 * public helpers was only ever exercised through the analyser. A wrong answer from one of them
 * shows up as a missing or extra message somewhere in a 500-line fixture, which is a terrible place
 * to debug from - and the message-prefix/identifier derivation was not covered at all.
 *
 * These are plain unit tests: no fixture, no analyser, one behaviour per assertion.
 */
final class IfConditionHelperTest extends PHPStanTestCase
{
    /**
     * The message prefix keeps the raw PhpParser node name (`If_`), the identifier strips the
     * underscore (`voku.If`). Both halves are public API: the prefix ends up in the reported
     * message, the identifier in the user's baseline.
     *
     * @dataProvider provideOriginalNodes
     */
    public function testBuildErrorMessageDerivesPrefixAndIdentifierFromTheNode(
        Node $originalNode,
        string $expectedPrefix,
        string $expectedIdentifier
    ): void {
        $error = IfConditionHelper::buildErrorMessage($originalNode, 'Something is wrong.', 42);

        static::assertSame($expectedPrefix . ': Something is wrong.', $error->getMessage());
        static::assertSame($expectedIdentifier, $error->getIdentifier());
        static::assertInstanceOf(LineRuleError::class, $error);
        static::assertSame(42, $error->getLine());
    }

    /**
     * @return array<string, array{0: Node, 1: string, 2: string}>
     */
    public function provideOriginalNodes(): array
    {
        $true = new Node\Expr\ConstFetch(new Node\Name('true'));

        return [
            'If_' => [new Node\Stmt\If_($true), 'If_', 'voku.If'],
            'ElseIf_' => [new Node\Stmt\ElseIf_($true, []), 'ElseIf_', 'voku.ElseIf'],
            'Switch_' => [new Node\Stmt\Switch_($true, []), 'Switch_', 'voku.Switch'],
            'BooleanNot' => [new Node\Expr\BooleanNot($true), 'BooleanNot', 'voku.BooleanNot'],
            'Ternary' => [new Node\Expr\Ternary($true, null, $true), 'Ternary', 'voku.Ternary'],
            'Equal' => [new Node\Expr\BinaryOp\Equal($true, $true), 'Equal', 'voku.Equal'],
            'NotIdentical' => [new Node\Expr\BinaryOp\NotIdentical($true, $true), 'NotIdentical', 'voku.NotIdentical'],
        ];
    }

    /**
     * The rules deliberately run the same checks twice (left/right and right/left), so the same
     * problem is found more than once. Deduplication is what keeps that from reaching the user.
     */
    public function testDeduplicateErrorsRemovesTheSameMessageOnTheSameLine(): void
    {
        $errors = [
            self::error('Duplicated.', 10),
            self::error('Duplicated.', 10),
            self::error('Duplicated.', 10),
        ];

        static::assertCount(1, IfConditionHelper::deduplicateErrors($errors));
    }

    /**
     * The same message on a different line is a different problem and has to survive.
     */
    public function testDeduplicateErrorsKeepsTheSameMessageOnDifferentLines(): void
    {
        $errors = [
            self::error('Duplicated.', 10),
            self::error('Duplicated.', 11),
        ];

        static::assertCount(2, IfConditionHelper::deduplicateErrors($errors));
    }

    public function testDeduplicateErrorsKeepsDifferentMessagesOnTheSameLine(): void
    {
        $errors = [
            self::error('First.', 10),
            self::error('Second.', 10),
        ];

        static::assertCount(2, IfConditionHelper::deduplicateErrors($errors));
    }

    /**
     * Order matters for the reported output, so deduplication has to keep the first occurrence.
     */
    public function testDeduplicateErrorsPreservesOrder(): void
    {
        $errors = [
            self::error('First.', 10),
            self::error('Second.', 10),
            self::error('First.', 10),
        ];

        $messages = [];
        foreach (IfConditionHelper::deduplicateErrors($errors) as $error) {
            $messages[] = $error->getMessage();
        }

        static::assertSame(['First.', 'Second.'], $messages);
    }

    public function testHasConstantStringValue(): void
    {
        static::assertTrue(IfConditionHelper::hasConstantStringValue(new ConstantStringType(''), ''));
        static::assertFalse(IfConditionHelper::hasConstantStringValue(new ConstantStringType('x'), ''));
        static::assertFalse(IfConditionHelper::hasConstantStringValue(new StringType(), ''));
        static::assertFalse(IfConditionHelper::hasConstantStringValue(null, ''));

        // a union of two constant strings is not a single constant value
        static::assertFalse(
            IfConditionHelper::hasConstantStringValue(
                TypeCombinator::union(new ConstantStringType(''), new ConstantStringType('x')),
                ''
            )
        );
    }

    /**
     * `0`, `''` and `false` are all falsy but they are not the same constant, and the double
     * negative rules branch on exactly that distinction.
     */
    public function testHasConstantScalarValueComparesStrictly(): void
    {
        static::assertTrue(IfConditionHelper::hasConstantScalarValue(new ConstantIntegerType(0), 0));
        static::assertTrue(IfConditionHelper::hasConstantScalarValue(new ConstantBooleanType(false), false));
        static::assertTrue(IfConditionHelper::hasConstantScalarValue(new ConstantStringType(''), ''));

        static::assertFalse(IfConditionHelper::hasConstantScalarValue(new ConstantIntegerType(0), false));
        static::assertFalse(IfConditionHelper::hasConstantScalarValue(new ConstantIntegerType(0), ''));
        static::assertFalse(IfConditionHelper::hasConstantScalarValue(new ConstantBooleanType(false), 0));
        static::assertFalse(IfConditionHelper::hasConstantScalarValue(new ConstantStringType(''), 0));
        static::assertFalse(IfConditionHelper::hasConstantScalarValue(new IntegerType(), 0));
        static::assertFalse(IfConditionHelper::hasConstantScalarValue(null, 0));
    }

    public function testIsPhpStanTypeMaybeWithUnionNullableAcceptsThePlainType(): void
    {
        static::assertTrue(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable(new IntegerType(), IntegerType::class)
        );
        static::assertFalse(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable(new StringType(), IntegerType::class)
        );
        static::assertFalse(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable(null, IntegerType::class)
        );
    }

    /**
     * "MaybeWithUnionNullable" is exactly a union of the type and null - and nothing wider.
     */
    public function testIsPhpStanTypeMaybeWithUnionNullableAcceptsOnlyANullableUnion(): void
    {
        static::assertTrue(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable(
                TypeCombinator::union(new IntegerType(), new NullType()),
                IntegerType::class
            )
        );
        static::assertTrue(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable(
                TypeCombinator::union(new NullType(), new IntegerType()),
                IntegerType::class
            )
        );

        // three members: not "type|null" any more
        static::assertFalse(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable(
                TypeCombinator::union(new IntegerType(), new StringType(), new NullType()),
                IntegerType::class
            )
        );

        // "int|string" is a union of two, but neither member is null
        static::assertFalse(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable(
                TypeCombinator::union(new IntegerType(), new StringType()),
                IntegerType::class
            )
        );
    }

    /**
     * The default generalises the type first, which is what turns a constant into its base type.
     * Callers that need the *constant* type to match therefore have to pass `false` - several
     * checks in IfConditionHelper depend on exactly this difference, e.g. the numeric-string
     * comparison that only applies to a constant integer.
     */
    public function testIsPhpStanTypeMaybeWithUnionNullableGeneralisesByDefault(): void
    {
        $constantInt = new ConstantIntegerType(5);

        // ConstantIntegerType extends IntegerType, so the base type matches either way
        static::assertTrue(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($constantInt, IntegerType::class)
        );
        static::assertTrue(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($constantInt, IntegerType::class, false)
        );

        // generalising drops the constant, so the constant class only matches without it
        static::assertFalse(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($constantInt, ConstantIntegerType::class)
        );
        static::assertTrue(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($constantInt, ConstantIntegerType::class, false)
        );
    }

    public function testIsPhpStanTypeMaybeWithUnionNullableWithUnrelatedTypes(): void
    {
        static::assertFalse(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable(new MixedType(), IntegerType::class)
        );
        static::assertFalse(
            IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable(
                new ArrayType(new IntegerType(), new StringType()),
                BooleanType::class
            )
        );
    }

    private static function error(string $message, int $line): \PHPStan\Rules\RuleError
    {
        return RuleErrorBuilder::message($message)
            ->line($line)
            ->identifier('voku.test')
            ->build();
    }
}
