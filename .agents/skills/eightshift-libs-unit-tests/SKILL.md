---
name: eightshift-libs-unit-tests
description: "When writing/running PHPUnit tests in infinum/eightshift-libs: Brain Monkey pitfalls, patchwork internals, phpstan path bug, red baseline comparison"
---

# Eightshift-libs unit testing

Applies to `infinum/eightshift-libs` (Brain Monkey + Patchwork + PHPUnit 12).

## Run tests

```fish
./vendor/bin/phpunit tests/Unit/Helpers/CacheTraitTest.php --no-coverage   # by file path
./vendor/bin/phpunit --list-tests | rg CacheTrait                           # discovery works
```

`--filter ClassName` matches NOTHING in this suite. Use file paths.

## Pitfalls (all verified 2026-10)

1. **Never use `Functions\expect()`** — fails with `Class "Mockery_0" not found` (Patchwork/Mockery collision with suite bootstrap). Use `Functions\when()` + closure write-counters:

```php
$writes = ['delete_transient' => 0];
Functions\when('delete_transient')->alias(function () use (&$writes) { $writes['delete_transient']++; return true; });
// ... assert counts after the call
```

2. **PHP internals need whitelisting**: add to `patchwork.json` `redefinable-internals` (e.g. `md5_file`). Undefined WP functions (`set_transient`, custom) need no whitelist.

3. **`composer test:types` → "No files found to analyse"** when checkout path contains an `eightshift/` segment (`phpstan.neon.dist` excludes `*/eightshift/*`). Workaround:

```fish
grep -v '\*/eightshift/\*' phpstan.neon.dist > phpstan-check.neon
./vendor/bin/phpstan analyze <files> --level 6 -c phpstan-check.neon --no-progress
rm phpstan-check.neon
```

Must be in repo root (relative `vendor/...` includes resolve against config location).

4. **Baseline is red** (test rot: wrappers for removed methods, unmocked `get_option`). Before claiming regressions: `git stash`, run same command, compare error name sets, `git stash pop`.

## Conventions

Test wrapper class at top of test file exposes private trait methods (`XWrapper`); static state reset via `resetCache()` reflection helper.
