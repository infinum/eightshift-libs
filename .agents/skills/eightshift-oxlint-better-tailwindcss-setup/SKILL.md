---
name: eightshift-oxlint-better-tailwindcss-setup
description: "Add eslint-plugin-better-tailwindcss via oxlint to an Eightshift v4 WordPress theme (cz-theme, eightshift-ui-kit, or similar). Use when a project needs Tailwind class canonicalisation in-editor for JS/JSX; covers v4 entry-point resolution, ^es: ignore, ESLint rule conflicts, and the no-auto-fix limitation."
---

# Add oxlint + eslint-plugin-better-tailwindcss to an Eightshift v4 theme

For Eightshift WordPress themes (cz-theme, eightshift-ui-kit, etc.) that want in-editor Tailwind canonical-class rewriting for JS/JSX. Pairs with the existing ESLint config from `@eightshift/frontend-libs-tailwind/linters/eslint.config.mjs`.

## Versions (pin, don't caret)

- `oxlint@1.82.0`
- `eslint-plugin-better-tailwindcss@4.7.0`
- Reference setup: `~/Sites/eightshift/eightshift-ui-components/oxlint.config.ts`

## Install

```
SOCKET=0 bun add -d oxlint@1.82.0 eslint-plugin-better-tailwindcss@4.7.0
```

## Config (`oxlint.config.ts`)

```ts
import { getDefaultSelectors } from 'eslint-plugin-better-tailwindcss/defaults';
import { defineConfig } from 'oxlint';

export default defineConfig({
  plugins: ['eslint', 'unicorn', 'oxc', 'react'],
  jsPlugins: ['eslint-plugin-better-tailwindcss'],
  settings: {
    'better-tailwindcss': {
      entryPoint: 'tailwind.css', // NOT tailwind-frontend.css — see gotcha 1
      selectors: [
        ...getDefaultSelectors(),
        { kind: 'attribute', name: '^.+ClassName$', match: [{ type: 'strings' }] },
      ],
    },
  },
  ignorePatterns: [
    'public/**', 'node_modules/**', 'vendor/**', 'eightshift/**', 'coverage/**',
    '.agent/**', '.agents/**', '.claude/**', '.codex/**', '.cursor/**',
    '.gemini/**', '.opencode/**', '.pi/**', '.roo/**', '.windsurf/**',
  ],
  rules: {
    'no-array-constructor': 'error',
    'no-constant-binary-expression': 'error',
    'no-implicit-coercion': 'error',
    'no-nested-ternary': 'error',
    'no-unused-expressions': 'error',
    // Omit no-unused-vars: ESLint's unused-imports handles it with ^_ pattern — see gotcha 3
    'no-void': 'error',
    'no-param-reassign': 'warn',
    'react/rules-of-hooks': 'error',
    'react/exhaustive-deps': 'error',
    'react/react-in-jsx-scope': 'off',
    'react/self-closing-comp': 'warn',
    'better-tailwindcss/no-duplicate-classes': 'error',
    'better-tailwindcss/no-deprecated-classes': 'error',
    'better-tailwindcss/enforce-canonical-classes': 'error',
    'better-tailwindcss/no-unknown-classes': [
      'error',
      {
        // `es:` is the @eightshift/ui-components library prefix;
        // those classes are defined in library CSS, not in this theme's design system.
        ignore: ['^es:'],
      },
    ],
    'better-tailwindcss/no-conflicting-classes': 'error',
  },
});
```

## package.json scripts

```json
{
  "lintJs": "eslint src/",
  "lintJs:oxlint": "oxlint",
  "lintJs:fix": "oxlint --fix",
  "test": "node --test tests/*.test.mjs && bun run lintStyle && bun run lintJs && bun run lintJs:oxlint",
  "lint-staged": {
    "*.js": "bun lintJs && bun lintJs:oxlint",
    "*.css": "bun lintStyle",
    "*.php": "sh -c 'composer test'"
  }
}
```

## Gotchas

### 1. Entry point must be the source CSS, not the wrapper

cz-theme has two CSS entries: `tailwind-frontend.css` (8 lines, just `@source` + `@import './tailwind.css'`) and `tailwind.css` (709 lines, the actual `@import 'tailwindcss' + @theme + @plugin + @source` source). Pointing at the wrapper produces false-positive "Unknown class" on standard Tailwind utilities because the plugin's design-system resolution can't see past the wrapper. Always point at the file that has the real `@import 'tailwindcss'` + `@theme` block.

How to confirm: if `bunx oxlint <file-with-standard-tailwind-classes>.js` flags `text-sm`, `rounded-md`, etc. as unknown, the entry point is wrong.

### 2. The `^es:` ignore pattern is mandatory for Eightshift projects

`es:` is the `@eightshift/ui-components` library's class prefix. Those classes are defined in the library's CSS, not in your theme's Tailwind design system, so `no-unknown-classes` will fire on every `es:` class without the ignore pattern. Other project-defined non-Tailwind classes (e.g. `parent`, `time`, `backdrop`, `shimmer*`) also need adding to the ignore list — but those are real findings, so the team should triage them per project.

### 3. Skip `no-unused-vars` in oxlint

cz-theme's ESLint config (via `@eightshift/frontend-libs-tailwind/linters/base.config.mjs`) uses `unused-imports/no-unused-vars` with `argsIgnorePattern: '^_'` and `varsIgnorePattern: '^_'`. oxlint's `no-unused-vars` doesn't honour those patterns and will flag `_underscore` params as errors. Let ESLint handle unused vars; omit the rule from oxlint.

### 4. `oxlint --fix` does NOT persist `enforce-canonical-classes` rewrites

Detection works perfectly (smoke confirmed all canonical rewrites from the eightshift-ui-components reference). But oxlint 1.82's jsPlugin shim doesn't pass through the auto-fix to disk. Fixes happen via:
- In-editor LSP quickfix (Neovim `tailwindcss` LSP, VSCode Oxc plugin)
- `tw-sweep` (project-wide batch tool in `eightshift-frontend-libs-tailwind/bin/`) — different scope, use for manifests + PHP

### 5. `MODULE_TYPELESS_PACKAGE_JSON` warning

oxlint loads the `.ts` config as ESM. Without `"type": "module"` in `package.json`, Node logs a perf warning. Don't add `"type": "module"` blindly — it can break webpack's `package.json` reads. Leave the warning.

## Verification protocol after install

1. **Smoke**: `SOCKET=0 bunx oxlint 2>&1 | tail -40` — expect real findings (canonical rewrites + design-system mismatches), not zero.
2. **Positive control**: scratch JSX with known canonicals (`h-full w-full`, `backdrop-blur-[12px]`, `leading-[1.5]`, `before:[mask-position:center]`) must produce the expected `enforce-canonical-classes` diagnostics.
3. **Confirm entry point**: a file with `className='rounded-md p-4'` should NOT flag `rounded-md` as unknown. If it does, switch the entry point to the actual `@theme` source.
4. **Confirm `^es:` ignore**: a file with `className='es:font-mono'` should NOT flag `es:font-mono` as unknown.

## What it does NOT cover

- `manifest.json` files (Eightshift's class location in `tailwind.parts/options/combinations`) — plugin only handles JS/JSX/TSX.
- PHP files with `tailwindClasses(...)` calls — plugin doesn't lint PHP.
- Use `tw-sweep` (`eightshift-frontend-libs-tailwind/bin/tw-sweep.mjs`) for those.
