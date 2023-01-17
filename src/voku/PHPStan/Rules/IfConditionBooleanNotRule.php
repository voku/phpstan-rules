<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;

/**
 * @implements Rule<\PhpParser\Node\Expr\BooleanNot>
 */
final class IfConditionBooleanNotRule implements Rule
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
     * @var bool
     */
    private $checkYodaConditions;

    /**
     * @var null|ReflectionProvider
     */
    private $reflectionProvider;

    /**
     * @param array<int, class-string> $classesNotInIfConditions
     */
    public function __construct(
        array $classesNotInIfConditions,
        ?ReflectionProvider $reflectionProvider = null,
        bool                $checkForAssignments = false,
        bool                $checkYodaConditions = false
    )
    {
        $this->reflectionProvider = $reflectionProvider;

        $this->classesNotInIfConditions = $classesNotInIfConditions;

        $this->checkForAssignments = $checkForAssignments;
        
        $this->checkYodaConditions = $checkYodaConditions;
    }

    public function getNodeType(): string
    {
        return \PhpParser\Node\Expr\BooleanNot::class;
    }

    /**
     * @param \PhpParser\Node\Expr\BooleanNot $node
     *
     * @return array<int, \PHPStan\Rules\RuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        return IfConditionHelper::processBooleanNodeHelper(
            $node->expr,
            $scope,
            $this->classesNotInIfConditions,
            $node,
            $this->reflectionProvider,
            $this->checkForAssignments,
            $this->checkYodaConditions
        );
    }
}
