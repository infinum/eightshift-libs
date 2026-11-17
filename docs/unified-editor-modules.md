# Unified editor modules

Unified editor builds share one ES module graph between initial registration and lazy editor code. This is the default editor mode in the Frontend Libs Tailwind 4 beta's `defineThemeBuild()` preset. PHP enables it only when valid editor asset metadata declares both `format: "module"` and `readiness: "eightshift-editor-v1"` alongside `dependencies`.

Libs 14 requires unified editor builds from Frontend Libs Tailwind 4; classic editor builds, including 3.x builds, are unsupported. Frontend and admin scripts remain classic. Generated metadata dependencies are merged for all supported scripts. When switching build modes, deploy matching scripts and metadata together with compatible PHP integration; unified output cannot be loaded as a classic script. The metadata URL must map to the local public directory as described in [Generated script dependencies](generated-script-dependencies.md).

## Loading and completion

The integration keeps `wp_register_script()`, the existing editor handle, WordPress dependency handles, localization and translations. Only the matching external editor script becomes `type="module"`; inline scripts remain classic scripts. Other attributes, including CSP nonces, are preserved with `WP_HTML_Tag_Processor`.

PHP reserves the external script's URL fragment (`#...`) for the encoded registered handle, replacing any existing fragment. The generated wrapper statically imports the editor entry and calls `globalThis.eightshiftEditorModules.complete(import.meta.url)`. Completion reads the handle from the fragment and matches the script's ID and readiness attribute, not its response URL. Redirects are supported as long as they do not replace or clear the fragment. This redirect fix requires no frontend-library update, asset rebuild or new project setting for builds already using this completion API.

## Waiting for editor registration

A classic inline script, printed before the module, creates a version-1 readiness API. Scripts that need the editor's registered blocks must wait for their own handle:

```js
const handle = 'my-project-block-editor-scripts';
const ready = globalThis.eightshiftEditorModules?.ready.get(handle);

if (!ready) {
  throw new Error(`Editor readiness is not registered for ${handle}`);
}

await ready;
```

Run this after the handle's inline bootstrap has executed, in a module or an async function. A missing map entry does not mean the editor is ready. The promise resolves after initial registration finishes, not after later lazy extensions load. Failures reject the promise and are also logged.

Repeated registration of the same handle is idempotent. Use distinct asset prefixes for independent themes and plugins; conflicting registrations and incompatible readiness metadata fail explicitly.

## Requirements and limits

- Hook editor JavaScript to `enqueue_block_editor_assets`, not `enqueue_block_assets`. Shared content styles can remain on `enqueue_block_assets`. The enqueue example uses this split; existing custom enqueue classes must update their own hooks.
- Frontend Libs Tailwind 4's conventional theme build requires WordPress 7.0 or newer. Custom builds must provide a compatible module-readiness integration.
- Keep the normal WordPress-generated script tags in the page HTML and register modules before scripts are printed. `WP_HTML_Tag_Processor` is required. PHP rejects already-printed handles and registration after a footer pass observed since module tracking began; the browser bootstrap also rejects registration after document loading.
- When adding a readiness bootstrap, the integration clones the registered script object to isolate WordPress registry snapshots. Code that stores a direct reference to that object before module configuration must look it up again afterward. Script handles, existing data, localization and translations remain in place.
- Module setup and footer tracking are scoped to each `WP_Scripts` registry. Gutenberg's temporary iframe registry does not close registration in the main editor. Repeated enqueues and copied registrations do not duplicate the readiness bootstrap.
- Do not add async/defer strategies or attributes to the module or its registered dependency chain. Classic WordPress dependencies must execute before the module. Modules already defer execution; use this integration rather than `wp_enqueue_script_module()` because WordPress package dependencies remain classic scripts.
- A loading/evaluation failure, a load event without a completion signal, or missing completion by `DOMContentLoaded` rejects readiness. Top-level await or dynamically inserted scripts that cross that deadline are unsupported; late completion cannot reverse failure.
- Classic script dependencies alone do not wait for module evaluation. Use the readiness promise where needed. Core editor boot and `wp.domReady` are not changed; verify block registration, editor controls and save/reload behavior with the target WordPress version and plugins.
- CSP must allow the WordPress-generated inline bootstrap and the external module. Existing external-script nonces are preserved; the integration does not bypass CSP.
- Filters that rewrite script IDs, sources, readiness attributes or loading strategies can break the integration. Late changes and dependency attributes added only to HTML are outside the registered dependency checks.

Missing, invalid or classic editor metadata fails registration. Editor script arguments are controlled by `editorScriptArgs()` independently of frontend strategy. Unsafe late changes to module tags or dependency strategies suppress the module tag and log an error rather than throwing during output.

[Back to README](../README.md)
