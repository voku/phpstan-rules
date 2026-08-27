# VPR-5: Check the rules against PHPStan's reportAlwaysTrueInLastCondition semantics

- **Ticket:** VPR-5
- **Lane:** VERIFY
- **Status:** verify
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T10:33:00+00:00
- **Summary:** Implemented by consuming PHPStan's own `reportAlwaysTrueInLastCondition` and `treatPhpDocTypesAsCertain` parameters. Always-true claims on nodes marked by PHPStan's `LastConditionVisitor` are suppressed only when PHPStan would suppress them under the same type-certainty mode. Always-false diagnostics and extension-only style/advice remain. Match conditions immediately before a default are not treated as last; an actual final match arm without a default is. Switch/default behavior remains reportable because PHPStan's visitor does not mark switch cases as last conditions.
- **Next:** Mark done after exact-head PHPUnit/PHPStan/CI proves both parameter modes and all three control-flow shapes.
- **Validation:** php vendor/bin/phpunit -c phpunit.xml --filter ComparisonDiagnosticPolicyTest
- **Format version:** 1

## Decision evidence

- Final `elseif` is tested with the flag off and on.
- The always-false `!$nonEmptyArray` final guard remains reportable when always-true reporting is disabled.
- Match-before-default and actual-last-match-arm are tested separately.
- Switch-before-default remains reportable, matching `LastConditionVisitor` semantics.
- PHPDoc-only truthiness is tested with `treatPhpDocTypesAsCertain` enabled and disabled.
