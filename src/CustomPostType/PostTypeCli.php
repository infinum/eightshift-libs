<?php

/**
 * Class that registers WPCLI command for custom post type registration.
 *
 * @package EightshiftLibs\CustomPostType
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomPostType;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;

/**
 * Class PostTypeCli
 */
class PostTypeCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliCreate::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'post_type';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'label' => 'Product',
			'plural_label' => 'Products',
			'slug' => 'product',
			'rewrite_url' => 'product',
			'rest_endpoint_slug' => 'products',
			'capability' => 'post',
			'menu_position' => 20,
			'menu_icon' => 'admin-settings',
		];
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create custom post type service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'label',
					'description' => 'The label of the custom post type to show in WP admin.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'plural_label',
					'description' => 'The plural label of the custom post type. Used for label generation. If not specified the plural will have appended s at the end of the label.', // phpcs:ignore Generic.Files.LineLength.TooLong
					'optional' => false,
					'default' => $this->getDefaultArg('plural_label'),
				],
				[
					'type' => 'assoc',
					'name' => 'slug',
					'description' => 'The custom post type slug. Example: location.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'rewrite_url',
					'description' => 'The custom post type url. Example: location.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'rest_endpoint_slug',
					'description' => 'The name of the custom post type REST-API endpoint slug. Example: locations.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'capability',
					'description' => 'The default capability for the custom post types. Example: post.',
					'optional' => true,
					'default' => $this->getDefaultArg('capability'),
				],
				[
					'type' => 'assoc',
					'name' => 'menu_position',
					'description' => 'The default menu position for the custom post types. Example: 20.',
					'optional' => true,
					'default' => $this->getDefaultArg('menu_position'),
				],
				[
					'type' => 'assoc',
					'name' => 'menu_icon',
					'description' => 'The default menu icon for the custom post types. Example: dashicons-analytics.',
					'optional' => true,
					'default' => $this->getDefaultArg('menu_icon'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create custom post type for all your custom data.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --label='Jobs' --slug='jobs' --rewrite_url='jobs' --rest_endpoint_slug='jobs'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/CustomPostType/PostTypeExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$label = $this->getArg($assocArgs, 'label');
		$slug = $this->prepareSlug($this->getArg($assocArgs, 'slug'));
		$rewriteUrl = $this->prepareSlug($this->getArg($assocArgs, 'rewrite_url'));
		$restEndpointSlug = $this->prepareSlug($this->getArg($assocArgs, 'rest_endpoint_slug'));
		$capability = $this->getArg($assocArgs, 'capability');
		$menuPosition = $this->getArg($assocArgs, 'menu_position');
		$menuIcon = $this->getArg($assocArgs, 'menu_icon');
		$pluralLabel = $this->getArg($assocArgs, 'plural_label');

		// Get full class name.
		$className = $this->getFileName($slug);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs)
			->searchReplaceString($this->getArgTemplate('slug'), $slug)
			->searchReplaceString($this->getArgTemplate('rewrite_url'), $rewriteUrl)
			->searchReplaceString($this->getArgTemplate('rest_endpoint_slug'), $restEndpointSlug)
			->searchReplaceString($this->getArgTemplate('label'), $label)
			->searchReplaceString($this->getArgTemplate('label_lowercaps'), \strtolower($label))
			->searchReplaceString($this->getArgTemplate('plural_label'), $pluralLabel)
			->searchReplaceString($this->getArgTemplate('plural_label_lowecaps'), \strtolower($pluralLabel));

		if (!empty($capability)) {
			$class->searchReplaceString($this->getArgTemplate('capability'), $capability);
		}

		if (!empty($menuPosition)) {
			$class->searchReplaceString($this->getDefaultArg('menu_position'), $menuPosition);
		}

		if (!empty($menuIcon)) {
			$class->searchReplaceString($this->getArgTemplate('menu_icon'), $menuIcon);
		}

		// Output final class to new file/folder and finish.
		$class->outputWrite(Components::getProjectPaths('srcDestination', 'CustomPostType'), "{$className}.php", $assocArgs);
	}
}
