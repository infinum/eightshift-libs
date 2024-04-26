<?php

/**
 * Class Blocks is the base class for Gutenberg blocks registration.
 * It provides the ability to register custom blocks using manifest.json.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Cache\ManifestCacheInterface;
use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Exception\InvalidManifest;
use EightshiftLibs\Exception\InvalidPath;
use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Services\ServiceInterface;
use WP_Block_Editor_Context;
use WP_Post;

/**
 * Class Blocks
 */

/**
 * Class Blocks
 */
abstract class AbstractBlocks implements ServiceInterface, RenderableBlockInterface
{
	/**
	 * Blocks builder array.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	const BLOCKS_BUILDER = [
		AbstractManifestCache::BLOCKS_KEY => [
			'multiple' => true,
			'validation' => [
				'blockName'
			],
		],
		AbstractManifestCache::COMPONENTS_KEY => [
			'multiple' => true,
			'validation' => [
				'componentName'
			],
		],
		AbstractManifestCache::VARIATIONS_KEY => [
			'multiple' => true,
			'validation' => [
				'name'
			],
		],
		AbstractManifestCache::WRAPPER_KEY => [
			'multiple' => false,
		],
		AbstractManifestCache::SETTINGS_KEY => [
			'multiple' => false,
			'validation' => [
				'namespace',
			],
		],
	];
	/**
	 * Instance variable for manifest cache.
	 */
	protected $manifestCache;

	/**
	 * Create a new instance.
	 *
	 * @param ManifestCacheInterface $manifestCache Inject manifest cache.
	 */
	public function __construct(ManifestCacheInterface $manifestCache) {
		$this->manifestCache = $manifestCache;
	}

	/**
	 * Create custom project color palette.
	 * These colors are fetched from the main settings manifest.json.
	 *
	 * @return void
	 */
	public function changeEditorColorPalette(): void
	{
		// Unable to use state due to this method is used in JS and store is not registered there.
		$colors = $this->getManifest(AbstractManifestCache::SETTINGS_KEY)['globalVariables']['colors'] ?? [];

		if ($colors) {
			\add_theme_support('editor-color-palette', $colors);
		}
	}

	/**
	 * Register multiple theme support options.
	 *
	 * @return void
	 */
	public function addThemeSupport(): void
	{
		\add_theme_support('align-wide');
	}

	/**
	 * Get blocks full data from global settings, blocks and wrapper.
	 *
	 * You should never call this method directly. It is used to prepare global store of data for all the blocks. Instead, you should call $this->blocks.
	 *
	 * @return void
	 */
	public function getBlocksDataFullRaw(): void
	{
		// Get global settings direct from file.
		$settings = $this->getManifest(AbstractManifestCache::SETTINGS_KEY);

		$namespace = $settings['namespace'];

		$blocks = \array_map(
			static function ($block) use ($namespace) {
				// Check if blocks-namespace is defined in block or in global manifest settings.
				$block['namespace'] = $namespace;
				$block['blockFullName'] = "{$namespace}/{$block['blockName']}";

				return $block;
			},
			$this->getManifest(AbstractManifestCache::BLOCKS_KEY)
		);

		// Register store and set all the data.
		Components::setStore();
		Components::setSettings($settings);
		Components::setBlocks($blocks);
		Components::setComponents($this->getManifest(AbstractManifestCache::COMPONENTS_KEY));
		Components::setVariations($this->getManifest(AbstractManifestCache::VARIATIONS_KEY));
		Components::setConfigFlags();
		Components::setPaths();

		if (Components::getConfigUseWrapper()) {
			Components::setWrapper($this->getManifest(AbstractManifestCache::WRAPPER_KEY));
		}
	}

	/**
	 * Get all allowed blocks with the full block name.
	 *
	 * Used to define blocks that are going to be used in your project using allowed_block_types_all filter.
	 * The function behaves similar to the native function except that it appends all the project blocks to
	 * the array of block type slugs sent as the first attribute.
	 *
	 * @hook allowed_block_types_all Available from WP 5.8.
	 *
	 * @param bool|string[] $allowedBlockTypes Array of block type slugs, or boolean to enable/disable all.
	 * @param WP_Block_Editor_Context $blockEditorContext The current block editor context.
	 *
	 * @return bool|string[] Boolean if you want to disallow or allow all blocks, or a list of all allowed blocks.
	 */
	public function getAllAllowedBlocksList($allowedBlockTypes, WP_Block_Editor_Context $blockEditorContext)
	{
		// Allow forms to be used correctly.
		if (
			$blockEditorContext->post instanceof WP_Post &&
			!empty($blockEditorContext->post->post_type) &&
			$blockEditorContext->post->post_type === 'eightshift-forms'
		) {
			return true;
		}

		if (\is_bool($allowedBlockTypes)) {
			return $allowedBlockTypes;
		}

		$allowedBlockTypes = \array_merge(
			\array_map(
				function ($block) {
					return $block['blockFullName'];
				},
				Components::getBlocks(),
			),
			$allowedBlockTypes,
		);

		// Allow reusable block.
		$allowedBlockTypes[] = 'eightshift-forms/forms';
		$allowedBlockTypes[] = 'core/block';
		$allowedBlockTypes[] = 'core/template';

		return $allowedBlockTypes;
	}

	/**
	 * Get the default list of blocks with the full block name attribute that are defined in the project.
	 *
	 * Used to limit what blocks are going to be used in your project using allowed_block_types_all filter.
	 * It is most commonly used directly as a callback for allowed_block_types_all filter.
	 * The first parameter doesn't have any influence on what the function returns.
	 *
	 * @hook allowed_block_types_all Available from WP 5.8.
	 *
	 * @param bool|string[] $allowedBlockTypes Doesn't have any influence on what function returns.
	 * @param WP_Block_Editor_Context $blockEditorContext The current block editor context.
	 *
	 * @return string[] The default list of blocks defined in the project.
	 */
	public function getAllBlocksList($allowedBlockTypes, WP_Block_Editor_Context $blockEditorContext)
	{
		return $this->getAllAllowedBlocksList([], $blockEditorContext);
	}

	/**
	 * Method used to register all custom blocks with data fetched from blocks manifest.json.
	 *
	 * @throws InvalidBlock Throws error if blocks are missing.
	 *
	 * @return void
	 */
	public function registerBlocks(): void
	{
		foreach (Components::getBlocks() as $block) {
			$this->registerBlock($block);
		}
	}

	/**
	 * Provides block registration callback method for rendering when using wrapper.
	 *
	 * @param array<string, mixed> $attributes Array of attributes as defined in block's manifest.json.
	 * @param string $innerBlockContent Block's content if using inner blocks.
	 *
	 * @throws InvalidBlock Throws error if block view is missing.
	 *
	 * @return string Html template for block.
	 */
	public function render(array $attributes, string $innerBlockContent): string
	{
		// Block details is unavailable in this method so we are fetching block name via attributes.
		$blockName = $attributes['blockName'] ?? '';

		// Get block view path.
		$sep = \DIRECTORY_SEPARATOR;
		$templatePath = Components::getProjectPaths('blocksDestinationCustom', "{$blockName}{$sep}{$blockName}.php");

		// Get block wrapper view path.
		if (Components::getConfigUseWrapper()) {
			$wrapperPath = Components::getProjectPaths('blocksDestinationWrapper', 'wrapper.php');

			// Check if wrapper component exists.
			if (!\file_exists($wrapperPath)) {
				throw InvalidPath::missingFileWithExampleException($wrapperPath, 'wrapper.php');
			}

			// Check if actual block exists.
			if (!\file_exists($templatePath)) {
				throw InvalidPath::missingFileWithExampleException($templatePath, "{$blockName}.php");
			}

			// If everything is ok, return the contents of the template (return, NOT echo).
			\ob_start();
			include $wrapperPath;
			$output = \ob_get_clean();
		} else {
			\ob_start();
			include $templatePath;
			$output = \ob_get_clean();
		}

		unset($blockName, $templatePath, $wrapperPath, $attributes, $innerBlockContent);

		return (string)$output;
	}

	/**
	 * Create custom category to assign all custom blocks.
	 *
	 * This category will be shown on all blocks list in "Add Block" button.
	 *
	 * @hook block_categories_all Available from WP 5.8.
	 *
	 * @param array<int, array<string, string|null>> $categories Array of categories for block types.
	 * @param WP_Block_Editor_Context $blockEditorContext The current block editor context.
	 *
	 * @return array<int, array<string, string|null>> Array of categories for block types.
	 */
	public function getCustomCategory(array $categories, WP_Block_Editor_Context $blockEditorContext): array
	{
		return \array_merge(
			$categories,
			[
				[
					'slug' => 'eightshift',
					'title' => \esc_html__('Eightshift', 'eightshift-libs'),
					'icon' => 'admin-settings',
				],
			]
		);
	}

	/**
	 * Locate and return template part with passed attributes for wrapper.
	 *
	 * Used to render php block wrapper view.
	 *
	 * @param string $src String with URL path to template.
	 * @param array<string, mixed> $attributes Attributes array to pass in template.
	 * @param string|null $innerBlockContent If using inner blocks content pass the data.
	 *
	 * @throws InvalidBlock Throws an error if wrapper file doesn't exist.
	 *
	 * @return void Includes an HTML view, or throws an error if the view is missing.
	 */
	public function renderWrapperView(string $src, array $attributes, ?string $innerBlockContent = null): void
	{
		if (!\file_exists($src)) {
			throw InvalidPath::missingFileWithExampleException($src, 'wrapper.php');
		}

		include $src;

		unset($src, $attributes, $innerBlockContent);
	}

	/**
	 * Removes paragraph block from the php part if the content is empty.
	 *
	 * Useful when setting the default paragraph block.
	 *
	 * @param array<string, mixed> $parsedBlock Array of block details.
	 * @param array<string, mixed> $sourceBlock Array of block source details.
	 *
	 * @return array<string, mixed>
	 */
	public function filterBlocksContent(array $parsedBlock, array $sourceBlock): array
	{
		$namespace = Components::getSettingsNamespace();

		if ($parsedBlock['blockName'] === "{$namespace}/paragraph") {
			$content = $parsedBlock['attrs']['paragraphParagraphContent'] ?? '';

			if (empty($content)) {
				$parsedBlock['attrs']['wrapperDisable'] = true;
				$parsedBlock['attrs']['paragraphParagraphUse'] = false;
			}
		}

		return $parsedBlock;
	}

	/**
	 * Render inline css variables in dom. Used with wp_footer hook.
	 *
	 * @return void
	 */
	public function outputCssVariablesInline(): void
	{
		echo Components::outputCssVariablesInline(); // phpcs:ignore
	}

	/**
	 * Method used to really register Gutenberg blocks.
	 *
	 * It uses native register_block_type() function from WP.
	 *
	 * @param array<string, mixed> $blockDetails Full Block Manifest details.
	 *
	 * @return void
	 */
	private function registerBlock(array $blockDetails): void
	{
		\register_block_type(
			$blockDetails['blockFullName'],
			[
				'render_callback' => [$this, 'render'],
				'attributes' => $this->getAttributes($blockDetails),
			]
		);
	}

	/**
	 * Get manifest data for the provided type.
	 *
	 * @param string $type Type of the manifest.
	 *
	 * @throws InvalidManifest If the manifest is missing or has an error.
	 *
	 * @return array<string, mixed>
	 */
	private function getManifest(string $type): array
	{
		$multiple = self::BLOCKS_BUILDER[$type]['multiple'] ?? false;

		return $multiple ? $this->getManifestItems($type) : $this->getManifestItem($type);
	}

	/**
	 * Get multiple items from the manifest.
	 *
	 * @param string $type Type of the manifest.
	 *
	 * @return array<array<string, mixed>>
	 */
	private function getManifestItems(string $type): array
	{
		$items = $this->manifestCache->getManifestCacheTopItem($type);

		$data = $items['data'] ?? [];

		foreach ($data as $path => $item) {
			$validation = self::BLOCKS_BUILDER[$type]['validation'] ?? [];

			if (!$validation) {
				continue;
			}

			foreach ($validation as $key) {
				if (!isset($item[$key])) {
					throw InvalidManifest::missingManifestKeyException($key, $path);
				}
			}
		}

		return array_values($data) ?? [];
	}

	/**
	 * Get single item from the manifest.
	 *
	 * @param string $type Type of the manifest.
	 *
	 * @return array<string, mixed>
	 */
	private function getManifestItem(string $type): array
	{
		$item = $this->manifestCache->getManifestCacheTopItem($type);

		$data = $item['data'] ?? [];
		$path = $item['path'] ?? [];

		if (!$data) {
			throw InvalidManifest::emptyOrErrorManifestException($path);
		}

		return $data;
	}

	/**
	 * Prepare all blocks attributes.
	 *
	 * This method combines default, block and wrapper attributes.
	 * Default attributes are hardcoded in this lib.
	 * Block attributes are provided by block manifest.json file.
	 * Also it is doing recursive loop for all children components and their attributes.
	 *
	 * @param array<string, mixed> $blockDetails Block Manifest details.
	 *
	 * @return array<string, mixed>
	 */
	private function getAttributes(array $blockDetails): array
	{
		$blockName = $blockDetails['blockName'];
		$blockClassPrefix = Components::getSettingsBlockClassPrefix();

		$wrapperAttributes = [];

		if (Components::getConfigUseWrapper()) {
			$wrapperAttributes = Components::getWrapperAttributes();
		}

		return \array_merge(
			[
				'blockName' => [
					'type' => 'string',
					'default' => $blockName,
				],
				// Used to pass reference to all components.
				'blockClientId' => [
					'type' => 'string',
				],
				'blockTopLevelId' => [
					'type' => 'string',
					'default' => Components::getUnique(),
				],
				'blockFullName' => [
					'type' => 'string',
					'default' => $blockDetails['blockFullName'],
				],
				'blockClass' => [
					'type' => 'string',
					'default' => "{$blockClassPrefix}-{$blockName}",
				],
				'blockJsClass' => [
					'type' => 'string',
					'default' => "js-{$blockClassPrefix}-{$blockName}",
				],
				'blockSsr' => [
					'type' => 'boolean',
					'default' => false,
				],
			],
			Components::getSettingsAttributes(),
			$wrapperAttributes,
			$this->prepareComponentAttributes($blockDetails)
		);
	}

	/**
	 * Iterate over attributes or example attributes array in block/component manifest and append the parent prefixes.
	 *
	 * @param array<string, mixed>   $manifest Array of component/block manifest to get data from.
	 * @param string  $newName New renamed component name.
	 * @param string  $realName Original real component name.
	 * @param string  $parent Parent component key with stacked parent component names for the final output.
	 * @param boolean $currentAttributes Check if current attribute is a part of the current component.
	 *
	 * @return  array<int|string, mixed>
	 */
	private function prepareComponentAttribute(array $manifest, string $newName, string $realName, string $parent = '', bool $currentAttributes = false): array
	{
		$output = [];

		// Define different data entry point for attributes or example.
		$componentAttributes = $manifest['attributes'] ?? [];

		// If the attributes or example key is missing in the manifest - bailout.
		if (!$componentAttributes) {
			return $output;
		}

		// Make sure the case is always correct for parent.
		$newParent = Components::kebabToCamelCase($parent);

		// Iterate each attribute and attach parent prefixes.
		$componentAttributeKeys = \array_keys($componentAttributes);
		foreach ($componentAttributeKeys as $componentAttribute) {
			$attribute = $componentAttribute;

			// If there is an attribute name switch, use the new one.
			if ($newName !== $realName) {
				$attribute = \str_replace($realName, $newName, (string) $componentAttribute);
			}

			// Check if current attribute is used strip component prefix from attribute and replace it with parent prefix.
			if ($currentAttributes) {
				$attribute = \str_replace(\lcfirst(Components::kebabToCamelCase($realName)), '', (string) $componentAttribute);
			}

			// Determine if parent is empty and if parent name is the same as component/block name and skip wrapper attributes.
			if (\substr((string)$attribute, 0, \strlen('wrapper')) === 'wrapper') {
				$attributeName = $attribute;
			} else {
				$attributeName = $newParent . \ucfirst((string)$attribute);
			}

			// Output new attribute names.
			$output[$attributeName] = $componentAttributes[$componentAttribute];
		}

		return $output;
	}

	/**
	 * Iterate over component array in block manifest and check if the component exists in the project.
	 * If components contains more component this function will run recursively.
	 *
	 * @param array<string, mixed>  $manifest Array of component/block manifest to get the data from.
	 * @param string $parent Parent component key with stacked parent component names for the final output.
	 *
	 * @throws InvalidBlock If the component is wrong, or the name is wrong or it doesn't exist.
	 *
	 * @return array<int|string, mixed>
	 */
	private function prepareComponentAttributes(array $manifest, string $parent = ''): array
	{
		$output = [];

		// Determine if this is component or block and provide the name, not used for anything important but only to output the error msg.
		$name = $manifest['blockName'] ?? $manifest['componentName'];

		$components = $manifest['components'] ?? [];

		$newParent = ($parent === '') ? $name : $parent;

		// Iterate over components key in manifest recursively and check component names.
		foreach ($components as $newComponentName => $realComponentName) {
			// Filter components real name.
			$component = Components::getComponent(Components::camelToKebabCase($realComponentName));

			// Bailout if component doesn't exist.
			if (!$component) {
				throw InvalidBlock::wrongComponentNameException($name, $realComponentName);
			}

			// If component has more components do recursive loop.
			if (isset($component['components'])) {
				$outputAttributes = $this->prepareComponentAttributes($component, $newParent . \ucfirst(Components::camelToKebabCase($newComponentName)));
			} else {
				// Output the component attributes if there is no nesting left, and append the parent prefixes.
				$outputAttributes = $this->prepareComponentAttribute($component, $newComponentName, $realComponentName, $newParent);
			}

			// Populate the output recursively.
			$output = \array_merge(
				$output,
				$outputAttributes
			);
		}

		return \array_merge(
			$output,
			$this->prepareComponentAttribute($manifest, '', $name, $newParent, true)
		);
	}
}
