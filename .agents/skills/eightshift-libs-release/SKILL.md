---
name: eightshift-libs-release
description: "Cutting a release in infinum/eightshift-libs: release branch PR flow, changelog compare links, tagging"
---

# Eightshift-libs release flow

Use for releasing infinum/eightshift-libs. Overrides the generic `release` skill (which direct-pushes and forbids PRs) — this repo requires a release PR.

## Fix branch → release branch

- Fix PRs target `release/X.Y.Z`, never `main`. If the release branch is missing, create it from origin/main: `git push origin origin/main:refs/heads/release/X.Y.Z`.
- Before creating the fix PR: prepend `## [X.Y.Z]` to CHANGELOG.md, bump package.json `version`, ensure GitHub milestone `X.Y.Z` exists (`gh api repos/:owner/:repo/milestones`), confirm bump type + target with the user (create-pull-request eightshift addendum step 7).

## Release PR (release/X.Y.Z → main)

- Title: `Release/X.Y.Z` (pattern: #499 "Release/13.5.0", #502 "Release/13.6.0"). Non-draft, `ai-assisted` label, assign the user.
- Add a docs commit on the release branch, message `docs: bump changelog to X.Y.Z`:
  - CHANGELOG.md bottom link-refs block: add `[X.Y.Z]: https://github.com/infinum/eightshift-libs/compare/<prev>...X.Y.Z` as the first ref. Watch for gaps — `[13.5.0]` ref is missing from the file entirely.
  - README.md: document user-visible behavior changes in the matching section (13.5.0 and 13.6.0 both extended `## Manifest cache` inside the release PR).

## After merge

- Tag without `v` prefix: `13.6.0` (git `v*` tags are the pre-2.5 era; GitHub releases since 13.x are bare).
- Releases are manual — only `ci.yml` exists, no release workflow: `gh release create 13.6.0`.

## Local gotcha

`composer test:types` (phpstan) fails with "No files found to analyse" on checkouts under `~/Sites/eightshift/` — phpstan.neon.dist `*/eightshift/*` excludePath matches the checkout path itself. Validate with a temp config minus that exclude, or rely on CI.
