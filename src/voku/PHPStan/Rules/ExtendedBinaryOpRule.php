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
     * @var ExtendedBinaryOpRuleDiagnostics
     */
    private $diagnostics;

    public function __construct()
    {
        $this->diagnostics = new ExtendedBinaryOpRuleDiagnostics();
    }

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

        return $this->diagnostics->processNode($node, $scope);
    }
}
