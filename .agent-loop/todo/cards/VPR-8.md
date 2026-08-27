# VPR-8: checkYodaConditions is silently ignored by three rules

- **Ticket:** VPR-8
- **Lane:** VERIFY
- **Status:** done
- **Created:** 2026-08-27T00:35:08+00:00
- **Updated:** 2026-08-27T10:33:00+00:00
- **Summary:** Implemented and merged through PR #71. `checkYodaConditions` now reaches ternary, match and switch rules. Match/Switch keep their historical first three positional constructor arguments and append the new flag instead of reordering public parameters. `rules.neon` uses named arguments, and regression tests cover both flag states plus the legacy constructor positions.
- **Next:** No follow-up required unless a new condition frontdoor bypasses the shared configuration contract.
- **Validation:** PR #71 exact-head PHP 7.4-8.4 matrix is green, including PHPStan on PHP 7.4.
- **Format version:** 1

## Decision evidence

- Configuration propagation is tested on all three previously missing rules.
- Direct positional construction remains backward-compatible for Match/Switch.
- `RulesNeonRegistrationTest` compiles the real service wiring.
