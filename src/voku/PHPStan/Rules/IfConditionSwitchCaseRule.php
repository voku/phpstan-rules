<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<Node\Stmt\Switch_>
 */
final class IfConditionSwitchCaseRule implements Rule
{

    /**
     * @var array<int, class-string>
     */
    private $classesNotInIfConditions;

    /**
     * @var bool
     */
    private $checkForAssignments;

    /**
     * @var null|ReflectionProvider
     */
    private $reflectionProvider;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(
        array $classesNotInIfConditions,
        bool $checkForAssignments = false,
        ?ReflectionProvider $reflectionProvider = null
    )
    {
        $this->reflectionProvider = $reflectionProvider;
        
        $this->checkForAssignments = $checkForAssignments;
        
        $this->classesNotInIfConditions = $classesNotInIfConditions;
    }

    public function getNodeType(): string
    {
        return Node\Stmt\Switch_::class;
    }

    /**
     * @param Node\Stmt\Switch_ $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // init
        $errors = [];

        foreach ($node->cases as $case) {
            $errors = IfConditionHelper::processNodeHelper(
                $scope->getType($node->cond),
                $case->cond === null ? null : $scope->getType($case->cond),
                $case->cond === null ? $node->cond : $case->cond,
                $errors,
                $this->classesNotInIfConditions,
                $node,
                $this->reflectionProvider,
                $this->checkForAssignments,
                false
            );
        }
        
        return $errors;
    }
}
