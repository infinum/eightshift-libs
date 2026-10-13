---
name: eightshift-libs-local-checks
description: "Run tests and static checks in eightshift-libs locally: Brain Monkey expect() Mockery_0 collision, patchwork internals whitelist, PHPUnit filter quirks, phpstan path-exclude failure and workarounds"
---

# Eightshift Libs — Local Test & Check Gotchas

For `infinum/eightshift-libs` (Brain Monkey + Patchwork suite) checked out under `~/Sites/eightshift/` on this machine.

## PHPUnit / Brain Monkey

- Do NOT use `Functions\expect()` in this suite (seen in `tests/Unit/Helpers/CacheTraitTest.php`): the first `expect()` call in the process dies with `Error: Class "Mockery_0" not found` (collides with a Mockery class generated at bootstrap). Use the file's convention instead: `Functions\when('fn')->alias(function () use (&$calls) { $calls[] = ...; })` with call-counter flags, assert counts/args after the call. `->never()` assertions become `assertSame([], $calls)`.
- Mocking a PHP internal (e.g. `md5_file`, `filemtime`) requires listing it in `patchwork.json` under `redefinable-internals`. Symptom: `Patchwork\Exceptions\NotUserDefined: Please include {"redefinable-internals": ["md5_file"]} in your patchwork.json`.
- Running tests: use the file path (`./vendor/bin/phpunit tests/Unit/Helpers/CacheTraitTest.php --no-coverage`). Class-substring `--filter CacheTraitTest` finds nothing; `--filter testMethodName` works. `composer test:unit -- --filter X` runs nothing.
- Baseline suite rot (2026-10, HEAD 77795832): 13 erroring tests in `CacheTraitTest` (wrappers call removed `fileExistsCached`/`getFileContentsCached`/`jsonDecodeCached`; some `setAllCache` tests never mock `get_option`). When adding tests, compare the error test-name set against stashed HEAD instead of chasing zero errors:

```bash
git stash push -- src tests patchwork.json
./vendor/bin/phpunit <file> --no-coverage 2>&1 | rg '^\d+\)' | rg -o '::\w+' | sort -u > /tmp/base.txt
git stash pop
# same for current, then diff
```

## PHPStan

- `composer test:types` fails at baseline with "No files found to analyse": the `*/eightshift/*` excludePath in `phpstan.neon.dist` matches the checkout path `~/Sites/eightshift/<repo>` itself. CI is unaffected.
- Workaround: generate a temp config in the repo root (must live there — `includes: vendor/...` resolve relative to the config file), analyze, delete:

```bash
grep -v '\*/eightshift/\*' phpstan.neon.dist > phpstan-check.neon
./vendor/bin/phpstan analyze <files> --level 6 -c phpstan-check.neon --no-progress
rm phpstan-check.neon
```

## Baseline commands

- rector: `composer test:rector` (dry-run, OK gate)
- phpcs: `composer test:standards` (clean gate)
