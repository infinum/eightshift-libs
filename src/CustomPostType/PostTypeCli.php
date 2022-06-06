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

/**
 * Class PostTypeCli
 */
class PostTypeCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'CustomPostType';

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
	 * Define default develop props.
	 *
	 * @param string[] $args WPCLI eval-file arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'label' => $args[1] ?? 'Product',
			'slug' => $args[2] ?? 'product',
			'rewrite_url' => $args[3] ?? 'product',
			'rest_endpoint_slug' => $args[4] ?? 'products',
			'capability' => $args[5] ?? 'post',
			'menu_position' => $args[6] ?? 40,
			'menu_icon' => $args[7] ?? 'admin-settings',
			'plural_label' => $args[8] ?? 'Products',
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
				],
				[
					'type' => 'assoc',
					'name' => 'menu_position',
					'description' => 'The default menu position for the custom post types. Example: 20.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'menu_icon',
					'description' => 'The default menu icon for the custom post types. Example: dashicons-analytics.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'plural_label',
					'description' => 'The plural label of the custom post type. Used for label generation. If not specified the plural will have appended s at the end of the label.', // phpcs:ignore Generic.Files.LineLength.TooLong
					'optional' => true,
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
		$label = $assocArgs['label'] ?? 'Custom Post Type';
		$slug = $this->prepareSlug($assocArgs['slug'] ?? 'custom-post-type');
		$rewriteUrl = $this->prepareSlug($assocArgs['rewrite_url'] ?? 'custom-post-type');
		$restEndpointSlug = $this->prepareSlug($assocArgs['rest_endpoint_slug'] ?? 'custom-post-type');
		$capability = $assocArgs['capability'] ?? '';
		$menuPosition = (string) ($assocArgs['menu_position'] ?? '');
		$menuIcon = $assocArgs['menu_icon'] ?? '';
		$pluralLabel = $assocArgs['plural_label'] ?? $label . 's';

		// Get full class name.
		$className = $this->getFileName($slug);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs)
			->searchReplaceString('example-slug', $slug)
			->searchReplaceString('example-url-slug', $rewriteUrl)
			->searchReplaceString('example-endpoint-slug', $restEndpointSlug)
			->searchReplaceString('Singular Name', $label)
			->searchReplaceString('singular name', \strtolower($label))
			->searchReplaceString('Plural Name', $pluralLabel)
			->searchReplaceString('plural name', \strtolower($pluralLabel));

		if (!empty($capability)) {
			$class->searchReplaceString("'post'", "'{$capability}'");
		}

		if (!empty($menuPosition)) {
			$class->searchReplaceString('20', $menuPosition);
		}

		if (!empty($menuIcon)) {
			$class->searchReplaceString('dashicons-analytics', $menuIcon);
		}

		// Output final class to new file/folder and finish.
		$class->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
