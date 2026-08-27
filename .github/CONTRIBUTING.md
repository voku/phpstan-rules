# How to Contribute

## Pull Requests

1. Create your own [fork](https://help.github.com/articles/fork-a-repo) of this repo
2. Create a new branch for each feature or improvement
3. Send a pull request from each feature branch to the **master** branch

It is very important to separate new features or improvements into separate
feature branches, and to send a pull request for each branch. This allows me to
review and pull in new features or improvements individually.

## Style Guide

All pull requests must adhere to the [PSR-2 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).

## Unit Testing

All pull requests must be accompanied by passing PHPUnit unit tests and
complete code coverage.

[Learn about PHPUnit](https://github.com/sebastianbergmann/phpunit/)

### What a good test looks like here

A rule test that only asserts "this fixture reports these messages" is worth
less than it looks. The failures that actually hurt a PHPStan extension are the
ones the old suite could not see, so a change to a rule should come with the
matching kind of test:

* **No false positives.** `tests/NoErrorsOnValidCodeTest.php` runs every rule
  with the strictest configuration over `tests/fixtures/ValidCodeFixtures.php`
  and expects silence. Code that a rule recommends belongs in that fixture.
* **The identifier, not only the message.** `tests/RuleErrorIdentifierTest.php`
  pins every `voku.*` identifier, because that is what users put into
  `ignoreErrors` and into their baseline.
* **Both states of a flag.** `tests/RuleConfigurationTest.php` asserts that an
  optional check is silent while it is off, not only that it fires when on.
  `rules.neon` ships all of them off.
* **The wiring.** `tests/RulesNeonRegistrationTest.php` builds a real PHPStan
  container from `rules.neon`, so a new rule that is not registered, a renamed
  constructor argument or a changed default fails the suite.
* **One variable per condition in a fixture.** PHPStan narrows a variable
  between two conditions, so reusing `$a` makes a later expectation depend on
  the narrowing of the earlier one instead of on the rule. That is what made
  the suite go red on a PHPStan patch release.

## Workflow tooling

This repository uses [`voku/agent-loop`](https://github.com/voku/agent-loop) for
governed changes and for the TODO board under `.agent-loop/todo`. It requires
PHP 8.3+, so it lives in its own Composer project and stays out of the root
dependency graph - see [`tools/agent-loop/README.md`](../tools/agent-loop/README.md).

```bash
make agent_loop_install
php tools/agent-loop/vendor/bin/agent-loop board summary
php tools/agent-loop/vendor/bin/agent-loop enter VPR-2 --format=json
```

Follow-up work discovered while writing tests is filed as a board card instead
of a `// TODO` comment.