[![Build Status](https://github.com/voku/phpstan-rules/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/voku/phpstan-rules/actions)
[![codecov.io](https://codecov.io/github/voku/phpstan-rules/coverage.svg?branch=master)](https://codecov.io/github/voku/phpstan-rules?branch=master)
[![Latest Stable Version](https://poser.pugx.org/voku/phpstan-rules/v/stable)](https://packagist.org/packages/voku/phpstan-rules)
[![License](https://poser.pugx.org/voku/phpstan-rules/license)](https://packagist.org/packages/voku/phpstan-rules)
[![Donate to this project using Paypal](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://www.paypal.me/moelleken)
[![Donate to this project using Patreon](https://img.shields.io/badge/patreon-donate-yellow.svg)](https://www.patreon.com/voku)

# phpstan-rules

Provides additional rules for [`phpstan/phpstan`](https://github.com/phpstan/phpstan).

## Installation

Run

```sh
$ composer require --dev voku/phpstan-rules
```

## Usage

All of the [rules](https://github.com/voku/phpstan-rules#rules) provided (and used) by this library are included in [`rules.neon`](rules.neon).

When you are using [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer), `rules.neon` will be automatically included.

Otherwise, you need to include `rules.neon` in your `phpstan.neon`:

```neon
includes:
	- vendor/voku/PHPStan/Rules/rules.neon
```

## Rules

### IfConditionHelper

This helper is used by different "condition"-rules: if - and - or - not - ternary

:bulb: We use this "hack" (helper) to run the check for all kind of conditions.

- double negative string conditions. e.g. `(string)$foo != \'\'` is the same as `(string)$foo`
  - https://3v4l.org/oDMie
- double negative integer conditions. e.g. `(int)$foo != 0` is the same as `(int)$foo`
  - https://3v4l.org/OWhrc
- double negative boolean conditions. e.g. `(bool)$foo != false` is the same as `(bool)$foo`
  - https://3v4l.org/SHoQP
- double negative null conditions. Use "!==" instead if needed
  - https://3v4l.org/a4VdC
- check non-empty string is never empty
- check non-empty string is always empty

#### Configuration

If you want to configure a list of classes / subclasses that can NOT be used in conditions directly:

e.g.:
- ok: `if ($emailValueObject->isValid())`
- error: `if ($emailValueObject != '')`

```neon
parameters:
    voku:
        classesNotInIfConditions: [
            AbstractValueObject
        ]
```

### DisallowedCallMethodOnNull

This code is copy&pasted from [`phpstan/phpstan-src`] and I used it to prevent `Call to a member function on null` errors while I wasn't already on level 8 where all kind of "NULL" checks are already covered by default.

e.g.
```neon
rules:
    - voku\PHPStan\Rules\DisallowedCallMethodOnNullRule
```

### Support

For support and donations please visit [Github](https://github.com/voku/phpstan-rules/) | [Issues](https://github.com/voku/phpstan-rules/issues) | [PayPal](https://paypal.me/moelleken) | [Patreon](https://www.patreon.com/voku).

For status updates and release announcements please visit [Releases](https://github.com/voku/phpstan-rules/releases) | [Twitter](https://twitter.com/suckup_de) | [Patreon](https://www.patreon.com/voku/posts).

For professional support please contact [me](https://about.me/voku).

### Thanks

- Thanks to [GitHub](https://github.com) (Microsoft) for hosting the code and a good infrastructure including Issues-Managment, etc.
- Thanks to [IntelliJ](https://www.jetbrains.com) as they make the best IDEs for PHP and they gave me an open source license for PhpStorm!
- Thanks to [Travis CI](https://travis-ci.com/) for being the most awesome, easiest continous integration tool out there!
- Thanks to [StyleCI](https://styleci.io/) for the simple but powerfull code style check.
- Thanks to [PHPStan](https://github.com/phpstan/phpstan) && [Psalm](https://github.com/vimeo/psalm) for relly great Static analysis tools and for discover bugs in the code!
