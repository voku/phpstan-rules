# VPR-7: Re-evaluate the unconditional Coalesce skip

- **Ticket:** VPR-7
- **Lane:** VERIFY
- **Status:** verify
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T09:10:00+00:00
- **Summary:** Measured and rejected as a production change. The Coalesce skip is load-bearing: issue #18 is the historical reproduction showing that a class configured in `classesNotInIfConditions` must remain valid on the left side of `??`. Regression coverage now pins both a nullable object and an optional array-offset case. PHPStan 2.2 already owns null-coalesce diagnostics through its level-1 `NullCoalesceRule`, including the `unnecessaryNullCoalesce` feature path, so adding a second voku rule would duplicate native responsibility.
- **Next:** Keep the existing Coalesce guards. No production change is required unless a future regression can narrow the skip without reintroducing #18.
- **Validation:** php vendor/bin/phpunit -c phpunit.xml --filter Vpr7CoalesceSkipTest
- **Format version:** 1

## Decision evidence

- `voku/phpstan-rules#18` records the concrete false positive that originally required the skip: a configured object used as `$maybeThing ?? new Thing()` was incorrectly treated as an invalid condition.
- `IfConditionHelper` excludes `BinaryOp\\Coalesce` from the object-comparison paths; those guards match the boundary demonstrated by #18.
- PHPStan 2.2 registers `PHPStan\\Rules\\Variables\\NullCoalesceRule` at level 1 and owns both ordinary null-coalesce diagnostics and the `nullCoalesce.unnecessary` feature-toggle diagnostic.
- The new fixture proves that configured nullable objects and optional object-valued array offsets stay silent in this extension.
