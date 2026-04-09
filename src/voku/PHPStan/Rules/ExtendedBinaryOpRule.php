<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ErrorType;
use PHPStan\Type\VerbosityLevel;
use function sprintf;

/**
 * @implements Rule<Node\Expr>
 */
class ExtendedBinaryOpRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Expr\BinaryOp::class;
    }

    /**
     * @param Node\Expr\BinaryOp $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // init
        $errors = [];

        $leftType = $scope->getType($node->left);
        $rightType = $scope->getType($node->right);

        // DEBUG
        //var_dump($leftType, $rightType);

        $errorsFound = false;
        $this->checkErrors($node, $leftType, $rightType, $errors, $errorsFound);
        if ($errorsFound === false) {
            $this->checkErrors($node, $rightType, $leftType, $errors, $errorsFound);
        }

        return $errors;
    }

    /**
     * @param Node\Expr\BinaryOp $node
     * @param array<int, \PHPStan\Rules\RuleError> $errors
     */
    private function checkErrors(
        Node\Expr          $node,
        \PHPStan\Type\Type $type_1,
        \PHPStan\Type\Type $type_2,
        array              &$errors,
        bool               &$errorsFound
    ): void
    {
        // if contains are checked separately
        if (
            $node instanceof \PhpParser\Node\Expr\BinaryOp\Identical
            ||
            $node instanceof \PhpParser\Node\Expr\BinaryOp\NotIdentical
            ||
            $node instanceof \PhpParser\Node\Expr\BinaryOp\Coalesce
            ||
            $node instanceof \PhpParser\Node\Expr\BinaryOp\BooleanAnd
            ||
            $node instanceof \PhpParser\Node\Expr\BinaryOp\BooleanOr
        ) {
            return;
        }

        if (
            $type_1->isString()->yes()
            &&
            !($type_2 instanceof \PHPStan\Type\MixedType)
            &&
            !IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($type_2, \PHPStan\Type\StringType::class)
            &&
            $type_2->describe(VerbosityLevel::typeOnly()) !== 'string' // INFO: hack for non-empty-string
        ) {
            // INFO:
            // string concatenation is allowed only with string compatible types
            // == and != are allowed only with string compatible types
            if (
                (
                    $node instanceof Node\Expr\BinaryOp\Concat
                    ||
                    $node instanceof Node\Expr\BinaryOp\Equal
                    ||
                    $node instanceof Node\Expr\BinaryOp\NotEqual
                )
                &&
                !IfConditionHelper::hasConstantStringValue($type_1, '')
                &&
                !($type_2->toString() instanceof ErrorType)
            ) {
                return;
            }

            $errors[] = IfConditionHelper::buildErrorMessage(
                $node,
                sprintf(
                    'string (%s) in combination with non-string (%s) is not allowed.',
                    $type_1->describe(VerbosityLevel::value()),
                    $type_2->describe(VerbosityLevel::value())
                ),
                $node->getStartLine()
            );

            $errorsFound = true;
            
            return;
        }

        if (
            $type_1->isArray()->yes()
            &&
            !($type_2 instanceof \PHPStan\Type\MixedType)
            &&
            !IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($type_2, \PHPStan\Type\ArrayType::class)
            &&
            !IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($type_2, \PHPStan\Type\Constant\ConstantArrayType::class, false)
            &&
            !IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($type_2, \PHPStan\Type\Accessory\NonEmptyArrayType::class, false)
            &&
            \strpos($type_2->describe(VerbosityLevel::typeOnly()), 'non-empty-array') !== false // INFO: hack for non-empty-array
        ) {
            $errors[] = IfConditionHelper::buildErrorMessage(
                $node,
                sprintf(
                    'array (%s) in combination with non-array (%s) is not allowed.',
                    $type_1->describe(VerbosityLevel::value()),
                    $type_2->describe(VerbosityLevel::value())
                ),
                $node->getStartLine()
            );

            $errorsFound = true;
        }
    }
}
