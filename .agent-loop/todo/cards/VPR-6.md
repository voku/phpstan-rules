# VPR-6: New rule candidate: loose in_array()/array_search() without $strict

- **Ticket:** VPR-6
- **Lane:** VERIFY
- **Status:** done
- **Created:** 2026-08-27T00:34:41+00:00
- **Updated:** 2026-08-27T10:33:00+00:00
- **Summary:** Implemented and merged through PR #73 as an opt-in rule only. It reports the PHP 7 -> PHP 8 loose-comparison hazard for global `in_array()` / `array_search()` when a definite int/float is compared with a definite non-numeric string through a non-constant typed haystack. Constant haystacks, numeric strings, mixed/union types, unknown strict flags, namespaced unqualified calls and strict mode remain silent. Named arguments are resolved by parameter name on PHP 8+.
- **Next:** Keep the rule opt-in; broaden only with new positive and false-positive evidence.
- **Validation:** PR #73 exact-head PHP 7.4-8.4 matrix and CodeRabbit are green.
- **Format version:** 1

## Decision evidence

- PHPStan's own finite-haystack rules target constant/finite haystacks; this rule explicitly declines constant arrays rather than duplicating that surface.
- The positive fixture covers `in_array()`, `array_search()`, omitted strict mode, explicit `false`, int/string in both directions and float/string.
- The false-positive fixture covers strict mode, same scalar families, numeric strings in both directions, unknown strict mode, mixed and union operands, constant haystacks and namespace-shadowable calls.
- PHP-8-only coverage proves named arguments are resolved semantically rather than by raw argument order, while the general fixture remains parseable on PHP 7.4.
- Every emitted diagnostic is pinned to identifier `voku.FuncCall`.
