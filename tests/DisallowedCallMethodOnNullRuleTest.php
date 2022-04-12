<?php

declare(strict_types=1);

namespace voku\PHPStan\Rules\Test;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use voku\PHPStan\Rules\DisallowedCallMethodOnNullRule;

/**
 * @extends RuleTestCase<DisallowedCallMethodOnNullRule>
 */
final class DisallowedCallMethodOnNullRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        $reflectionProvider = $this->createReflectionProvider();
        
        return new DisallowedCallMethodOnNullRule($reflectionProvider);
    }

    public function testDisallowedCallMethodOnNull(): void
    {
        $this->analyse(
            [
                __DIR__ . '/fixtures/DisallowedCallMethodOnNullFixtures.php',
            ],
            [
                [
                    'Call to method DOMDocument::createDocumentFragment() on NULL.',
                    6,
                ],
            ]
        );
    }
}
