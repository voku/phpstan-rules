<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules;

use Exception;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\FoundTypeResult;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ErrorType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NeverType;
use PHPStan\Type\NullType;
use PHPStan\Type\StaticType;
use PHPStan\Type\TypeUtils;
use function count;

/**
 * copy&past from
 * https://github.com/phpstan/phpstan-src/blob/master/src/Rules/Methods/CallToMethodStamentWithoutSideEffectsRule.php
 *
 * @implements Rule<\PhpParser\Node\Expr\MethodCall>
 */
final class DisallowedCallMethodOnNullRule implements Rule
{

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param \PhpParser\Node|MethodCall $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        assert($node instanceof MethodCall);

        if (!$node->name instanceof Node\Identifier) {
            return [];
        }

        $typeResult = $this->findTypeToCheck($scope, $node->var);

        $calledOnType = $typeResult->getType();

        $methodName = $node->name->toString();
        if (!$methodName) {
            return [];
        }

        try {
            $method = $calledOnType->getMethod($methodName, $scope);
        } catch (Exception $exception) {
            return [];
        }
        $methodName = $method->getName();
        $className = $method->getDeclaringClass()->getDisplayName();

        if ($calledOnType->accepts((new NullType()), true)->yes()) {
            return [RuleErrorBuilder::message(sprintf('Call to %s %s::%s() on NULL.', $method->isStatic() ? 'static method' : 'method', $className, $methodName))->build(),];
        }

        return [];
    }

    public function findTypeToCheck(\PHPStan\Analyser\Scope $scope, Expr $var): FoundTypeResult
    {

        $type = $scope->getType($var);

        if ($type instanceof MixedType || $type instanceof NeverType) {
            /* @phpstan-ignore-next-line - not covered by backward compatibility promise */
            return new FoundTypeResult(new ErrorType(), [], [], null);
        }

        if ($type instanceof StaticType) {
            $type = $type->getStaticObjectType();
        }
        $errors = [];
        $directClassNames = TypeUtils::getDirectClassNames($type);
        $hasClassExistsClass = false;
        foreach ($directClassNames as $referencedClass) {
            if ($this->reflectionProvider->hasClass($referencedClass)) {
                $classReflection = $this->reflectionProvider->getClass($referencedClass);
                if (!$classReflection->isTrait()) {
                    continue;
                }
            }
            if ($scope->isInClassExists($referencedClass)) {
                $hasClassExistsClass = true;

                continue;
            }

            $errors[] = RuleErrorBuilder::message($referencedClass)->line($var->getLine())->discoveringSymbolsTip()->build();
        }

        if ($hasClassExistsClass || count($errors) > 0) {
            /* @phpstan-ignore-next-line - not covered by backward compatibility promise */
            return new FoundTypeResult(new ErrorType(), [], $errors, null);
        }

        /* @phpstan-ignore-next-line - not covered by backward compatibility promise */
        return new FoundTypeResult($type, $directClassNames, [], null);
    }

}
