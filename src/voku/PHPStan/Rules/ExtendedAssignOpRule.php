<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Node\Expr>
 */
class ExtendedAssignOpRule implements Rule
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
        return Node\Expr\AssignOp::class;
    }

    /**
     * @param Node\Expr\AssignOp $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($scope->isInTrait()) {
            return [];
        }

        return $this->diagnostics->processNode($node, $scope);
    }
}
