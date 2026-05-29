<?php

/**
 * Class Blocks is the base class for Gutenberg blocks registration.
 * It provides the ability to register custom blocks using manifest.json.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Services\ServiceInterface;
use WP_Block_Editor_Context;
use WP_Block;

/**
 * Class Blocks
 */
abstract class AbstractBlocks implements ServiceInterface, RenderableBlockInterface
{
	/**
	 * Memoized camel-to-kebab conversions for component names.
	 *
	 * @var array<string, string>
	 */
	private static array $camelToKebabMemo = [];

	/**
	 * Memoized kebab-to-camel conversions for parent prefixes.
	 *
	 * @var array<string, string>
	 */
	private static array $kebabToCamelMemo = [];

	/**
	 * Create custom project color palette.
	 * These colors are fetched from the main settings manifest.json.
	 */
	public function changeEditorColorPalette(): void
	{
		if ($colors = Helpers::getSettingsGlobalVariablesColors()) {
			\add_theme_support('editor-color-palette', $colors);
		}
	}

	/**
	 * Register multiple theme support options.
	 */
	public function addThemeSupport(): void
	{
		\add_theme_support('align-wide');
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
		if (\is_bool($allowedBlockTypes)) {
			return $allowedBlockTypes;
		}

		static $projectBlockNames = null;

		if ($projectBlockNames === null) {
			$blocks = Helpers::getBlocks();
			$projectBlockNames = $blocks !== []
				? \array_values(\array_map(static fn(array $block): mixed => $block['blockFullName'], $blocks))
				: [];
		}

		if ($projectBlockNames) {
			$allowedBlockTypes = \array_values(\array_merge($projectBlockNames, $allowedBlockTypes));
		}

		// Allow reusable block.
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
	 */
	public function registerBlocks(): void
	{
		$settings = Helpers::getSettings();
		$context = [
			'blockClassPrefix' => $settings['blockClassPrefix'] ?? 'block',
			'settingsAttributes' => $settings['attributes'] ?? [],
			'wrapperAttributes' => Helpers::getConfigUseWrapper() ? (Helpers::getWrapper()['attributes'] ?? []) : [],
		];

		foreach (Helpers::getBlocks() as $block) {
			$this->registerBlock($block, $context);
		}
	}

	/**
	 * Provides block registration callback method for rendering when using wrapper.
	 *
	 * @param array<string, mixed> $attributes Array of attributes as defined in block's manifest.json.
	 * @param string $innerBlockContent Block's content if using inner blocks.
	 * @param WP_Block|null $block The current WP_Block instance, available as $block in the template.
	 *
	 * @return string Html template for block.
	 */
	public function render(array $attributes, string $innerBlockContent, ?WP_Block $block = null): string
	{
		// Get block view path.
		$blockOutput = Helpers::render(
			$attributes['blockName'] ?? '',
			$attributes,
			'blocks',
			false,
			'',
			$innerBlockContent,
			$block
		);

		// Get block wrapper view path.
		if (Helpers::getConfigUseWrapper()) {
			return Helpers::render(
				'wrapper',
				$attributes,
				'wrapper',
				false,
				'',
				$blockOutput,
			);
		}

		return $blockOutput;
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
	 */
	public function outputCssVariablesInline(): void
	{
		echo Helpers::outputCssVariablesInline(); // phpcs:ignore
	}

	/**
	 * Render global css variables in dom. Used with wp_head hook.
	 */
	public function outputCssVariablesGlobal(): void
	{
		echo Helpers::outputCssVariablesGlobal(); // phpcs:ignore
	}

	/**
	 * Method used to really register Gutenberg blocks.
	 *
	 * It uses native register_block_type() function from WP.
	 *
	 * @param array<string, mixed> $blockDetails Full Block Manifest details.
	 * @param array<string, mixed> $context Shared registration context (blockClassPrefix, settingsAttributes, wrapperAttributes).
	 */
	private function registerBlock(array $blockDetails, array $context): void
	{
		if (($blockDetails['active'] ?? true) === false) {
			return;
		}

		\register_block_type(
			$blockDetails['blockFullName'],
			[
				'render_callback' => $this->render(...),
				'attributes' => $this->getAttributes($blockDetails, $context),
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
	 * @param array<string, mixed> $context Shared registration context (blockClassPrefix, settingsAttributes, wrapperAttributes).
	 *
	 * @return array<string, mixed>
	 */
	private function getAttributes(array $blockDetails, array $context): array
	{
		$blockName = $blockDetails['blockName'];
		$blockClassPrefix = $context['blockClassPrefix'];

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
			$context['settingsAttributes'],
			$context['wrapperAttributes'],
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
		$newParent = self::$kebabToCamelMemo[$parent] ??= Helpers::kebabToCamelCase($parent);

		// Precompute the prefix that gets stripped when currentAttributes is true.
		$currentPrefix = $currentAttributes
			? \lcfirst(self::$kebabToCamelMemo[$realName] ??= Helpers::kebabToCamelCase($realName))
			: '';

		// Iterate each attribute and attach parent prefixes.
		foreach ($componentAttributes as $componentAttribute => $attributeValue) {
			$attribute = (string) $componentAttribute;

			// If there is an attribute name switch, use the new one.
			if ($newName !== $realName) {
				$attribute = \str_replace($realName, $newName, $attribute);
			}

			// Check if current attribute is used strip component prefix from attribute and replace it with parent prefix.
			if ($currentAttributes) {
				$attribute = \str_replace($currentPrefix, '', (string) $componentAttribute);
			}

			// Determine if parent is empty and if parent name is the same as component/block name and skip wrapper attributes.
			$attributeName = \str_starts_with($attribute, 'wrapper') ? $attribute : $newParent . \ucfirst($attribute);

			// Output new attribute names.
			$output[$attributeName] = $attributeValue;
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
		// Determine if this is component or block and provide the name, not used for anything important but only to output the error msg.
		$name = $manifest['blockName'] ?? '';

		if (Helpers::getConfigUseLegacyComponents()) {
			$name = $manifest['blockName'] ?? $manifest['componentName'];
		}

		$components = $manifest['components'] ?? [];

		$newParent = ($parent === '') ? $name : $parent;

		$collected = [];

		// Iterate over components key in manifest recursively and check component names.
		foreach ($components as $newComponentName => $realComponentName) {
			// Filter components real name.
			$realKebab = self::$camelToKebabMemo[$realComponentName] ??= Helpers::camelToKebabCase($realComponentName);
			$component = Helpers::getComponent($realKebab);

			// Bailout if component doesn't exist.
			if ($component === []) {
				throw InvalidBlock::wrongComponentNameException($name, $realComponentName);
			}

			// If component has more components do recursive loop.
			if (isset($component['components'])) {
				$newKebab = self::$camelToKebabMemo[$newComponentName] ??= Helpers::camelToKebabCase($newComponentName);
				$collected[] = $this->prepareComponentAttributes($component, $newParent . \ucfirst($newKebab));
			} else {
				// Output the component attributes if there is no nesting left, and append the parent prefixes.
				$collected[] = $this->prepareComponentAttribute($component, $newComponentName, $realComponentName, $newParent);
			}
		}

		$collected[] = $this->prepareComponentAttribute($manifest, '', $name, $newParent, true);

		return \array_merge(...$collected);
	}
}
