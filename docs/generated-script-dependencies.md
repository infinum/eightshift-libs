# Generated script dependencies

The admin, block editor, block frontend and theme script enqueue methods merge generated WordPress dependency handles with the handles declared by the project. Declared handles keep their order; generated handles are appended only when not already present. All generated handles must be registered and form an acyclic dependency graph: missing or circular dependencies can prevent scripts from loading.

If an enqueued theme script has not printed by the footer script hook, WordPress `_doing_it_wrong` diagnostics report unregistered dependencies. Check `WP_DEBUG` and the debug log, then register the missing handles before printing. Dependencies are not removed or registered automatically; late registrations remain supported. This diagnostic does not detect cycles and requires `wp_footer()`.

For a script manifest key such as `applicationBlocksEditor.js`, the reader first tries `applicationBlocksEditor.asset.json`, then the legacy `applicationBlocksEditor.json` key. Both are resolved through the asset manifest. Metadata must contain a `dependencies` list of nonempty strings, for example:

```json
{"dependencies":["wp-blocks","wp-element","wp-i18n"],"version":"build-version"}
```

Only readable JSON files inside the owning theme or plugin's public directory are accepted. Manifest values can be paths relative to that directory or URLs using the WordPress public directory's URL path. Other custom public URL prefixes are not automatically mapped to local files. For non-editor scripts, missing or invalid metadata leaves declared dependencies unchanged. For editor scripts, a declared metadata entry must resolve to valid readable metadata; an invalid or missing referenced file stops registration instead of silently falling back to classic. If neither metadata key is declared, classic compatibility is retained. The generated `version` field does not override `getAssetsVersion()`.

[Back to README](../README.md)
