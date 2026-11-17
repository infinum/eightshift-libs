# Shared fonts (optional)

Publish `public/shared-fonts.json` alongside assets:

```json
{"version":1,"fonts":[{"hash":"<lowercase SHA-256 of the WOFF2 bytes>","file":"fonts/<same hash>.woff2"}],"css":"@font-face{font-family:Example;src:url('./fonts/<same hash>.woff2') format('woff2');font-weight:400;font-style:normal;font-display:swap;}"}
```

The existing admin, theme and block style enqueue methods automatically load this
artifact. The block-assets hook also supplies editor iframe styles. Custom enqueue
implementations can call the inherited `enqueueSharedFonts()` method, or
`EightshiftLibs\Enqueue\SharedFonts::enqueue($publicDirectory, $publicUrl)`.
Keep font declarations out of ordinary CSS when emitting this manifest.

Only version 1, complete plain `@font-face` rules and exact `./fonts/<hash>.woff2`
URLs are accepted. CSS escapes, nested rules, HTML and other URLs are rejected.
Files must resolve inside the public directory and match their SHA-256 hashes.
The automatic enqueue methods use the WordPress theme or plugin public URL; it must
serve these files and have the same scheme, host and port as WordPress `home_url()`.
CDN origins are intentionally unsupported. Missing or invalid manifests are a
no-op. Existing builds without the artifact are unchanged.

The first valid provider in a request supplies the URL for each hash, including
across independently Strauss-prefixed library copies. Face handles derive from
complete face content with hash-based URL placeholders, not family names, so
identical declarations share one inline style while distinct weights, subsets,
descriptors and versions survive. Declaration formatting differences may produce
separate handles. Styles use a false source and no external stylesheet request.
The registry and manifest cache contain only request-local data: there are no filesystem writes,
transients or database changes. Unchanged, older files reuse manifest reads and font
verification within a request, including across prefixed copies. Recently changed
files are checked again; Windows always rereads and rehashes because its ctime
cannot reliably detect content changes. When a persistent WordPress object cache is
available, successful font verification is also cached for five minutes, scoped to
the hostname, resolved path, expected hash and file metadata (including device and
inode). Hosts sharing an object cache must have unique hostnames. Filesystems must
provide reliable device, inode and change timestamps; metadata-preserving changes
or duplicate hostnames can defeat invalidation. Disable the persistent object cache
for deployments where these guarantees cannot be made. Provider files must remain publicly reachable
while pages using them are loaded, including cached pages that outlive the PHP
request. Deployment and cache retention remain the project's responsibility.

[Back to README](../README.md)
