# VPR-3: Decide how much of PHPStan 2.x native comparison reporting to duplicate

- **Ticket:** VPR-3
- **Lane:** VERIFY
- **Status:** done
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T10:54:00+00:00
- **Summary:** Implemented as a backward-compatible output policy. `voku.reportDuplicateNativeComparisons` defaults to `true`; when disabled, only the generic constant loose-comparison truth claim is removed, and only when PHPStan would report the same condition under the same PHP-version semantics, `treatPhpDocTypesAsCertain`, and `reportAlwaysTrueInLastCondition` configuration. Extension-only PHP 7/8 migration advice, double-negative advice and possible-insane-comparison diagnostics remain published. Trait contexts fail open until #74 owns contextual deferral.
- **Next:** No VPR-3 follow-up remains; trait-context behavior is separately owned by #74.
- **Validation:** PR #75 run #321 is green across PHP 7.4-8.4; PHPStan is green on PHP 7.4. Message-level regression coverage also pins PHP 7 fail-open semantics.
- **Format version:** 1

## Decision evidence

- Duplicate suppression is opt-in; existing consumers keep current output by default.
- The policy mirrors PHPStan's `treatPhpDocTypesAsCertain` type choice instead of inferring overlap from this extension's PHPDoc-aware type alone.
- A last always-true condition is not classified as a native duplicate when PHPStan itself suppresses it.
- PHP 7 comparison semantics are tested separately and deliberately fail open where PHPStan does not prove the same constant result.
- Tests assert individual messages on the same line, so removing an extension-only diagnostic cannot hide behind a correct set of line numbers.
- PHPDoc-only overlap is tested in both certainty modes.
