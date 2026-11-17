<?php

/**
 * Request-scoped, same-origin font sharing.
 *
 * @package EightshiftLibs\Enqueue
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue;

/**
 * Shares scalar registry data even between independently prefixed library copies.
 */
final class SharedFonts
{
	/**
	 * Validate a local manifest and enqueue one content-addressed style per face.
	 *
	 * @param string $directory Local public directory.
	 * @param string $url Same-origin public URL.
	 */
	public static function enqueue(string $directory, string $url): void
	{
		$root = \realpath($directory);
		if ($root === false) {
			return;
		}
		$manifest = \realpath($root . '/shared-fonts.json');
		if ($manifest === false || !\str_starts_with($manifest, $root . \DIRECTORY_SEPARATOR) || !\is_readable($manifest)) {
			return;
		}

		$origin = \wp_parse_url(\home_url('/'));
		$provider = \wp_parse_url($url);
		if (!\is_array($origin) || !\is_array($provider) || isset($provider['user']) || isset($provider['pass']) || isset($provider['query']) || isset($provider['fragment'])) {
			return;
		}
		foreach (['scheme', 'host', 'port'] as $part) {
			if (($origin[$part] ?? null) !== ($provider[$part] ?? null)) {
				return;
			}
		}
		if (!\in_array($provider['scheme'] ?? '', ['http', 'https'], true) || \preg_match('/[<>"\'\\\\\x00-\x20]/', $url)) {
			return;
		}

		$data = self::readManifest($manifest);
		if (!\is_array($data) || ($data['version'] ?? null) !== 1 || !\is_array($data['fonts'] ?? null) || !\is_string($data['css'] ?? null)) {
			return;
		}

		$fonts = [];
		foreach ($data['fonts'] as $font) {
			if (!\is_array($font) || !\is_string($font['hash'] ?? null) || !\preg_match('/^[a-f0-9]{64}$/D', $font['hash']) || ($font['file'] ?? null) !== 'fonts/' . $font['hash'] . '.woff2') {
				return;
			}
			$path = $root . '/' . $font['file'];
			\clearstatcache(true, $path);
			$file = \realpath($path);
			if ($file === false || !\str_starts_with($file, $root . \DIRECTORY_SEPARATOR)) {
				return;
			}
			\clearstatcache(true, $file);
			if (!\is_file($file) || !\is_readable($file) || !self::verifyFont($file, $font['hash'])) {
				return;
			}
			$fonts[$font['hash']] = \rtrim($url, '/') . '/' . $font['file'];
		}

		// Restrict the artifact to plain face rules; escapes, nested rules and HTML are not accepted.
		$css = \preg_replace('~/\*.*?\*/~s', '', $data['css']);
		if ($css === null || \str_contains($css, '__eightshift_font_') || \preg_match('/[<>\\\\\x00-\x08\x0b\x0c\x0e-\x1f]/', $css)) {
			return;
		}
		\preg_match_all('/@font-face\s*\{[^{}@]*\}/i', $css, $matches);
		if (\trim((string) \preg_replace('/@font-face\s*\{[^{}@]*\}/i', '', $css)) !== '') {
			return;
		}
		$faces = [];
		foreach ($matches[0] as $face) {
			$valid = true;
			$face = \preg_replace_callback('/url\(\s*(["\']?)([^()"\'\s]+)\1\s*\)/i', static function (array $match) use ($fonts, &$valid): string {
				if (!\preg_match('~^\./fonts/([a-f0-9]{64})\.woff2$~D', $match[2], $hash) || !isset($fonts[$hash[1]])) {
					$valid = false;
					return '';
				}
				return 'url("__eightshift_font_' . $hash[1] . '__")';
			}, $face);
			if (!$valid || $face === null || \preg_match('/url\s*\(/i', (string) \preg_replace('/url\("__eightshift_font_[a-f0-9]{64}__"\)/', '', $face))) {
				return;
			}
			$faces[] = $face;
		}

		// Deliberately not a class static: Strauss copies must share the same request data.
		$registry = &$GLOBALS['eightshift_shared_fonts_v1'];
		$registry ??= [];
		foreach ($fonts as $hash => $fontUrl) {
			$registry[$hash] ??= $fontUrl;
		}
		foreach ($faces as $face) {
			$handle = 'eightshift-font-face-' . \hash('sha256', $face);
			$face = \preg_replace_callback('/__eightshift_font_([a-f0-9]{64})__/', static fn(array $match): string => $registry[$match[1]], $face);
			if (!\wp_style_is($handle, 'registered')) {
				// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Inline-only content-addressed handle has no cacheable resource.
				\wp_register_style($handle, false, [], null);
				\wp_add_inline_style($handle, $face);
			}
			\wp_enqueue_style($handle);
		}
	}

	/**
	 * Read an unchanged manifest once per request, including across prefixed copies.
	 *
	 * @param string $manifest Resolved manifest path.
	 * @return mixed Decoded manifest.
	 */
	private static function readManifest(string $manifest): mixed
	{
		$cache = &$GLOBALS['eightshift_shared_fonts_manifests_v1'];
		$cache ??= [];
		$state = \PHP_OS_FAMILY === 'Windows' ? null : self::getFontState($manifest);
		$stable = $state !== null && $state['mtime'] < \time() && $state['ctime'] < \time();
		if ($stable && isset($cache[$manifest]) && $cache[$manifest]['state'] === $state) {
			return $cache[$manifest]['data'];
		}
		unset($cache[$manifest]);
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents, WordPress.PHP.NoSilencedErrors.Discouraged -- Validated local build artifact may disappear during deployment.
		$json = @\file_get_contents($manifest);
		$data = \is_string($json) ? \json_decode($json, true) : null;
		if ($stable && \is_array($data) && self::getFontState($manifest) === $state) {
			$cache[$manifest] = ['state' => $state, 'data' => $data];
		}
		return $data;
	}

	/**
	 * Verify a font's contents, reusing only an unchanged, previously verified file.
	 *
	 * @param string $file Resolved local font path.
	 * @param string $hash Expected SHA-256 hash.
	 */
	private static function verifyFont(string $file, string $hash): bool
	{
		// A shared global lets independently prefixed library copies reuse verification.
		$verified = &$GLOBALS['eightshift_shared_fonts_verified_v1'];
		$verified ??= [];

		$now = \time();
		$state = null;
		// Windows ctime is creation time, so it cannot detect changes to a font's contents.
		if (\PHP_OS_FAMILY !== 'Windows') {
			$state = self::getFontState($file);
		}
		// PHP reports whole-second timestamps; recently changed files cannot be cached safely.
		$stable = $state !== null && $state['mtime'] < $now && $state['ctime'] < $now;
		if ($stable && isset($verified[$file]) && $verified[$file]['hash'] === $hash && $verified[$file]['state'] === $state) {
			return true;
		}

		unset($verified[$file]);
		$host = \gethostname();
		$cacheKey = null;
		if ($stable && \is_string($host) && $host !== '' && \function_exists('wp_using_ext_object_cache') && \wp_using_ext_object_cache()) {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- Only scalar metadata is encoded and never unserialized.
			$cacheKey = \hash('sha256', \serialize([$host, $file, $hash, $state]));
			if (\wp_cache_get($cacheKey, 'eightshift_shared_fonts_v1') === true && self::getFontState($file) === $state) {
				$verified[$file] = ['hash' => $hash, 'state' => $state];
				return true;
			}
		}
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- A file removed during deployment must fail closed.
		if (@\hash_file('sha256', $file) !== $hash) {
			return false;
		}

		if ($state !== null && self::getFontState($file) !== $state) {
			return false;
		}
		if ($stable) {
			$verified[$file] = [
				'hash' => $hash,
				'state' => $state,
			];
			if ($cacheKey !== null) {
				\wp_cache_set($cacheKey, true, 'eightshift_shared_fonts_v1', 300);
			}
		}

		return true;
	}

	/**
	 * Get the file identity and change indicators used to validate cached hashes.
	 *
	 * @param string $file Resolved local font path.
	 * @return array{dev: int, ino: int, mode: int, uid: int, gid: int, size: int, mtime: int, ctime: int}|null
	 */
	private static function getFontState(string $file): ?array
	{
		\clearstatcache(true, $file);
		// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- A file may disappear during deployment.
		$state = @\stat($file);
		if ($state === false) {
			return null;
		}

		return [
			'dev' => $state['dev'],
			'ino' => $state['ino'],
			'mode' => $state['mode'],
			'uid' => $state['uid'],
			'gid' => $state['gid'],
			'size' => $state['size'],
			'mtime' => $state['mtime'],
			'ctime' => $state['ctime'],
		];
	}
}
