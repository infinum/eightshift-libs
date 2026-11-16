# Generated script dependencies

The admin, block editor, block frontend and theme script enqueue methods merge generated WordPress dependency handles with the handles declared by the project. Declared handles keep their order; generated handles are appended only when not already present.

For a script manifest key such as `applicationBlocksEditor.js`, the reader first tries `applicationBlocksEditor.asset.json`, then the legacy `applicationBlocksEditor.json` key. Both are resolved through the asset manifest. Metadata must contain a `dependencies` list of nonempty strings, for example:

```json
{"dependencies":["wp-blocks","wp-element","wp-i18n"],"version":"build-version"}
```

Only readable JSON files inside the owning theme or plugin's public directory are accepted. Missing or invalid metadata leaves declared dependencies unchanged. The generated `version` field does not override `getAssetsVersion()`.

[Back to README](../README.md)
