# VPR-7: Re-evaluate the unconditional Coalesce skip

- **Ticket:** VPR-7
- **Lane:** VERIFY
- **Status:** done
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T10:33:00+00:00
- **Summary:** Measured and rejected as a production change, then merged through PR #72 as regression evidence. The Coalesce skip is load-bearing: issue #18 is the historical reproduction showing that a class configured in `classesNotInIfConditions` must remain valid on the left side of `??`. Regression coverage pins both a nullable object and an optional array-offset case. PHPStan 2.2 already owns null-coalesce diagnostics through its level-1 `NullCoalesceRule`, including the `unnecessaryNullCoalesce` feature path, so adding a second voku rule would duplicate native responsibility.
- **Next:** Keep the existing Coalesce guards. Revisit only if a future regression can narrow the skip without reintroducing #18.
- **Validation:** PR #72 exact-head PHP 7.4-8.4 matrix and PHPStan job are green.
- **Format version:** 1

## Decision evidence

- `voku/phpstan-rules#18` records the concrete false positive that originally required the skip.
- `IfConditionHelper` excludes `BinaryOp\\Coalesce` from the object-comparison paths; those guards match the boundary demonstrated by #18.
- PHPStan owns ordinary and unnecessary null-coalesce diagnostics, so no parallel extension rule was added.
- The fixture proves configured nullable objects and optional object-valued array offsets stay silent in this extension.
