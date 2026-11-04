<?php

/**
 * The Assets abstract class.
 *
 * @package EightshiftLibs\Enqueue
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Services\ServiceInterface;
use Throwable;

/**
 * Class Assets
 *
 * This abstract class holds helper methods that can be used and overwritten in
 * user defined classes that extend the enqueue classes in their project.
 *
 * @package EightshiftLibs\Enqueue
 */
abstract class AbstractAssets implements ServiceInterface
{
	/**
	 * Media style const
	 */
	public const MEDIA_ALL = 'all';
	public const MEDIA_PRINT = 'print';
	public const MEDIA_SCREEN = 'screen';

	/**
	 * Load scripts in footer const
	 */
	public const IN_FOOTER = true;

	/**
	 * Get script localizations
	 *
	 * * Example: $localization_array => [
	 *  'localizationHandler' => [
	 *      'someValue'    => \esc_html__( 'Hi there!', 'text-domain' ),
	 *      'anotherValue' => $variableValue,
	 *  ]
	 * ];
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_localize_script/
	 *
	 * @return array<string, mixed> Key value pair of different localizations.
	 */
	protected function getLocalizations(): array
	{
		return [];
	}

	/**
	 * Get style media definition
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return string The media for which this stylesheet has been defined.
	 * Accepts media types like 'all', 'print' and 'screen',
	 * or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 * Default value: 'all'
	 */
	protected function getMedia(): string
	{
		return static::MEDIA_ALL;
	}

	/**
	 * Load script in footer
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return bool Whether to enqueue the script before </body> instead of in the <head>.
	 * Default value: true
	 */
	protected function scriptInFooter(): bool
	{
		return static::IN_FOOTER;
	}

	/**
	 * Load script 'defer' or 'async'.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return string Whether to enqueue the script normally, with defer or async.
	 * Default value: normal
	 */
	protected function scriptStrategy(): string
	{
		return '';
	}

	/**
	 * Additional script args.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return array<string, string|bool> Additional script args.
	 */
	protected function scriptArgs(): array
	{
		return [
			'strategy' => $this->scriptStrategy(),
			'in_footer' => $this->scriptInFooter(),
		];
	}

	/**
	 * Get the manifest data.
	 *
	 * @param string $key The key from the manifest.json file.
	 *
	 * @return string The value from the manifest.json file.
	 */
	public function setAssetsItem(string $key): string
	{
		return Helpers::getAsset($key);
	}

	/**
	 * Merge optional generated JSON dependencies with explicitly declared handles.
	 *
	 * The manifest may contain an un-hashed .asset.json or legacy .json key.
	 * Only files within the project public directory are read.
	 *
	 * @param string $scriptKey Script manifest key.
	 * @param array<int, string> $dependencies Explicit dependencies.
	 * @return array<int, string> Dependencies with generated handles appended.
	 */
	protected function mergeAssetDependencies(string $scriptKey, array $dependencies): array
	{
		if (!\str_ends_with($scriptKey, '.js')) {
			return $dependencies;
		}

		$root = \realpath(Helpers::getProjectPaths('public'));
		if ($root === false) {
			return $dependencies;
		}

		$projectRoot = \realpath(Helpers::getProjectPaths());

		if (\realpath(\get_stylesheet_directory()) === $projectRoot) {
			$publicUrl = \get_stylesheet_directory_uri() . '/public/';
		} elseif (\realpath(\get_template_directory()) === $projectRoot) {
			$publicUrl = \get_template_directory_uri() . '/public/';
		} else {
			$publicUrl = \plugins_url('public/', $projectRoot . '/index.php');
		}

		$publicUrlPath = \wp_parse_url($publicUrl, \PHP_URL_PATH);
		$publicUrlPath = \is_string($publicUrlPath) ? \rtrim(\rawurldecode($publicUrlPath), '/') . '/' : '';

		$entry = \substr($scriptKey, 0, -3);
		foreach ([$entry . '.asset.json', $entry . '.json'] as $key) {
			try {
				$asset = $this->setAssetsItem($key);
			} catch (Throwable) {
				continue;
			}

			$path = \wp_parse_url($asset, \PHP_URL_PATH);
			if (!\is_string($path)) {
				continue;
			}

			$path = \rawurldecode($path);
			if (\str_contains($path, "\0") || \str_contains($path, '\\')) {
				continue;
			}

			if (!\str_ends_with($path, '.json')) {
				continue;
			}

			if ($publicUrlPath !== '' && \str_starts_with($path, $publicUrlPath)) {
				$path = \substr($path, \strlen($publicUrlPath));
			}

			$file = \realpath($root . \DIRECTORY_SEPARATOR . \ltrim($path, '/'));
			if ($file === false || !\str_starts_with($file, $root . \DIRECTORY_SEPARATOR) || !\is_file($file) || !\is_readable($file)) {
				continue;
			}

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- The resolved file is local and confined to the public directory.
			$json = @\file_get_contents($file);
			$data = \is_string($json) ? \json_decode($json, true) : null;
			$generated = $data['dependencies'] ?? null;
			if (!\is_array($generated) || !\array_is_list($generated)) {
				continue;
			}

			foreach ($generated as $dependency) {
				if (!\is_string($dependency) || \trim($dependency) === '') {
					continue 2;
				}
			}

			foreach ($generated as $dependency) {
				if (!\in_array($dependency, $dependencies, true)) {
					$dependencies[] = $dependency;
				}
			}

			return $dependencies;
		}

		return $dependencies;
	}

	/**
	 * Method that returns assets name used to prefix asset handlers.
	 */
	abstract public function getAssetsPrefix(): string;

	/**
	 * Method that returns assets version for versioning asset handlers.
	 */
	abstract public function getAssetsVersion(): string;
}
