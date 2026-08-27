# VPR-6: New rule candidate: loose in_array()/array_search() without $strict

- **Ticket:** VPR-6
- **Lane:** VERIFY
- **Status:** verify
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T09:31:00+00:00
- **Summary:** Implemented as an opt-in rule only. It reports the PHP 7 -> PHP 8 loose-comparison hazard for global `in_array()` / `array_search()` when a definite int/float is compared with a definite non-numeric string through a non-constant typed haystack. Constant haystacks, numeric-string, mixed/union types, unknown strict flags, namespaced unqualified calls and strict mode remain silent. Named arguments are resolved by parameter name on PHP 8+.
- **Next:** Keep the rule out of `rules.neon`; merge only after the PHP 7.4-8.4 matrix proves the positive, false-positive and named-argument fixtures.
- **Validation:** php vendor/bin/phpunit -c phpunit.xml --filter InArrayLooseComparisonRuleTest
- **Format version:** 1

## Decision evidence

- PHPStan's own finite-haystack rules target constant/finite haystacks; this rule explicitly declines constant arrays rather than duplicating that surface.
- The positive fixture uses parameter-typed non-constant haystacks and covers `in_array()`, `array_search()`, omitted strict mode, explicit `false`, int/string in both directions and float/string.
- The false-positive fixture covers strict mode, same scalar families, numeric-string in both directions, unknown strict mode, mixed and union operands, constant haystacks and namespace-shadowable calls.
- PHP-8-only coverage proves named arguments are resolved semantically rather than by raw argument order, while the general fixture remains parseable on PHP 7.4.
- Every emitted diagnostic is pinned to identifier `voku.FuncCall`.
