<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;

final class IfConditionHelper
{

    /**
     * @param \PhpParser\Node\Expr $cond
     * @param array<int, class-string> $classesNotInIfConditions
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public static function processNode(Node $cond, Scope $scope, $classesNotInIfConditions): array
    {
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

        // left <-> right
        $errors = self::extracted($leftType, $rightType, $cond, $errors, $classesNotInIfConditions);
        // right <-> left
        $errors = self::extracted($rightType, $leftType, $cond, $errors, $classesNotInIfConditions);
        
        return $errors;
    }

    /**
     * @param \PhpParser\Node\Expr $cond
     * @param array<int, \PHPStan\Rules\RuleError> $errors
     * @param array<int, class-string> $classesNotInIfConditions
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    private static function extracted(
        \PHPStan\Type\Type $type_1,
        \PHPStan\Type\Type $type_2,
        Node $cond, 
        array $errors, 
        array $classesNotInIfConditions
    ): array
    {

        // DEBUG
        //var_dump(get_class($type_1), get_class($cond), get_class($type_2));

        if ($cond instanceof \PhpParser\Node\Expr\BinaryOp\NotEqual) { 

            // DEBUG
            //var_dump(get_class($type_2));

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
                        $type_2 instanceof \PHPStan\Type\UnionType && $type_2->getTypes()[0] instanceof \PHPStan\Type\IntegerType
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
                        $type_2 instanceof \PHPStan\Type\UnionType && $type_2->getTypes()[0] instanceof \PHPStan\Type\BooleanType 
                        && $type_2->getTypes()[1] instanceof \PHPStan\Type\NullType
                    )
                )
            ) {
                $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Please do not use double negative boolean conditions. e.g. `(bool)$foo != false` is the same as `(bool)$foo`.')->line($cond->getAttribute('startLine'))->build();
            }

            // NULL checks are difficult and maybe unexpected, so that we should use strict check here
            // https://3v4l.org/a4VdC
            if (
                $type_1 instanceof \PHPStan\Type\ConstantScalarType && $type_1->getValue() === null 
                &&
                (
                    (
                        $type_2 instanceof \PHPStan\Type\UnionType && $type_2->getTypes()[0] instanceof \PHPStan\Type\IntegerType 
                        &&
                        $type_2->getTypes()[1] instanceof \PHPStan\Type\NullType
                    )
                    ||
                    (
                        $type_2 instanceof \PHPStan\Type\UnionType && $type_2->getTypes()[0] instanceof \PHPStan\Type\StringType 
                        &&
                        $type_2->getTypes()[1] instanceof \PHPStan\Type\NullType
                    )
                )
            ) {
                $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Please do not use double negative null conditions. Use "!==" instead if needed.')->line($cond->getAttribute('startLine'))->build();
            }

        }

        // -----------------------------------------------------------------------------------------

        foreach ($classesNotInIfConditions as $classesNotInIfCondition) {
            if (
                $type_1 instanceof \PHPStan\Type\ObjectType 
                &&
                is_a($type_1->getClassName(), $classesNotInIfCondition, true)
            ) {
                $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Use a method to check the condition e.g. `$foo->value()` instead of `$foo`.')->line($cond->getAttribute('startLine'))->build();
            }
        }

        // -----------------------------------------------------------------------------------------

        if (
            $type_1 instanceof \PHPStan\Type\BooleanType 
            &&
            $type_2 instanceof \PHPStan\Type\Constant\ConstantIntegerType
        ) {
            $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Do not compare boolean and integer')->line($cond->getAttribute('startLine'))->build();
        }

        if (
            $type_1 instanceof \PHPStan\Type\BooleanType 
            &&
            $type_2 instanceof \PHPStan\Type\Constant\ConstantStringType
            
        ) {
            $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Do not compare boolean and string')->line($cond->getAttribute('startLine'))->build();
        }

        // -----------------------------------------------------------------------------------------

        if (
            $type_1 instanceof \PHPStan\Type\ObjectType 
            &&
            $type_2 instanceof \PHPStan\Type\Type
            &&
            !$type_2 instanceof \PHPStan\Type\ObjectType
        ) {
            $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Do not compare objects directly.')->line($cond->getAttribute('startLine'))->build();
        }

        // -----------------------------------------------------------------------------------------

        if (
            $cond instanceof \PhpParser\Node\Expr\BinaryOp\NotEqual 
            ||
            $cond instanceof \PhpParser\Node\Expr\BinaryOp\NotIdentical 
        ) {
            if (
                $type_1 instanceof \PHPStan\Type\Constant\ConstantStringType 
                &&
                $type_1->getValue() === ''
                &&
                $type_2->isNonEmptyString()->yes()
                
            ) {
                $errors[] = \PHPStan\Rules\RuleErrorBuilder::message('Non-empty string is never empty.')->line($cond->getAttribute('startLine'))->build();
            }
        }

        // -----------------------------------------------------------------------------------------

        return $errors;
    }
}
