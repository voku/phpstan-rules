# Changelog

### 1.9.0 (2022-07-20)
- PHP8: check empty string checks on 0 values: https://3v4l.org/lBFHI

### 1.8.2 (2022-07-18)
- fix for `count($a) === 0` instead of something like `if (!$a)`

### 1.8.1 (2022-07-18)
- use `count($a) === 0` instead of something like `elseif (!$a)`

### 1.8.0 (2022-07-18)
- use `count($a) === 0` instead of something like `if (!$a)`

### 1.7.0 (2022-07-07)
- use `count($a) > 0` instead of something like `if ($a)`
- check non-empty array is never empty

### 1.6.3 (2022-05-11)
- do not compare objects with another type: fix false-positive errors

### 1.6.2 (2022-05-11)
- do not compare objects with another type: allow AND && OR conditions

### 1.6.1 (2022-05-11)
- do not compare objects with another type: allow NULL and BOOLEAN checks

### 1.6.0 (2022-05-11)
- check more conditions (left <-> right) | thanks @Slamdunk
- check DateTime/DateTimeImmutable conditions | thanks @Slamdunk

### 1.5.2 (2022-05-09)
- do not compare objects with another type: allow UnionType with the same ObjectType and NULL

### 1.5.1 (2022-05-09)
- do not compare objects with another type: allow NULL checks

### 1.5.0 (2022-05-09)
- inspect all binary operations regardless within or without if/else statement | thanks @Slamdunk
- do not compare objects with another type: allow $this object type comparison | thanks @Slamdunk
- do not compare objects with another type: allow object strict comparison for ENUM types | thanks @Slamdunk

### 1.4.0 (2022-04-13)
- fix ternary checks
- re-add the "Non-empty string is always non-empty." check
- add tests and ci config

### 1.3.0 (2022-04-11)
- add checks for boolean <> string conditions
- add checks for non-nullable object conditions
- use the same checks for "left <-> right" and "right <-> left" 

### 1.2.1 (2022-04-06)
- add check for boolean <> integer conditions

### 1.2.0 (2022-02-23)
- add check for call method on NULL before level 8

### 1.1.0 (2022-02-17)
- check also for "Non-empty string is always empty."

### 1.0.0 (2022-02-17)
- Initial release with checks for "conditions"
