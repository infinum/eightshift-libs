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
use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Services\ServiceInterface;
use WP_Block_Editor_Context;
use WP_Post;

/**
 * Class Blocks
 */
abstract class AbstractBlocks implements ServiceInterface, RenderableBlockInterface
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
	 * Create custom project color palette.
	 * These colors are fetched from the main settings manifest.json.
	 *
	 * @return void
	 */
	public function changeEditorColorPalette(): void
	{
		// Unable to use state due to this method is used in JS and store is not registered there.
		$colors = $this->manifestCache->getManifestCacheTopItem(AbstractManifestCache::SETTINGS_KEY)['globalVariables']['colors'] ?? [];

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
		// Register store and set all the data.
		Helpers::setStore();
		Helpers::setSettings($this->manifestCache->getManifestCacheTopItem(AbstractManifestCache::SETTINGS_KEY));
		Helpers::setConfigFlags();

		if (Helpers::getConfigUseBlocks()) {
			Helpers::setBlocks($this->manifestCache->getManifestCacheTopItem(AbstractManifestCache::BLOCKS_KEY));
		}

		if (Helpers::getConfigUseComponents()) {
			Helpers::setComponents($this->manifestCache->getManifestCacheTopItem(AbstractManifestCache::COMPONENTS_KEY));
		}

		if (Helpers::getConfigUseVariations()) {
			Helpers::setVariations($this->manifestCache->getManifestCacheTopItem(AbstractManifestCache::VARIATIONS_KEY));
		}

		if (Helpers::getConfigUseWrapper()) {
			Helpers::setWrapper($this->manifestCache->getManifestCacheTopItem(AbstractManifestCache::WRAPPER_KEY));
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

		if (Helpers::getConfigUseBlocks()) {
			$allowedBlockTypes = \array_values(\array_merge(
				\array_map(
					fn ($block) => $block['blockFullName'],
					$this->manifestCache->getManifestCacheTopItem(AbstractManifestCache::BLOCKS_KEY)
				),
				$allowedBlockTypes,
			));
		}

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
	 * @return void
	 */
	public function registerBlocks(): void
	{
		if (!Helpers::getConfigUseBlocks()) {
			return;
		}

		foreach ($this->manifestCache->getManifestCacheTopItem(AbstractManifestCache::BLOCKS_KEY) as $block) {
			$this->registerBlock($block);
		}
	}

	/**
	 * Provides block registration callback method for rendering when using wrapper.
	 *
	 * @param array<string, mixed> $attributes Array of attributes as defined in block's manifest.json.
	 * @param string $innerBlockContent Block's content if using inner blocks.
	 *
	 * @return string Html template for block.
	 */
	public function render(array $attributes, string $innerBlockContent): string
	{
		// Get block view path.
		$blockOutpout = Helpers::render(
			$attributes['blockName'] ?? '',
			$attributes,
			'blocks',
			false,
			'',
			$innerBlockContent
		);

		// Get block wrapper view path.
		if (Helpers::getConfigUseWrapper()) {
			return Helpers::render(
				'wrapper',
				$attributes,
				'wrapper',
				false,
				'',
				$blockOutpout
			);
		}

		return $blockOutpout;
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
		$namespace = Helpers::getSettingsNamespace();

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
		echo Helpers::outputCssVariablesInline(); // phpcs:ignore
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
		$blockClassPrefix = Helpers::getSettingsBlockClassPrefix();

		$wrapperAttributes = [];

		if (Helpers::getConfigUseWrapper()) {
			$wrapperAttributes = Helpers::getWrapperAttributes();
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
					'default' => Helpers::getUnique(),
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
			Helpers::getSettingsAttributes(),
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
		$newParent = Helpers::kebabToCamelCase($parent);

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
				$attribute = \str_replace(\lcfirst(Helpers::kebabToCamelCase($realName)), '', (string) $componentAttribute);
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
			$component = Helpers::getComponent(Helpers::camelToKebabCase($realComponentName));

			// Bailout if component doesn't exist.
			if (!$component) {
				throw InvalidBlock::wrongComponentNameException($name, $realComponentName);
			}

			// If component has more components do recursive loop.
			if (isset($component['components'])) {
				$outputAttributes = $this->prepareComponentAttributes($component, $newParent . \ucfirst(Helpers::camelToKebabCase($newComponentName)));
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
