<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PhpParser\Node\Expr\BooleanNot>
 */
final class IfConditionBooleanNotRule implements Rule {

    /**
     * @var array<int, class-string>
     */
    private $classesNotInIfConditions;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(array $classesNotInIfConditions)
    {
        $this->classesNotInIfConditions = $classesNotInIfConditions;
    }

    public function getNodeType(): string {
        return \PhpParser\Node\Expr\BooleanNot::class;
    }

    /**
     * @param \PhpParser\Node\Expr\BooleanNot $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array {
        $cond = $node->expr;

        return IfConditionHelper::processNode($cond, $scope, $this->classesNotInIfConditions);
    }
}
