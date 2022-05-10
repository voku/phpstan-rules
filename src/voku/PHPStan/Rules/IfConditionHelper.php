<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Enum\EnumCaseObjectType;
use PHPStan\Type\VerbosityLevel;

final class IfConditionHelper
{
    /**
     * @param \PHPStan\Type\Type|null $type_1
     * @param \PHPStan\Type\Type|null $type_2
     * @param \PhpParser\Node $cond
     * @param array<int, \PHPStan\Rules\RuleError> $errors
     * @param array<int, class-string> $classesNotInIfConditions
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     * @throws \PHPStan\ShouldNotHappenException
     */
    public static function processNodeHelper(
        ?\PHPStan\Type\Type $type_1,
        ?\PHPStan\Type\Type $type_2,
        Node $cond,
        array $errors,
        array $classesNotInIfConditions
    ): array
    {

        // DEBUG
        //var_dump(get_class($type_1), get_class($cond), get_class($type_2));

        self::processNotEqualRules($type_1, $type_2, $cond, $errors);

        // -----------------------------------------------------------------------------------------

        self::processBooleanComparison($type_1, $type_2, $cond, $errors);

        // -----------------------------------------------------------------------------------------

        self::processObjectMethodUsageForComparison($type_1, $cond, $errors, $classesNotInIfConditions);

        // -----------------------------------------------------------------------------------------

        self::processObjectComparison($type_1, $type_2, $cond, $errors);

        // -----------------------------------------------------------------------------------------

        self::processNonEmptyStrings($type_1, $type_2, $cond, $errors);

        // -----------------------------------------------------------------------------------------

        return $errors;
    }

    /**
     * @param \PHPStan\Type\Type|null $type_1
     * @param \PHPStan\Type\Type|null $type_2
     * @param Node $cond
     * @param array<int, \PHPStan\Rules\RuleError> $errors
     *
     * @throws \PHPStan\ShouldNotHappenException
     */
    private static function processNotEqualRules(
        ?\PHPStan\Type\Type $type_1,
        ?\PHPStan\Type\Type $type_2,
        Node                $cond,
        array               &$errors
    ): void
    {
        if (! $cond instanceof \PhpParser\Node\Expr\BinaryOp\NotEqual) {
            return;
        }

        if (
            $type_1 instanceof \PHPStan\Type\Constant\ConstantStringType
            &&
            $type_1->getValue() === ''
            &&
            $type_2 instanceof \PHPStan\Type\StringType
        ) {
            $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Please do not use double negative string conditions. e.g. `(string)$foo != \'\'` is the same as `(string)$foo`.')->line($cond->getAttribute('startLine'))->build();
        }

        if (
            (
                ($type_1 instanceof \PHPStan\Type\Constant\ConstantStringType && $type_1->getValue() === '')
                ||
                ($type_1 instanceof \PHPStan\Type\Constant\ConstantIntegerType && $type_1->getValue() === 0)
                ||
                ($type_1 instanceof \PHPStan\Type\Constant\ConstantBooleanType && $type_1->getValue() === false)
            )
            &&
            (
                $type_2 instanceof \PHPStan\Type\IntegerType
                ||
                (
                    $type_2 instanceof \PHPStan\Type\UnionType
                    &&
                    $type_2->getTypes()[0] instanceof \PHPStan\Type\IntegerType
                    &&
                    $type_2->getTypes()[1] instanceof \PHPStan\Type\NullType
                )
            )
        ) {
            $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.')->line($cond->getAttribute('startLine'))->build();
        }

        if (
            (
                ($type_1 instanceof \PHPStan\Type\Constant\ConstantStringType && $type_1->getValue() === '')
                ||
                ($type_1 instanceof \PHPStan\Type\Constant\ConstantIntegerType && $type_1->getValue() === 0)
                ||
                ($type_1 instanceof \PHPStan\Type\Constant\ConstantBooleanType && $type_1->getValue() === false)
            )
            &&
            (
                $type_2 instanceof \PHPStan\Type\BooleanType
                ||
                (
                    $type_2 instanceof \PHPStan\Type\UnionType
                    &&
                    $type_2->getTypes()[0] instanceof \PHPStan\Type\BooleanType
                    &&
                    $type_2->getTypes()[1] instanceof \PHPStan\Type\NullType
                )
            )
        ) {
            $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Please do not use double negative boolean conditions. e.g. `(bool)$foo != false` is the same as `(bool)$foo`.')->line($cond->getAttribute('startLine'))->build();
        }

        // NULL checks are difficult and maybe unexpected, so that we should use strict check here
        // https://3v4l.org/a4VdC
        if (
            $type_1 instanceof \PHPStan\Type\ConstantScalarType
            &&
            $type_1->getValue() === null
            &&
            (
                (
                    $type_2 instanceof \PHPStan\Type\UnionType
                    &&
                    $type_2->getTypes()[0] instanceof \PHPStan\Type\IntegerType
                    &&
                    $type_2->getTypes()[1] instanceof \PHPStan\Type\NullType
                )
                ||
                (
                    $type_2 instanceof \PHPStan\Type\UnionType
                    &&
                    $type_2->getTypes()[0] instanceof \PHPStan\Type\StringType
                    &&
                    $type_2->getTypes()[1] instanceof \PHPStan\Type\NullType
                )
            )
        ) {
            $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Please do not use double negative null conditions. Use "!==" instead if needed.')->line($cond->getAttribute('startLine'))->build();
        }
    }

    /**
     * @param \PHPStan\Type\Type|null $type_1
     * @param \PHPStan\Type\Type|null $type_2
     * @param Node $cond
     * @param array<int, \PHPStan\Rules\RuleError> $errors
     *
     * @throws \PHPStan\ShouldNotHappenException
     */
    private static function processBooleanComparison(
        ?\PHPStan\Type\Type $type_1,
        ?\PHPStan\Type\Type $type_2,
        Node                $cond,
        array               &$errors
    ): void
    {
        if (! $type_1 instanceof \PHPStan\Type\BooleanType) {
            return;
        }

        if ($type_2 instanceof \PHPStan\Type\Constant\ConstantIntegerType) {
            $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Do not compare boolean and integer.')->line($cond->getAttribute('startLine'))->build();
        }

        if ($type_2 instanceof \PHPStan\Type\Constant\ConstantStringType) {
            $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Do not compare boolean and string.')->line($cond->getAttribute('startLine'))->build();
        }
    }

    /**
     * @param \PHPStan\Type\Type|null $type_1
     * @param \PHPStan\Type\Type|null $type_2
     * @param Node $cond
     * @param array<int, \PHPStan\Rules\RuleError> $errors
     *
     * @throws \PHPStan\ShouldNotHappenException
     */
    private static function processObjectComparison(
        ?\PHPStan\Type\Type $type_1,
        ?\PHPStan\Type\Type $type_2,
        Node                $cond,
        array               &$errors
    ): void
    {
        if (
            $cond instanceof \PhpParser\Node\Expr\BinaryOp\Identical
            ||
            $cond instanceof \PhpParser\Node\Expr\BinaryOp\NotIdentical
            ||
            $cond instanceof \PhpParser\Node\Expr\BinaryOp\Coalesce
        ) {
            return;
        }

        if (! self::isObjectOrNullType($type_1)) {
            return;
        }

        $errors[] = \PHPStan\Rules\RuleErrorBuilder::message(sprintf(
            'Do not compare objects directly, %s found.',
            $type_1->describe(VerbosityLevel::value())
        ))->line($cond->getAttribute('startLine'))->build();
    }

    /**
     * @param \PHPStan\Type\Type|null $type_1
     * @param \PHPStan\Type\Type|null $type_2
     * @param Node $cond
     * @param array<int, \PHPStan\Rules\RuleError> $errors
     *
     * @throws \PHPStan\ShouldNotHappenException
     */
    private static function processNonEmptyStrings(
        ?\PHPStan\Type\Type $type_1,
        ?\PHPStan\Type\Type $type_2,
        Node                $cond,
        array               &$errors
    ): void
    {
        if (!(
            $type_1 instanceof \PHPStan\Type\Constant\ConstantStringType
            &&
            $type_1->getValue() === ''
            &&
            $type_2->isNonEmptyString()->yes()

        )) {
            return;
        }

        if (
            $cond instanceof \PhpParser\Node\Expr\BinaryOp\NotEqual
            ||
            $cond instanceof \PhpParser\Node\Expr\BinaryOp\NotIdentical
        ) {
            $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Non-empty string is never empty.')->line($cond->getAttribute('startLine'))->build();
        }

        if (
            $cond instanceof \PhpParser\Node\Expr\BinaryOp\Equal
            ||
            $cond instanceof \PhpParser\Node\Expr\BinaryOp\Identical
        ) {
            $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Non-empty string is always non-empty.')->line($cond->getAttribute('startLine'))->build();
        }
    }

    /**
     * @param \PHPStan\Type\Type|null $type_1
     * @param Node $cond
     * @param array<int, \PHPStan\Rules\RuleError> $errors
     * @param array<int, class-string> $classesNotInIfConditions
     *
     * @throws \PHPStan\ShouldNotHappenException
     */
    private static function processObjectMethodUsageForComparison(
        ?\PHPStan\Type\Type $type_1,
        Node                $cond,
        array               &$errors,
        array               $classesNotInIfConditions
    ): void
    {
        foreach ($classesNotInIfConditions as $classesNotInIfCondition) {
            if (
                $type_1 instanceof \PHPStan\Type\ObjectType
                &&
                \is_a($type_1->getClassName(), $classesNotInIfCondition, true)
            ) {
                $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.')->line($cond->getAttribute('startLine'))->build();
            }
        }
    }

    private static function isObjectOrNullType(?\PHPStan\Type\Type $type): bool
    {
        if (
            $type instanceof \PHPStan\Type\ObjectType
            ||
            $type instanceof \PHPStan\Type\StaticType
            ||
            $type instanceof \PHPStan\Type\NullType
        ) {
            return true;
        }

        if (! $type instanceof  \PHPStan\Type\UnionType) {
            return false;
        }

        $return = true;
        foreach ($type->getTypes() as $typeFromUnion) {
            $return = $return && self::isObjectOrNullType($typeFromUnion);
        }

        return $return;
    }
}
