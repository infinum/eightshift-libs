<?php

/**
 * The Assets abstract class.
 *
 * @package EightshiftLibs\Enqueue
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue;

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
	 * Media style const
	 */
	public const MEDIA_ALL    = 'all';
	public const MEDIA_PRINT  = 'print';
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
	 * @return array List of all the script dependencies.
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
	 * @return array List of all the script dependencies.
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
	 *      'someValue'    => esc_html__( 'Hi there!', 'text-domain' ),
	 *      'anotherValue' => $variableValue,
	 *  ]
	 * ];
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_localize_script/
	 *
	 * @return array Key value pair of different localizations.
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
	 * @return array List of all the style dependencies.
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
	 * @return array List of all the style dependencies.
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
