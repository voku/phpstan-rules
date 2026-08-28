<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Node\Expr>
 */
class ExtendedBinaryOpRule implements Rule
{
    /**
     * @var ExtendedBinaryOpRuleDiagnostics|null
     */
    private $diagnostics;

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
        if ($scope->isInTrait()) {
            return [];
        }

        return $this->getDiagnostics()->processNode($node, $scope);
    }

    private function getDiagnostics(): ExtendedBinaryOpRuleDiagnostics
    {
        if ($this->diagnostics === null) {
            $this->diagnostics = new ExtendedBinaryOpRuleDiagnostics();
        }

        return $this->diagnostics;
    }
}
