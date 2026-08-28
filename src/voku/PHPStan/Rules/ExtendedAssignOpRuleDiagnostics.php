<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\RuleError;
use PHPStan\Type\ErrorType;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;
use function sprintf;

/**
 * Shared diagnostic calculation for direct and trait-context ExtendedAssignOpRule analysis.
 */
final class ExtendedAssignOpRuleDiagnostics
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
    ) {
        $this->reflectionProvider = $reflectionProvider;
        $this->checkForAssignments = $checkForAssignments;
        $this->checkYodaConditions = $checkYodaConditions;
    }

    /**
     * @return array<int, RuleError>
     */
    public function processNode(Node\Expr\AssignOp $node, Scope $scope): array
    {
        $errors = [];

        $leftType = $scope->getType($node->var);
        $rightType = $scope->getType($node->expr);

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

    /**
     * @param array<int, RuleError> $errors
     */
    private function checkErrors(
        Node\Expr\AssignOp $node,
        Type $type_1,
        Type $type_2,
        array &$errors,
        bool &$errorsFound
    ): void {
        if (
            $type_1->isString()->yes()
            &&
            !($type_2 instanceof \PHPStan\Type\MixedType)
            &&
            !IfConditionHelper::isPhpStanTypeMaybeWithUnionNullable($type_2, \PHPStan\Type\StringType::class)
            &&
            $type_2->describe(VerbosityLevel::typeOnly()) !== 'string'
        ) {
            if (
                $node instanceof Node\Expr\AssignOp\Concat
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
            $type_2->isArray()->no()
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
