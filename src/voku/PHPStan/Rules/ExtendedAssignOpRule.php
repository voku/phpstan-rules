<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ErrorType;
use PHPStan\Type\VerbosityLevel;
use function sprintf;

/**
 * @implements Rule<Node\Expr>
 */
class ExtendedAssignOpRule implements Rule
{
    /**
     * @var bool
     */
    private $checkForAssignments;

    /**
     * @var bool
     */
    private $checkYodaConditions;
    
    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(
        ReflectionProvider $reflectionProvider,
        bool $checkForAssignments = false,
        bool $checkYodaConditions = false
    )
    {
        $this->reflectionProvider = $reflectionProvider;
        
        $this->checkForAssignments = $checkForAssignments;
        
        $this->checkYodaConditions = $checkYodaConditions;
    }

    public function getNodeType(): string
    {
        return Node\Expr\AssignOp::class;
    }

    /**
     * @param Node\Expr\AssignOp $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // init
        $errors = [];

        if (
            \property_exists($node, 'var')
            &&
            \property_exists($node, 'expr')
        ) {
            $leftType = $scope->getType($node->var);
            $rightType = $scope->getType($node->expr);

            // DEBUG
            //var_dump($leftType, $rightType);

            $errors = IfConditionHelper::processNodeHelper(
                $leftType,
                $rightType,
                $node,
                $errors,
                [],
                $node,
                $this->reflectionProvider,
                $this->checkForAssignments,
                $this->checkYodaConditions
            );
            $errors = IfConditionHelper::processNodeHelper(
                $rightType,
                $leftType,
                $node,
                $errors,
                [],
                $node,
                $this->reflectionProvider,
                false,
                false
            );

            $errorsFound = false;
            $this->checkErrors($node, $leftType, $rightType, $errors, $errorsFound);
            if ($errorsFound === false) {
                $this->checkErrors($node, $rightType, $leftType, $errors, $errorsFound);
            }

            return $errors;
        }

        return $errors;
    }

    /**
     * @param Node\Expr\AssignOp $node
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
        if (
            $type_1 instanceof \PHPStan\Type\StringType
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
                $node instanceof Node\Expr\AssignOp\Concat
                &&
                !(
                    $type_1 instanceof \PHPStan\Type\Constant\ConstantStringType
                    &&
                    $type_1->getValue() === ''
                )
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
            $type_1 instanceof \PHPStan\Type\ArrayType
            &&
            !($type_2 instanceof \PHPStan\Type\MixedType)
            &&
            !IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($type_2, \PHPStan\Type\ArrayType::class)
            &&
            !IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($type_2, \PHPStan\Type\Constant\ConstantArrayType::class, false)
            &&
            !IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($type_2, \PHPStan\Type\Accessory\NonEmptyArrayType::class, false)
            &&
            \strpos($type_2->describe(VerbosityLevel::typeOnly()), 'non-empty-array') !== false
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
