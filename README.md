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

### `IfConditionHelper.php`

This helper is used by different "condition"-rules: if - and - or - not - ternary

:bulb: We use this "hack" (helper) to run the check for all kind of conditions.

#### Configuring container interfaces

If you want to configure a list of classes / subclasses that can NOT be used in conditions directly:

e.g.:
- ok: `if ($valueObject->isValid())`
- error: `if ($valueObject != '')`

```neon
parameters:
	voku:
		classesNotInIfConditions: [
			Other\EmailValueObject
		]
```

### Support

For support and donations please visit [Github](https://github.com/voku/anti-xss/) | [Issues](https://github.com/voku/anti-xss/issues) | [PayPal](https://paypal.me/moelleken) | [Patreon](https://www.patreon.com/voku).

For status updates and release announcements please visit [Releases](https://github.com/voku/anti-xss/releases) | [Twitter](https://twitter.com/suckup_de) | [Patreon](https://www.patreon.com/voku/posts).

For professional support please contact [me](https://about.me/voku).

### Thanks

- Thanks to [GitHub](https://github.com) (Microsoft) for hosting the code and a good infrastructure including Issues-Managment, etc.
- Thanks to [IntelliJ](https://www.jetbrains.com) as they make the best IDEs for PHP and they gave me an open source license for PhpStorm!
- Thanks to [Travis CI](https://travis-ci.com/) for being the most awesome, easiest continous integration tool out there!
- Thanks to [StyleCI](https://styleci.io/) for the simple but powerfull code style check.
- Thanks to [PHPStan](https://github.com/phpstan/phpstan) && [Psalm](https://github.com/vimeo/psalm) for relly great Static analysis tools and for discover bugs in the code!
