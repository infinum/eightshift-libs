# Manifest cache

Runtime manifest caches exclude the optional examples fixture data used by provisioning and testing tools. Legacy cache entries without the version marker are discarded on first use after updating this library. Later cache rebuilds also happen when the project cache version changes.

The transient cache stamp uses a content hash of the manifest payload instead of the file modification time, so multiple web servers sharing one database no longer invalidate each other's cache on every request. The cache read path never writes to the database, and the large transient payload is stored without autoloading when the WordPress version supports it.

[Back to README](../README.md)
