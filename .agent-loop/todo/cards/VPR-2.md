# VPR-2: Fix the unreachable array branch in ExtendedBinaryOpRule and ExtendedAssignOpRule

- **Ticket:** VPR-2
- **Lane:** VERIFY
- **Status:** done
- **Created:** 2026-08-27T00:35:08+00:00
- **Updated:** 2026-08-27T09:40:00+00:00
- **Summary:** Implemented and merged in #71. The dead `typeOnly()` / `non-empty-array` string predicate was replaced by PHPStan's trinary `$type->isArray()->no()`: definite non-array operands are reported, array and non-empty-array operands stay silent, and the changed winner for `array + 'foo'` is pinned by regression coverage.
- **Next:** None; consumed by #71.
- **Validation:** GitHub Actions run #300, PHP 7.4-8.4 green; php vendor/bin/phpunit -c phpunit.xml --filter ExtendedOpRuleArrayCheckTest
- **Format version:** 1

## Decision evidence

- `ExtendedBinaryOpRule` and `ExtendedAssignOpRule` now use type-system semantics rather than string descriptions.
- The repaired branch is covered for comparisons and `+=`, including valid array/non-empty-array combinations.
- PR #71 merged the change after the full supported PHP matrix passed.
