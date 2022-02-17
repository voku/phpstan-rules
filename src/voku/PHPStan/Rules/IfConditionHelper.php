<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;

final class IfConditionHelper {

    /**
     * @param \PhpParser\Node\Expr $cond
     * @param array<int, class-string> $classesNotInIfConditions
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public static function processNode(Node $cond, Scope $scope, $classesNotInIfConditions): array {
        // init
        $errors = [];

        if (!property_exists($cond, 'left')) {
            return [];
        }
        if (!property_exists($cond, 'right')) {
            return [];
        }

        $condType = $scope->getType($cond);
        if ($condType instanceof \PHPStan\Type\MixedType) {
            return [];
        }

        $leftType = $scope->getType($cond->left);
        $rightType = $scope->getType($cond->right);

        // DEBUG
        //var_dump(get_class($cond));

        if ($cond instanceof \PhpParser\Node\Expr\BinaryOp\NotEqual) {

            // DEBUG
            //var_dump(get_class($leftType));

            if (
                $rightType instanceof \PHPStan\Type\Constant\ConstantStringType
                &&
                $rightType->getValue() === ''
                &&
                $leftType instanceof \PHPStan\Type\StringType
            ) {
                $errors[] = \PHPStan\Rules\RuleErrorBuilder::message(
                    'Please do not use double negative string conditions. e.g. `(string)$foo != \'\'` is the same as `(string)$foo`.'
                )->line($cond->getAttribute('startLine'))->build();
            }

            if (
                (
                    (
                        $rightType instanceof \PHPStan\Type\Constant\ConstantStringType
                        &&
                        $rightType->getValue() === ''
                    )
                    ||
                    (
                        $rightType instanceof \PHPStan\Type\Constant\ConstantIntegerType
                        &&
                        $rightType->getValue() === 0
                    )
                    ||
                    (
                        $rightType instanceof \PHPStan\Type\Constant\ConstantBooleanType
                        &&
                        $rightType->getValue() === false
                    )
                )
                &&
                (
                    $leftType instanceof \PHPStan\Type\IntegerType
                    ||
                    (
                        $leftType instanceof \PHPStan\Type\UnionType
                        &&
                        $leftType->getTypes()[0] instanceof \PHPStan\Type\IntegerType
                        &&
                        $leftType->getTypes()[1] instanceof \PHPStan\Type\NullType
                    )
                )
            ) {
                $errors[] = \PHPStan\Rules\RuleErrorBuilder::message(
                    'Please do not use double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`.'
                )->line($cond->getAttribute('startLine'))->build();
            }

            if (
                (
                    (
                        $rightType instanceof \PHPStan\Type\Constant\ConstantStringType
                        &&
                        $rightType->getValue() === ''
                    )
                    ||
                    (
                        $rightType instanceof \PHPStan\Type\Constant\ConstantIntegerType
                        &&
                        $rightType->getValue() === 0
                    )
                    ||
                    (
                        $rightType instanceof \PHPStan\Type\Constant\ConstantBooleanType
                        &&
                        $rightType->getValue() === false
                    )
                )
                &&
                (
                    $leftType instanceof \PHPStan\Type\BooleanType
                    ||
                    (
                        $leftType instanceof \PHPStan\Type\UnionType
                        &&
                        $leftType->getTypes()[0] instanceof \PHPStan\Type\BooleanType
                        &&
                        $leftType->getTypes()[1] instanceof \PHPStan\Type\NullType
                    )
                )
            ) {
                $errors[] = \PHPStan\Rules\RuleErrorBuilder::message(
                    'Please do not use double negative boolean conditions. e.g. `(bool)$foo != false` is the same as `(bool)$foo`.'
                )->line($cond->getAttribute('startLine'))->build();
            }

            // NULL checks are difficult and maybe unexpected, so that we should use strict check here
            // https://3v4l.org/a4VdC
            if (
                $rightType instanceof \PHPStan\Type\ConstantScalarType
                &&
                $rightType->getValue() === null
                &&
                (
                    (
                        $leftType instanceof \PHPStan\Type\UnionType
                        &&
                        $leftType->getTypes()[0] instanceof \PHPStan\Type\IntegerType
                        &&
                        $leftType->getTypes()[1] instanceof \PHPStan\Type\NullType
                    )
                    ||
                    (
                        $leftType instanceof \PHPStan\Type\UnionType
                        &&
                        $leftType->getTypes()[0] instanceof \PHPStan\Type\StringType
                        &&
                        $leftType->getTypes()[1] instanceof \PHPStan\Type\NullType
                    )
                )
            ) {
                $errors[] = \PHPStan\Rules\RuleErrorBuilder::message(
                    'Please do not use double negative null conditions. Use "!==" instead if needed.'
                )->line($cond->getAttribute('startLine'))->build();
            }

        }

        // -----------------------------------------------------------------------------------------

        foreach ($classesNotInIfConditions as $classesNotInIfCondition) {
            if (
                (
                    $leftType instanceof \PHPStan\Type\ObjectType
                    &&
                    is_a($leftType->getClassName(), $classesNotInIfCondition, true)
                )
                ||
                (
                    $rightType instanceof \PHPStan\Type\ObjectType
                    &&
                    is_a($rightType->getClassName(), $classesNotInIfCondition, true)
                )
            ) {
                $errors[] = \PHPStan\Rules\RuleErrorBuilder::message(
                    'Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.'
                )->line($cond->getAttribute('startLine'))->build();
            }
        }

        // -----------------------------------------------------------------------------------------

        if (
            $cond instanceof \PhpParser\Node\Expr\BinaryOp\NotEqual
            ||
            $cond instanceof \PhpParser\Node\Expr\BinaryOp\NotIdentical
        ) {
            if (
                (
                    $rightType instanceof \PHPStan\Type\Constant\ConstantStringType
                    &&
                    $rightType->getValue() === ''
                    &&
                    $leftType->isNonEmptyString()->yes()
                )
                ||
                (
                    $leftType instanceof \PHPStan\Type\Constant\ConstantStringType
                    &&
                    $leftType->getValue() === ''
                    &&
                    $rightType->isNonEmptyString()->yes()
                )
            ) {
                $errors[] = \PHPStan\Rules\RuleErrorBuilder::message(
                    'Non-empty string is never empty.'
                )->line($cond->getAttribute('startLine'))->build();
            }
        }

        // -----------------------------------------------------------------------------------------

        return $errors;
    }
}
