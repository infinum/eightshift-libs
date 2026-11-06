![image](https://repository-images.githubusercontent.com/179663878/59edeed5-62a8-4c8e-95a4-a51e27457de2)

[![GitHub tag](https://img.shields.io/github/tag/infinum/eightshift-libs.svg?style=for-the-badge)](https://github.com/infinum/eightshift-libs)
[![GitHub stars](https://img.shields.io/github/stars/infinum/eightshift-libs.svg?style=for-the-badge&label=Stars)](https://github.com/infinum/eightshift-libs)
[![license](https://img.shields.io/github/license/infinum/eightshift-libs.svg?style=for-the-badge)](https://github.com/infinum/eightshift-libs)
[![codecov](https://img.shields.io/codecov/c/gh/infinum/eightshift-libs/develop?label=Codecov&style=for-the-badge&token=d4wKeqf1r0)](https://codecov.io/gh/infinum/eightshift-libs)

# Eightshift Libs

This library is aimed at bringing the modern backend development tools to the [Eightshift Boilerplate](https://github.com/infinum/eightshift-boilerplate) or [Eightshift Boilerplate Plugin](https://github.com/infinum/eightshift-boilerplate-plugin), but you can use it on any WordPress project.

## Manifest cache

Runtime manifest caches exclude the optional examples fixture data used by provisioning and testing tools. Legacy cache entries without the version marker are discarded on first use after updating this library. Later cache rebuilds also happen when the project cache version changes.

The transient cache stamp uses a content hash of the manifest payload instead of the file modification time, so multiple web servers sharing one database no longer invalidate each other's cache on every request. The cache read path never writes to the database, and the large transient payload is stored without autoloading when the WordPress version supports it.

## Experimental unified editor modules

The opt-in unified editor build emits `format: "module"` and `readiness: "eightshift-editor-v1"` alongside `dependencies` and `version` in its generated editor asset JSON. `AbstractEnqueueBlocks::enqueueBlockEditorScript()` recognizes this metadata through the same public-directory-confined reader used for generated dependencies. Existing build modes and non-editor enqueues remain classic scripts.

This integration retains the editor handle, `wp_register_script()` dependencies, WordPress globals, localization, and translations. Only the matching external editor script becomes `type="module"`; its other attributes (including CSP nonces) are preserved using `WP_HTML_Tag_Processor`. The generated wrapper statically imports the editor entry and then calls `globalThis.eightshiftEditorModules.complete(import.meta.url)`.

A classic inline **before** script creates a shared version-1 readiness API. Consumers that need editor registrations must explicitly wait for their own handle:

```js
await globalThis.eightshiftEditorModules.ready.get('my-project-block-editor-scripts');
```

Call this after that handle's before script has run; a missing map entry is not a readiness signal. The promise resolves when the initial registration module has evaluated, **not** when later lazy extensions initialize. `register(handle)` is idempotent, and different theme/plugin asset prefixes produce independent handles and promises. Duplicate registered handles and incompatible readiness metadata fail explicitly. Rejections are logged without replacing the rejected promise exposed to consumers.

### Opt-in limits

- Hook editor JavaScript to `enqueue_block_editor_assets`, not `enqueue_block_assets`; shared content styles can remain on `enqueue_block_assets`. Gutenberg collects iframe assets with a temporary `WP_Scripts` registry and prints its footer before the main editor enqueue. Module configuration and tag filters are scoped to the active registry, so that temporary footer does not close registration in the restored main registry. Repeated enqueues are idempotent, including when core copies registration objects between registries.
- Requires `WP_HTML_Tag_Processor` and normal parser-inserted script output while the document is loading. Register before scripts are printed. PHP rejects already-printed handles and registration after an observed footer-processing pass in that same registry; tracking starts at the first module validation. The browser before script remains the authoritative late-registration guard, including output that occurred before tracking began.
- Async/defer strategies or attributes are rejected on the module. Registered transitive dependencies are checked at setup and again before printing: any `strategy`, `async`, or `defer` script data rejects the opt-in, even if WordPress might otherwise downgrade that strategy. Classic WordPress globals must execute synchronously before the module. Native module deferral is sufficient. Do not use `wp_enqueue_script_module()` for these builds: WordPress packages are still classic globals.
- Loading/evaluation failures, a script load without its completion signal, and missing completion by `DOMContentLoaded` reject readiness. Top-level await or dynamic insertion that crosses that deadline is unsupported; late completion cannot reverse failure. No timers or inline event-handler attributes are used.
- Classic script dependencies alone do **not** wait for a module to evaluate. Dependent classic consumers must use the readiness promise. Core editor boot and `wp.domReady` are not modified; validate the experiment against the target WordPress/editor and plugins before enabling it.
- CSP must allow the WordPress-generated inline before script as well as the module. Existing external-script nonce attributes are preserved; this integration does not invent or bypass a site's CSP policy.
- Filters run late and target only the exact handle/id/source. Dependency attributes injected directly by other plugins' HTML filters are not represented in the dependency registry and cannot be detected by this handle-only integration. Plugins can also override later filters, move scripts, or interfere with output; this opt-in contract cannot guarantee compatibility with those changes.

## Documentation

**For the full documentation please check this [link](https://eightshift.com).**

## Who do I talk to?

If you have any questions or problems, please [open an issue](https://github.com/infinum/eightshift-libs/issues) on github and we will do our best to give you a timely answer.

## Maintainers
Eightshift Libs is maintained and sponsored by
[Eightshift](https://eightshift.com) and [Infinum](https://infinum.com).

## License
Eightshift Libs [Eightshift](https://eightshift.com) and [Infinum](https://infinum.com). It is free software, and may be redistributed under the terms specified in the LICENSE file.
