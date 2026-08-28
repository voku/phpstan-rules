<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Type\Type;

/**
 * Output policy for binary comparison diagnostics produced by IfConditionHelper.
 *
 * The helper remains responsible for discovering extension-specific findings. This class only
 * removes a generic truth claim when PHPStan owns the same constant loose comparison, or when
 * PHPStan deliberately suppresses an always-true final binary comparison.
 */
final class IfConditionDiagnosticPolicy
{
    /**
     * Mirrored from PHPStan\Parser\LastConditionVisitor::ATTRIBUTE_NAME.
     *
     * Referencing that class constant from src/ is outside PHPStan's backward-compatibility promise,
     * so the value is pinned by a regression test instead.
     */
    private const LAST_CONDITION_ATTRIBUTE = 'isLastCondition';

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

            if ($suppressAlwaysTrue && self::isAlwaysTrueBinaryClaim($message)) {
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

        // Mirror PHPStan's ConstantLooseComparisonRule type choice exactly.
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
            $condition->getAttribute(self::LAST_CONDITION_ATTRIBUTE) === true
        ) {
            // PHPStan's ConstantLooseComparisonRule deliberately returns no error here.
            return false;
        }

        return true;
    }

    private static function isSuppressedAlwaysTrueLastCondition(
        Node\Expr\BinaryOp $condition,
        Scope $scope,
        bool $reportAlwaysTrueInLastCondition,
        bool $treatPhpDocTypesAsCertain
    ): bool
    {
        if (
            $reportAlwaysTrueInLastCondition
            ||
            $condition->getAttribute(self::LAST_CONDITION_ATTRIBUTE) !== true
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

    private static function isAlwaysTrueBinaryClaim(string $message): bool
    {
        return self::isNativeConstantTruthClaim($message)
            || \strpos($message, ' is always true') !== false;
    }
}
