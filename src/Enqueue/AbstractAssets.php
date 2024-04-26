<?php

/**
 * The Assets abstract class.
 *
 * @package EightshiftLibs\Enqueue
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue;

use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Cache\ManifestCacheInterface;
use EightshiftLibs\Exception\InvalidManifest;
use EightshiftLibs\Services\ServiceInterface;

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
	 * Instance variable for manifest cache.
	 *
	 * @var ManifestCacheInterface
	 */
	protected $manifestCache;

	/**
	 * Create a new instance.
	 *
	 * @param ManifestCacheInterface $manifestCache Inject manifest cache.
	 */
	public function __construct(ManifestCacheInterface $manifestCache)
	{
		$this->manifestCache = $manifestCache;
	}

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
	 * Get frontend script dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/#default-scripts-included-and-registered-by-wordpress
	 *
	 * @return array<int, string> List of all the script dependencies.
	 */
	protected function getFrontendScriptDependencies(): array
	{
		return [];
	}

	/**
	 * Get admin script dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/#default-scripts-included-and-registered-by-wordpress
	 *
	 * @return array<int, string> List of all the script dependencies.
	 */
	protected function getAdminScriptDependencies(): array
	{
		return [];
	}

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
	 * Get front end style dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return array<int, string> List of all the style dependencies.
	 */
	protected function getFrontendStyleDependencies(): array
	{
		return [];
	}

	/**
	 * Get admin style dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return array<int, string> List of all the style dependencies.
	 */
	protected function getAdminStyleDependencies(): array
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
	 * @throws InvalidManifest If manifest key is missing.
	 *
	 * @return string The value from the manifest.json file.
	 */
	public function setAssetsItem(string $key): string
	{
		$data = $this->manifestCache->getManifestCacheTopItem(AbstractManifestCache::ASSETS_KEY, AbstractManifestCache::TYPE_ASSETS);

		if (!isset($data[$key])) {
			throw InvalidManifest::missingManifestKeyException($key, 'public');
		}

		return $data[$key] ?? '';
	}

	/**
	 * Method that returns assets name used to prefix asset handlers.
	 *
	 * @return string
	 */
	abstract public function getAssetsPrefix(): string;

	/**
	 * Method that returns assets version for versioning asset handlers.
	 *
	 * @return string
	 */
	abstract public function getAssetsVersion(): string;
}
