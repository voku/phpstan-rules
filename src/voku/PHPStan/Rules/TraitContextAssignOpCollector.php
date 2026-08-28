<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\AssignOp;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\LineRuleError;

/**
 * Collects assignment diagnostics for every using-class context of a trait expression.
 *
 * @implements Collector<AssignOp, array{
 *     string,
 *     string,
 *     string,
 *     string,
 *     array<int, array{string, null|string, int}>
 * }>
 */
final class TraitContextAssignOpCollector implements Collector
{
    /**
     * @var ExtendedAssignOpRuleDiagnostics
     */
    private $diagnostics;

    public function __construct(
        ReflectionProvider $reflectionProvider,
        bool $checkForAssignments = false,
        bool $checkYodaConditions = false
    ) {
        $this->diagnostics = new ExtendedAssignOpRuleDiagnostics(
            $reflectionProvider,
            $checkForAssignments,
            $checkYodaConditions
        );
    }

    public function getNodeType(): string
    {
        return AssignOp::class;
    }

    /**
     * @param AssignOp $node
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

        $serializedErrors = [];
        foreach ($this->diagnostics->processNode($node, $scope) as $error) {
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

    private static function expressionKey(AssignOp $node): string
    {
        return \implode(':', [
            \get_class($node),
            (string) $node->getStartLine(),
            (string) $node->getAttribute('startFilePos', -1),
            (string) $node->getAttribute('endFilePos', -1),
        ]);
    }
}
