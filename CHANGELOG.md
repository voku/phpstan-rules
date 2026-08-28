# Changelog

### Upcoming
- restore clean self-analysis for the comparison policy, make PHPStan analysis blocking in CI, and propagate duplicate-native suppression through ternary, match, switch and boolean condition rules
- propagate `checkYodaConditions` to ternary, match and switch rules, and fix definite array/non-array checks in binary and assignment operators
- add opt-in `InArrayLooseComparisonRule` for PHP 7/8 loose-comparison surprises in `in_array()` and `array_search()`
- add `voku.reportDuplicateNativeComparisons` to suppress proven PHPStan-native comparison overlap while preserving extension-specific advice and PHPStan last-condition/PHPDoc-certainty behavior
- defer trait-body comparison diagnostics across using-class contexts to avoid per-use duplicates and suppress context-specific false positives

### 3.7.0 (2026-04-28)
- detect disguised constant ternary conditions and dedupe duplicate diagnostics
- allow enum `$this` in `match` expressions without direct-object-comparison errors
- report missed loose constant-array comparisons in `IfConditionRule`

### 3.6.0 (2025-01-05)
- support for phpstan 2.x

### 3.5.0 (2024-08-30)
- check more mix types e.g. integer-range in `==` and `!=` conditions

### 3.4.0 (2024-07-20)
- add check for mix types e.g. integer-range in `==` and `!=` conditions

### 3.3.1 (2024-07-17)
- add check for `switch-case` and `match` conditions v2

### 3.3.0 (2024-07-16)
- add check for `switch-case` and `match` conditions
- changed the error message for "Insane comparison ..."

### 3.2.0 (2023-06-09)
- add check for `array ==(=) bool` conditions

### 3.1.12 (2023-03-21)
- do not report possible insane comparison between e.g. 'NULL' and 'NULL'|int|null

### 3.1.11 (2023-01-18)
- allow "Yoda"-conditions for more cases

### 3.1.10 (2023-01-18)
- allow "Yoda"-conditions for concat

### 3.1.9 (2023-01-18)
- allow "Yoda"-conditions for "Ternary"-conditions

### 3.1.8 (2023-01-18)
- "rules.neon" -> add missing config

### 3.1.7 (2023-01-17)
- "rules.neon" -> fix typo only

### 3.1.6 (2023-01-17)
- add check for "assignments" 
- add check for "Yoda conditions"

### 3.1.5 (2022-11-21)
- fix for PhpStan >= 1.9.x (v2)

### 3.1.4 (2022-11-21)
- fix for PhpStan >= 1.9.x

### 3.1.3 (2022-09-05)
- "Do not compare objects directly" -> but allow compare with interfaces / extends v2

### 3.1.2 (2022-09-05)
- "Do not compare objects directly" -> but allow compare with interfaces

### 3.1.1 (2022-08-31)
- fix for "double negative integer conditions"

### 3.1.0 (2022-08-31)
- check array vs. non-array usage 
- add new error message: "Do not cast objects magically, please use `__toString` here ..."

### 3.0.0 (2022-07-29)
- check more insane comparisons e.g. `false && true`
- add "ExtendedBinaryOpRule": This rule will check "+", "*", "/", "-", ... (operators) and "." (concatenation) for compatible types.

### 2.0.0 (2022-07-22)
- add a prefix for the error messages
- check for more non-typical "if"-conditions

### 1.10.0 (2022-07-21)
- check possible insane comparisons. e.g. `0 == '0foo'`, the behavior was changed in PHP 8, https://3v4l.org/BJ6b8

### 1.9.0 (2022-07-20)
- PHP8: check empty string checks on 0 values: https://3v4l.org/lBFHI

### 1.8.2 (2022-07-18)
- use `count($a) === 0` instead of something like `if (!$a)`

### 1.8.1 (2022-07-18)
- use `count($a) === 0` instead of something like `elseif (!$a)`

### 1.8.0 (2022-07-18)
- use `count($a) === 0` instead of something like `if (!$a)`

### 1.7.0 (2022-07-07)
- use `count($a) > 0` instead of something like `if ($a)`
- check non-empty array is never empty
