<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PHPStan\Node\BooleanAndNode>
 */
final class IfConditionBooleanAndRule implements Rule
{

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

    public function getNodeType(): string
    {
        return \PHPStan\Node\BooleanAndNode::class;
    }

    /**
     * @param \PHPStan\Node\BooleanAndNode $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $cond = $node->getOriginalNode();
        $rightScope = $node->getRightScope();

        $errors = IfConditionHelper::processNode($cond->left, $scope, $this->classesNotInIfConditions);
        $errors = array_merge($errors, IfConditionHelper::processNode($cond->right, $rightScope, $this->classesNotInIfConditions));

        return $errors;
    }
}
