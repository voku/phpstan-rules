<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Parser\LastConditionVisitor;
use PHPStan\Rules\RuleError;
use PHPStan\Type\Type;

/**
 * Context policy for diagnostics produced by IfConditionHelper.
 *
 * The helper remains responsible for discovering extension-specific findings. This class only
 * decides whether one of those findings is publishable when PHPStan already owns the same constant
 * comparison or deliberately suppresses an always-true final guard.
 */
final class IfConditionDiagnosticPolicy
{
    /**
     * @param array<int, RuleError> $errors
     *
     * @return array<int, RuleError>
     */
    public static function filterBinaryComparison(
        Node\Expr\BinaryOp $condition,
        Scope $scope,
        array $errors,
        bool $reportDuplicateNativeComparisons,
        bool $reportAlwaysTrueInLastCondition,
        bool $treatPhpDocTypesAsCertain
    ): array
    {
        // PHPStan defers trait constant-condition decisions across all using classes. Until VPR-4
        // / #74 has the same contextual boundary, dropping a per-use finding here is unsafe.
        if ($scope->isInTrait()) {
            return $errors;
        }

        $suppressNativeDuplicate = !$reportDuplicateNativeComparisons
            && self::isProvablyReportedByNativeConstantLooseComparison(
                $condition,
                $scope,
                $reportAlwaysTrueInLastCondition,
                $treatPhpDocTypesAsCertain
            );
        $suppressAlwaysTrue = self::isSuppressedAlwaysTrueLastCondition(
            $condition,
            $scope,
            $reportAlwaysTrueInLastCondition,
            $treatPhpDocTypesAsCertain
        );

        if (!$suppressNativeDuplicate && !$suppressAlwaysTrue) {
            return $errors;
        }

        $filtered = [];
        foreach ($errors as $error) {
            $message = $error->getMessage();

            if ($suppressNativeDuplicate && self::isNativeConstantTruthClaim($message)) {
                continue;
            }

            if ($suppressAlwaysTrue && self::isAlwaysTrueClaim($message)) {
                continue;
            }

            $filtered[] = $error;
        }

        return $filtered;
    }

    /**
     * @param array<int, RuleError> $errors
     *
     * @return array<int, RuleError>
     */
    public static function filterTruthiness(
        Node\Expr $condition,
        Scope $scope,
        array $errors,
        bool $reportAlwaysTrueInLastCondition,
        bool $treatPhpDocTypesAsCertain
    ): array
    {
        if (
            $scope->isInTrait()
            ||
            !self::isSuppressedAlwaysTrueLastCondition(
                $condition,
                $scope,
                $reportAlwaysTrueInLastCondition,
                $treatPhpDocTypesAsCertain
            )
        ) {
            return $errors;
        }

        $filtered = [];
        foreach ($errors as $error) {
            if (self::isAlwaysTrueClaim($error->getMessage())) {
                continue;
            }

            $filtered[] = $error;
        }

        return $filtered;
    }

    private static function isProvablyReportedByNativeConstantLooseComparison(
        Node\Expr\BinaryOp $condition,
        Scope $scope,
        bool $reportAlwaysTrueInLastCondition,
        bool $treatPhpDocTypesAsCertain
    ): bool
    {
        if (
            !$condition instanceof Node\Expr\BinaryOp\Equal
            &&
            !$condition instanceof Node\Expr\BinaryOp\NotEqual
        ) {
            return false;
        }

        // Mirror PHPStan's ConstantLooseComparisonRule type choice exactly. If PHPDoc types are not
        // trusted, only native types may prove overlap; otherwise the configured full type is the
        // source of truth.
        $value = self::constantBooleanValue(
            self::configuredType($condition, $scope, $treatPhpDocTypesAsCertain)
        );
        if ($value === null) {
            return false;
        }

        if (
            $value
            &&
            !$reportAlwaysTrueInLastCondition
            &&
            $condition->getAttribute(LastConditionVisitor::ATTRIBUTE_NAME) === true
        ) {
            // PHPStan's ConstantLooseComparisonRule deliberately returns no error here.
            return false;
        }

        return true;
    }

    private static function isSuppressedAlwaysTrueLastCondition(
        Node\Expr $condition,
        Scope $scope,
        bool $reportAlwaysTrueInLastCondition,
        bool $treatPhpDocTypesAsCertain
    ): bool
    {
        if (
            $reportAlwaysTrueInLastCondition
            ||
            $condition->getAttribute(LastConditionVisitor::ATTRIBUTE_NAME) !== true
        ) {
            return false;
        }

        return self::configuredType($condition, $scope, $treatPhpDocTypesAsCertain)
            ->toBoolean()
            ->isTrue()
            ->yes();
    }

    private static function configuredType(
        Node\Expr $condition,
        Scope $scope,
        bool $treatPhpDocTypesAsCertain
    ): Type
    {
        if ($treatPhpDocTypesAsCertain) {
            return $scope->getType($condition);
        }

        return $scope->getNativeType($condition);
    }

    private static function constantBooleanValue(Type $type): ?bool
    {
        if ($type->isTrue()->yes()) {
            return true;
        }

        if ($type->isFalse()->yes()) {
            return false;
        }

        return null;
    }

    private static function isNativeConstantTruthClaim(string $message): bool
    {
        return \strpos($message, 'Condition between ') !== false
            && \strpos($message, ' are falsy, please do not mix types.') !== false;
    }

    private static function isAlwaysTrueClaim(string $message): bool
    {
        if (self::isNativeConstantTruthClaim($message)) {
            return true;
        }

        if (\strpos($message, ' is always true') !== false) {
            return true;
        }

        return $message === 'Non-empty string is never empty.'
            || $message === 'Non-empty array is never empty.';
    }
}
