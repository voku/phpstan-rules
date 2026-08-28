<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\LineRuleError;

/**
 * Collects binary comparison diagnostics for every using-class context of a trait expression.
 *
 * @implements Collector<BinaryOp, array{
 *     string,
 *     string,
 *     string,
 *     string,
 *     array<int, array{string, null|string, int}>
 * }>
 */
final class TraitContextComparisonCollector implements Collector
{
    /**
     * @var IfConditionRuleDiagnostics
     */
    private $ifConditionDiagnostics;

    /**
     * @var ExtendedBinaryOpRuleDiagnostics
     */
    private $extendedBinaryOpDiagnostics;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(
        array $classesNotInIfConditions = [],
        ?ReflectionProvider $reflectionProvider = null,
        bool $checkForAssignments = false,
        bool $checkYodaConditions = false,
        bool $reportDuplicateNativeComparisons = true,
        bool $reportAlwaysTrueInLastCondition = true,
        bool $treatPhpDocTypesAsCertain = true
    ) {
        $this->ifConditionDiagnostics = new IfConditionRuleDiagnostics(
            $classesNotInIfConditions,
            $reflectionProvider,
            $checkForAssignments,
            $checkYodaConditions,
            $reportDuplicateNativeComparisons,
            $reportAlwaysTrueInLastCondition,
            $treatPhpDocTypesAsCertain
        );
        $this->extendedBinaryOpDiagnostics = new ExtendedBinaryOpRuleDiagnostics();
    }

    public function getNodeType(): string
    {
        return BinaryOp::class;
    }

    /**
     * @param BinaryOp $node
     *
     * @return array{
     *     string,
     *     string,
     *     string,
     *     string,
     *     array<int, array{string, null|string, int}>
     * }|null
     */
    public function processNode(Node $node, Scope $scope)
    {
        if (!$scope->isInTrait()) {
            return null;
        }

        $traitReflection = $scope->getTraitReflection();
        $traitFile = $traitReflection->getFileName();
        if ($traitFile === null) {
            return null;
        }

        $classReflection = $scope->getClassReflection();
        $contextName = $classReflection !== null
            ? $classReflection->getName()
            : $scope->getFileDescription();

        $errors = \array_merge(
            $this->ifConditionDiagnostics->processNode($node, $scope),
            $this->extendedBinaryOpDiagnostics->processNode($node, $scope)
        );

        $serializedErrors = [];
        foreach ($errors as $error) {
            $serializedErrors[] = [
                $error->getMessage(),
                $error instanceof IdentifierRuleError ? $error->getIdentifier() : null,
                $error instanceof LineRuleError ? $error->getLine() : $node->getStartLine(),
            ];
        }

        return [
            $traitReflection->getName(),
            $contextName,
            self::expressionKey($node),
            $traitFile,
            $serializedErrors,
        ];
    }

    private static function expressionKey(BinaryOp $node): string
    {
        return \implode(':', [
            \get_class($node),
            (string) $node->getStartLine(),
            (string) $node->getAttribute('startFilePos', -1),
            (string) $node->getAttribute('endFilePos', -1),
        ]);
    }
}
