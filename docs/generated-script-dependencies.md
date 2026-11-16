# Generated script dependencies

The admin, block editor, block frontend and theme script enqueue methods merge generated WordPress dependency handles with the handles declared by the project. Declared handles keep their order; generated handles are appended only when not already present. All generated handles must be registered and form an acyclic dependency graph: missing or circular dependencies can prevent scripts from loading.

For a script manifest key such as `applicationBlocksEditor.js`, the reader first tries `applicationBlocksEditor.asset.json`, then the legacy `applicationBlocksEditor.json` key. Both are resolved through the asset manifest. Metadata must contain a `dependencies` list of nonempty strings, for example:

```json
{"dependencies":["wp-blocks","wp-element","wp-i18n"],"version":"build-version"}
```

Only readable JSON files inside the owning theme or plugin's public directory are accepted. Manifest values can be paths relative to that directory or URLs using the WordPress public directory's URL path. Other custom public URL prefixes are not automatically mapped to local files. Missing or invalid metadata leaves declared dependencies unchanged. The generated `version` field does not override `getAssetsVersion()`.

[Back to README](../README.md)
