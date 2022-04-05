<?php

/**
 * Helpers for components
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Blocks\AbstractBlocks;
use EightshiftLibs\Exception\ComponentException;

/**
 * Helpers for components
 */
class Components
{
	/**
	 * Store trait.
	 */
	use StoreTrait;

	/**
	 * Css Variables trait.
	 */
	use CssVariablesTrait;

	/**
	 * Selectors trait.
	 */
	use SelectorsTrait;

	/**
	 * Attributes trait.
	 */
	use AttributesTrait;

	/**
	 * Generic object helper trait.
	 */
	use ObjectHelperTrait;

	/**
	 * Shortcode trait.
	 */
	use ShortcodeTrait;

	/**
	 * Post trait.
	 */
	use PostTrait;

	/**
	 * Renders a components and (optionally) passes some attributes to it.
	 *
	 * Note about "parentClass" attribute: If provided, the component will be wrapped with a
	 * parent BEM selector. For example, if $attributes['parentClass'] === 'header' and $component === 'logo'
	 * are set, the component will be wrapped with a <div class="header__logo"></div>.
	 *
	 * @param string $component Component's name or full path (ending with .php).
	 * @param array<string, mixed> $attributes Array of attributes that's implicitly passed to component.
	 * @param string $parentPath If parent path is provides it will be appended to the file location.
	 *                           If not get_template_directory_uri() will be used as a default parent path.
	 * @param bool $useComponentDefaults If true the helper will fetch component manifest and merge default attributes in the original attributes list.
	 *
	 * @throws ComponentException When we're unable to find the component by $component.
	 *
	 * @return string
	 */
	public static function render(string $component, array $attributes = [], string $parentPath = '', bool $useComponentDefaults = false): string
	{
		$sep = \DIRECTORY_SEPARATOR;

		if (empty($parentPath)) {
			$parentPath = \get_template_directory();
		}

		/**
		 * Detect if user passed component name or path.
		 *
		 * If the path was passed, we need to get the component name, in case the
		 * parentClass attribute was added, because the class of the wrapper need to look like
		 *
		 * parentClass__componentName
		 *
		 * not
		 *
		 * parentClass__componentName.php
		 */
		if (\strpos($component, '.php') !== false) {
			$componentPath = "{$parentPath}{$sep}$component";

			if ($useComponentDefaults) {
				$manifest = self::getManifest($parentPath);
			}
		} else {
			$blocksParentPath = AbstractBlocks::PATH_BLOCKS_PARENT;
			$componentsPath = AbstractBlocks::PATH_COMPONENTS;
			$componentPath = "{$parentPath}{$blocksParentPath}{$componentsPath}{$sep}{$component}";

			$componentPath = "{$componentPath}{$sep}{$component}.php";

			if ($useComponentDefaults) {
				$manifest = self::getManifest("{$componentPath}");
			}
		}

		if (!\file_exists($componentPath)) {
			throw ComponentException::throwUnableToLocateComponent($componentPath);
		}

		// Merge default attributes with the component attributes.
		if ($useComponentDefaults && isset($manifest['attributes'])) {
			$attributes = Components::getDefaultRenderAttributes($manifest, $attributes);
		}

		\ob_start();

		// Wrap component with parent BEM selector if parent's class is provided. Used
		// for setting specific styles for components rendered inside other components.
		if (isset($attributes['parentClass'])) {
			$component = \str_replace('.php', '', $component);
			\printf('<div class="%s">', \esc_attr("{$attributes['parentClass']}__{$component}")); // phpcs:ignore Eightshift.Security.CustomEscapeOutput.OutputNotEscaped
		}

		require $componentPath;

		if (isset($attributes['parentClass'])) {
			echo '</div>'; // phpcs:ignore Eightshift.Security.CustomEscapeOutput.OutputNotEscaped
		}

		return \trim((string) \ob_get_clean());
	}

	/**
	 * Get manifest json. Generally used for getting block/components manifest.
	 *
	 * @param string $path Absolute path to manifest folder.
	 * @param string $name Block/Component name.
	 *
	 * @throws ComponentException When we're unable to find the component by $component.
	 *
	 * @return array<string, mixed>
	 */
	public static function getManifest(string $path, string $name = ''): array
	{
		$pathNew = \trim($path, \DIRECTORY_SEPARATOR);

		$pathNew = \explode(\DIRECTORY_SEPARATOR, $pathNew);

		// Find last item to get name.
		$item = $pathNew[\count($pathNew) - 1] ?? '';

		// Settings details.
		if ($item === 'Blocks' || $path === 'settings') {
			return Components::getSettings();
		}

		// Wrapper details.
		if ($item === 'wrapper' || $path === 'wrapper') {
			return Components::getWrapper();
		}

		$type = $pathNew[\count($pathNew) - 2] ?? '';
		$itemName = $item;

		if ($name) {
			$itemName = $name;
		}

		// Components settings.
		if ($type === 'components' || $path === 'component') {
			return Components::getComponent($itemName);
		}

		// Blocks settings.
		if ($type === 'custom' || $path === 'block') {
			return Components::getBlock($itemName);
		}

		return [];
	}

	/**
	 * Get manifest json. Generally used for getting block/components manifest. Used to directly fetch json file.
	 * Used in combination with getManifest helper.
	 *
	 * @param string $path Absolute path to manifest folder.
	 *
	 * @throws ComponentException When we're unable to find the component by $component.
	 *
	 * @return array<string, mixed>
	 */
	public static function getManifestDirect(string $path): array
	{
		$sep = \DIRECTORY_SEPARATOR;
		$path = \trim($path, $sep);

		$manifest = "{$sep}{$path}{$sep}manifest.json";

		if (!\file_exists($manifest)) {
			throw ComponentException::throwUnableToLocateComponent($manifest);
		}

		return \json_decode(\implode(' ', (array)\file($manifest)), true);
	}
}
