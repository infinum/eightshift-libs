# Eightshift-Libs — Optimization Plan

A consolidated list of proposed changes from the in-depth review, grouped by category, with file references, severity, effort, and confidence. Followed by a phased implementation plan ordered by ROI and dependency.

> Codebase snapshot: 134 PHP files, ~35k LOC, PHP 8.4+, PHP-DI 7, PHPUnit 12. Line coverage ~15% (Exception ~89%, Helpers ~38%, everything else 0%).

---

## Legend

- **Severity / Impact**: `high` | `med` | `low`
- **Effort**: `S` (≤1h) | `M` (1–4h) | `L` (4h+)
- **Confidence**: % that the change is correct and worth doing as described
- **Status**: `[ ]` open / `[x]` done

---

## 1. Performance — hot paths

### 1.1 CSS variable generation (highest-value)

- [ ] **Triple-nested lookup → indexed map** — `src/Helpers/CssVariablesTrait.php:680`
      Inside `foreach $variables × foreach $variableValue`, the inner `foreach $data as $index => $item` is a linear search by `name`+`type`. Build a `["{$name}---{$type}" => $index]` map once.
      _Impact: high · Effort: M · Confidence: 90_

- [ ] **`$output .= …` → `$parts[]` + `implode`** — `CssVariablesTrait.php:31-38, 486-513`
      Repeated string concatenation in long global-variables loops; O(n²) memory copies.
      _Impact: high · Effort: S · Confidence: 85_

- [ ] **3× `str_replace()` → single `strtr($s, $map)`** — `CssVariablesTrait.php:815-829`
      Per attribute/variable pair, three sequential `str_replace` calls. One `strtr` is a single pass.
      _Impact: med · Effort: S · Confidence: 85_

- [ ] **`gettype()` checks → `is_int()/is_float()`** — `CssVariablesTrait.php:686-691`
      Five `gettype($x) === 'integer'` checks per item in the inner loop. `is_*()` are faster and clearer.
      _Impact: low · Effort: S · Confidence: 95_

- [ ] **Memoize breakpoint setup** — `CssVariablesTrait.php:98` (`prepareVariableData`), `~:84` (`getSettingsGlobalVariablesBreakpoints`)
      Both are stable per request but rebuilt every render. Add `static $cache = [];` keyed by manifest hash / breakpoint signature.
      _Impact: med · Effort: S · Confidence: 75_

- [ ] **`esc_attr()` on configured CSS selector ID** — `CssVariablesTrait.php:25-48, 62`
      Config-driven, but escape the value placed in `id='…'` and similar attributes anyway.
      _Impact: low (security/defensive) · Effort: S · Confidence: 75_

### 1.2 HTML attribute building

- [ ] **`$htmlAttrs .= …` → array + `implode`** — `src/Helpers/AttributesTrait.php:339, 343`
      _Impact: med · Effort: S · Confidence: 80_

- [ ] **Loose `==` → strict `===`** — `AttributesTrait.php:338`
      `if ($value == 0 || !empty($value))` is type-coercive; tighten.
      _Impact: low (correctness) · Effort: S · Confidence: 90_

### 1.3 Tailwind debug path

- [ ] **Move regex out of render path** — `src/Helpers/TailwindTrait.php:496`
      `preg_replace('/[^a-zA-Z]+/', '-', $manifest['title'])` runs on every block render when `WP_DEBUG` is on. Precompute the slug at manifest cache build, store on the manifest.
      _Impact: low (only debug) · Effort: S · Confidence: 80_

### 1.4 Cache stampede protection

- [ ] **`flock`-based advisory lock around rebuild** — `src/Helpers/CacheTrait.php` rebuild path (~line 141)
      When the timestamp option changes the transient is deleted; any concurrent request triggers a full rebuild. Wrap the rebuild orchestration with `flock(LOCK_EX)` and re-check validity after acquiring.
      _Impact: med (only under deploy/concurrent load) · Effort: M · Confidence: 70_

### 1.5 Dev-mode autowiring cache

- [ ] **Short-TTL dev cache for service scanning** — `src/Main/Autowiring.php:209` and `src/Main/AbstractMain.php:262-283`
      Production caches the service list; development scans the namespace dir on every page load. Add a filemtime-based dev cache (e.g., key on max mtime of `src/` files), or 30s TTL.
      _Impact: med (DX) · Effort: M · Confidence: 80_

- [ ] **Document compiled-container reuse** — `src/Main/AbstractMain.php:216`
      Verify `buildDiContainer()` is invoked once and the result reused per request in consumer projects; add an example to the README/wiki.
      _Impact: low (docs) · Effort: S · Confidence: 60_

---

## 2. Code quality / modernization (PHP 8.4)

- [ ] **Constructor property promotion** — `src/Main/AbstractMain.php:52-56`, `src/Main/Autowiring.php:35-42`
      _Effort: S · Confidence: 90_

- [ ] **`gettype()` → `is_*()` sweep** — `AbstractMain.php:203`, `Exception/ComponentException.php:31-36`, plus the CssVariablesTrait hits above
      _Effort: S · Confidence: 95_

- [ ] **`array_merge(…)` → spread `[...$a, ...$b]`** — `Autowiring.php:82-85` and several traits
      _Effort: S · Confidence: 80_

- [ ] **Empty closures → arrow functions** — `Autowiring.php:335-339` (`function () { return true; }` → `fn() => true`)
      _Effort: S · Confidence: 95_

- [ ] **First-class callable syntax in hook callbacks** — `*Example.php` and abstracts, e.g. `[$this, 'method']` → `$this->method(...)`
      Better PHPStan inference, no behavioural change.
      _Effort: M (broad sweep) · Confidence: 85_

- [ ] **`readonly` properties** — `AbstractMain::$container` (set once)
      _Effort: S · Confidence: 80_

- [ ] **`JSON_THROW_ON_ERROR`** — `src/Helpers/GeneralTrait.php:254`, `CacheTrait` decode sites
      Replace `json_last_error()` plumbing with `try { json_decode(…, flags: JSON_THROW_ON_ERROR) } catch (JsonException)`.
      _Effort: S · Confidence: 85_

- [ ] **Property hooks / asymmetric visibility (8.4)** — opportunistic; scan for trivial getters wrapping a private field
      _Effort: M · Confidence: 50_

---

## 3. Security

No high/critical findings. All defensive.

- [ ] **`esc_attr()` on CSS selector outputs** — `CssVariablesTrait.php:25-48, 62` (see 1.1)
      _Severity: low · Effort: S · Confidence: 75_

- [ ] **Justify or nonce `$_GET['context']`** — `CssVariablesTrait.php:144, 167`
      Sanitized but unnonced; only flips render context. Either add a code comment justifying it, or restrict to admin contexts.
      _Severity: low · Effort: S · Confidence: 70_

- [ ] **Validate manual IP override** — `src/Geolocation/AbstractGeolocation.php:308-313`
      `getIpAddress()` allows an override that bypasses `FILTER_VALIDATE_IP`. Apply the same validation to the override path.
      _Severity: low · Effort: S · Confidence: 80_

- [ ] **`realpath()` containment for CLI template reads** — `src/Cli/AbstractCli.php:425-426`
      Defensive only; CLI-only attack surface.
      _Severity: low · Effort: S · Confidence: 70_

---

## 4. Architecture

- [ ] **New functionality behind injectable interfaces** — `src/Helpers/Helpers.php` static facade is convenient but blocks DI testing. Don't break BC; add interfaces alongside the static delegate going forward.
      _Effort: L (incremental, per-feature) · Confidence: 70_

- [ ] **Optional service priority** — `src/Services/ServiceInterface.php`
      Add `getPriority(): int { return 10; }` default; consumers can override to control hook ordering declaratively.
      _Effort: M · Confidence: 55_

---

## 5. Tests — biggest gap

Existing tests are good (Brain/Monkey + Mockery + data providers); pattern is established in `tests/Unit/Helpers/`. The task is breadth.

Highest-ROI targets (in order):

- [ ] **`src/Main/Autowiring.php`** — reflection-heavy, silent breakage if wrong
- [ ] **`src/Cache/ManifestCache.php` + `CacheTrait`** — perf-critical, fail-silent prone
- [ ] **`src/Helpers/CssVariablesTrait.php`** — largest untested trait; must precede the refactor in §1.1
- [ ] **`src/Blocks/AbstractBlocks.php`** — manifest discovery + rendering
- [ ] **`src/Rest/Routes/AbstractRoute.php`** — public surface, permission checks

_Confidence: 95 — refactoring §1.1 without §5.3 first is risky._

---

## Implementation plan

Five PRs, sequenced to minimize risk and keep diffs reviewable.

### Phase 1 — Safety net (tests first)

**Goal: lock down behaviour before changing hot paths.**

1. Add unit tests for `CssVariablesTrait` covering global output, block-level output, breakpoint inheritance, and the `name`+`type` lookup currently at `:680`. Pattern: copy `tests/Unit/Helpers/RenderTraitTest`.
2. Add unit tests for `CacheTrait`: read/write, transient invalidation, missing file fallback.
3. Add unit tests for `Autowiring`: simple dependency tree, interface resolution, circular detection.

**Exit criteria:** coverage for these three traits ≥ 70%. `composer test` green.

### Phase 2 — Hot-path performance

**Goal: measurable render-time win on CSS variable generation.**

4. Apply §1.1 fixes in this order, one commit each:
   - Indexed `name`+`type` lookup map
   - `$parts[]` + `implode` for global output
   - `strtr` for variable substitution
   - `is_int/is_float` swap
   - Memoize breakpoint setup
5. Apply §1.2 (AttributesTrait) `implode` + strict comparison.
6. Apply §1.3 (Tailwind debug slug precomputation).

**Exit criteria:** all phase-1 tests still green. Optional: micro-benchmark a representative page (10–20 blocks) before/after.

### Phase 3 — Dev-mode + cache safety

**Goal: better DX locally, no stampede in production.**

7. Add filemtime-based dev cache to `Autowiring` (§1.5).
8. Add `flock` advisory lock around `CacheTrait` rebuild (§1.4).
9. Add `esc_attr()` to CSS selector outputs (§1.1 / §3).

**Exit criteria:** dev page-load no longer rescans `src/`. Concurrent rebuild test (two simultaneous warm-ups) produces a single rebuild.

### Phase 4 — PHP 8.4 modernization sweep

**Goal: one mechanical PR, no behaviour changes.**

10. Apply §2 in a single commit each:
    - Constructor property promotion
    - `gettype` → `is_*`
    - `array_merge` → spread
    - Empty closures → arrow fns
    - `readonly` on `AbstractMain::$container`
    - `JSON_THROW_ON_ERROR` migration
    - First-class callable syntax in hook callbacks (separate PR if diff is large)

**Exit criteria:** PHPStan level 6 still clean. PHPCS clean. All tests green.

### Phase 5 — Defensive cleanups

**Goal: low-priority hardening.**

11. Validate manual IP override in `Geolocation` (§3).
12. `realpath()` containment in CLI template reads (§3).
13. Comment / nonce decision for `$_GET['context']` (§3).

**Exit criteria:** no PHPCS security warnings introduced.

### Optional follow-up — Architectural

14. Service priority API (§4).
15. Begin injectable interface pattern alongside `Helpers` static facade (§4) — apply only to new functionality.

---

## Risks & rollback

- **CssVariables refactor (Phase 2)** is the riskiest change; Phase 1 is the mitigation. If a regression escapes, each Phase 2 step is in its own commit and can be reverted independently.
- **Autowiring dev cache (Phase 3)** could mask added classes during dev. Use filemtime keying — not TTL alone — to avoid surprises.
- **PHP 8.4 sweep (Phase 4)** is mechanical and reviewable; low risk if PHPStan + tests pass.

---

## Out of scope (intentionally)

- Replacing the static `Helpers` facade wholesale — BC break for every consumer.
- Switching to a different DI container — PHP-DI 7 is fine.
- WP-CLI scaffold output changes — separate concern.
- Public API renames.
