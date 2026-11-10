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

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Validated local build artifact.
		$json = @\file_get_contents($manifest);
		$data = \is_string($json) ? \json_decode($json, true) : null;
		if (!\is_array($data) || ($data['version'] ?? null) !== 1 || !\is_array($data['fonts'] ?? null) || !\is_string($data['css'] ?? null)) {
			return;
		}

		$fonts = [];
		foreach ($data['fonts'] as $font) {
			if (!\is_array($font) || !\is_string($font['hash'] ?? null) || !\preg_match('/^[a-f0-9]{64}$/D', $font['hash']) || ($font['file'] ?? null) !== 'fonts/' . $font['hash'] . '.woff2') {
				return;
			}
			$file = \realpath($root . '/' . $font['file']);
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- A file removed during deployment must fail closed.
			if ($file === false || !\str_starts_with($file, $root . \DIRECTORY_SEPARATOR) || !\is_file($file) || !\is_readable($file) || @\hash_file('sha256', $file) !== $font['hash']) {
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
}
