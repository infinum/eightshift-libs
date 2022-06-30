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
	 * Label Generator trait.
	 */
	use LabelGeneratorTrait;

	/**
	 * Media trait.
	 */
	use MediaTrait;

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

		// If parent path is missing provide project root.
		if (!$parentPath) {
			$parentPath = Components::getProjectPaths('root');
		} else {
			// Remove slash.
			$parentPath = \trim($parentPath, $sep);
			$parentPath = "{$sep}{$parentPath}{$sep}";
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
			$component = \ltrim($component, $sep);
			$componentPath = $component;
		} else {
			$componentPath = Components::getProjectPaths('blocksComponents', "{$component}{$sep}{$component}.php", $sep);
		}

		$componentPath = "{$parentPath}{$componentPath}";

		if ($useComponentDefaults) {
			$manifest = Components::getManifest($componentPath);
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
	 * Render component/block partial.
	 *
	 * @param string $type Type of content block, component, variable, etc.
	 * @param string $parent Parent block/component name.
	 * @param string $name Name of the partial. It can be without extension so .php is used.
	 * @param array<string, mixed> $attributes Attributes that will be passed to partial.
	 * @param string $partialFolderName Partial folder name.
	 *
	 * @throws ComponentException When we're unable to find the partial.
	 *
	 * @return string Partial html.
	 */
	public static function renderPartial(
		string $type,
		string $parent,
		string $name,
		array $attributes = [],
		string $partialFolderName = 'partials'
	): string {
		$sep = \DIRECTORY_SEPARATOR;

		// If no extension is provided use php.
		if (\strpos($name, '.php') === false) {
			$name = "{$name}.php";
		}

		$partialPath = "{$parent}{$sep}{$partialFolderName}{$sep}{$name}";

		// Detect folder based on the name.
		switch ($type) {
			case 'block':
			case 'blocks':
			case 'custom':
				$path = Components::getProjectPaths('blocksCustom', $partialPath);
				break;
			case 'component':
			case 'components':
				$path = Components::getProjectPaths('blocksComponents', $partialPath);
				break;
			case 'variation':
			case 'variations':
				$path = Components::getProjectPaths('blocksVariations', $partialPath);
				break;
			case 'wrapper':
				$path = Components::getProjectPaths('blocksWrapper', $partialPath);
				break;
			default:
				$path = Components::getProjectPaths('root', $partialPath);
				break;
		}

		// Bailout if file is missing.
		if (!\file_exists($path)) {
			throw ComponentException::throwUnableToLocatePartial($path);
		}

		\ob_start();

		require $path;

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

		// If user provides url with .php at the end.
		if (\strpos(\end($pathNew), '.php') !== false) {
			\array_pop($pathNew);
		}

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
		$path = \rtrim($path, $sep);

		$manifest = "{$path}{$sep}manifest.json";

		if (!\file_exists($manifest)) {
			throw ComponentException::throwUnableToLocateComponent($manifest);
		}

		return \json_decode(\implode(' ', (array)\file($manifest)), true);
	}

	/**
	 * Internal helper for getting all project paths for easy mocking in tests.
	 *
	 * @param string $type Type fo path to return.
	 * @param string $sufix Additional sufix path to add.
	 * @param string $prefix Additional prefix insted of dirname path.
	 * @param bool $useSufixSlash Force / at the end of the path.
	 *
	 * @return string
	 */
	public static function getProjectPaths(string $type = '', string $sufix = '', string $prefix = '', bool $useSufixSlash = true): string
	{
		$sep = \DIRECTORY_SEPARATOR;

		$path = '';
		$internalPrefix = \dirname(__FILE__, 5);

		if (\getenv('ES_TEST')) {
			$internalPrefix = \dirname(__FILE__, 3);
		}

		switch ($type) {
			case 'projectRoot':
				$internalPrefix = \dirname(__FILE__, 8);

				if (\getenv('ES_TEST')) {
					$internalPrefix = \dirname(__FILE__, 3);
				}
				break;
			case 'setupJson':
				$internalPrefix = \dirname(__FILE__, 8);

				if (\getenv('ES_TEST')) {
					$internalPrefix = \dirname(__FILE__, 3);
					$path = "cliOutput{$sep}setup";
				}

				break;
			case 'cliOuput':
				if (\getenv('ES_TEST')) {
					$internalPrefix = \dirname(__FILE__, 3);
					$path = "cliOutput";
				}

				break;
			case 'wpContent':
				$internalPrefix = \dirname(__FILE__, 6);
				break;
			case 'frontendLibsBlocks':
				$path = "node_modules{$sep}@eightshift{$sep}frontend-libs{$sep}blocks{$sep}init{$sep}src{$sep}Blocks";

				if (\getenv('ES_TEST')) {
					$path = "cliOutput{$sep}src{$sep}Blocks";
				}
				break;
			case 'libs':
				$path = "vendor{$sep}infinum{$sep}eightshift-libs";

				if (\getenv('ES_TEST')) {
					$path = '';
				}

				break;
			case 'blocks':
				$path = "src{$sep}Blocks";

				if (\getenv('ES_TEST')) {
					$path = "cliOutput{$sep}src{$sep}Blocks";
				}
				break;
			case 'blocksCustom':
				$name = 'custom';
				$path = "src{$sep}Blocks{$sep}{$name}";

				if (\getenv('ES_TEST')) {
					$path = "cliOutput{$sep}src{$sep}Blocks{$sep}{$name}";
				}
				break;
			case 'blocksComponents':
				$name = 'components';
				$path =  "src{$sep}Blocks{$sep}{$name}";

				if (\getenv('ES_TEST')) {
					$path = "cliOutput{$sep}src{$sep}Blocks{$sep}{$name}";
				}
				break;
			case 'blocksVariations':
				$name = 'variations';
				$path = "src{$sep}Blocks{$sep}{$name}";

				if (\getenv('ES_TEST')) {
					$path = "cliOutput{$sep}src{$sep}Blocks{$sep}{$name}";
				}
				break;
			case 'blocksWrapper':
				$name = 'wrapper';
				$path = "src{$sep}Blocks{$sep}{$name}";

				if (\getenv('ES_TEST')) {
					$path = "cliOutput{$sep}src{$sep}Blocks{$sep}{$name}";
				}
				break;
		}

		if (!$prefix) {
			$prefix = $internalPrefix;
		} else {
			$prefix = \trim($prefix, $sep);
		}

		$path = \trim($path, $sep);
		$path = "{$prefix}{$sep}{$path}{$sep}";

		$isFile = false;

		if ($sufix) {
			$sufix = \trim($sufix, $sep);
			$isFile = \strpos($sufix, '.') !== false;
		}

		if ($useSufixSlash) {
			$newPath = "{$path}{$sufix}";
			$newPath = \rtrim($newPath, $sep);

			if (!$isFile) {
				return str_replace("{$sep}{$sep}", $sep, "{$newPath}{$sep}");
			}

			return str_replace("{$sep}{$sep}", $sep, $newPath);
		}

		if (!$isFile) {
			return str_replace("{$sep}{$sep}", $sep, "{$path}{$sufix}");
		}

		return str_replace("{$sep}{$sep}", $sep, $path);
	}
}
