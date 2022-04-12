<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PhpParser\Node\Stmt>
 */
final class IfConditionRule implements Rule
{

    /**
     * @var array<int, class-string>
     */
    private $classesNotInIfConditions;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(array $classesNotInIfConditions = [])
    {
        $this->classesNotInIfConditions = $classesNotInIfConditions;
    }

    public function getNodeType(): string
    {
        return \PhpParser\Node\Stmt::class;
    }

    /**
     * @param \PhpParser\Node\Stmt $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!($node instanceof \PhpParser\Node\Stmt\If_) && !($node instanceof \PhpParser\Node\Stmt\ElseIf_)) {
            return [];
        }

        $cond = $node->cond;

        return IfConditionHelper::processNode($cond, $scope, $this->classesNotInIfConditions);
    }
}
