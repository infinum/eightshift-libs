<?php

/**
 * Class that registers WPCLI command for Custom Taxonomy.
 *
 * @package EightshiftLibs\CustomPostType
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomPostType;

use EightshiftLibs\Cli\AbstractCli;

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
	public const OUTPUT_DIR = 'src/CustomPostType';

	/**
	 * Define default develop props.
	 *
	 * @param array $args WPCLI eval-file arguments.
	 *
	 * @return array
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'label' => $args[1] ?? 'Products',
			'slug' => $args[2] ?? 'product',
			'rewrite_url' => $args[3] ?? 'product',
			'rest_endpoint_slug' => $args[4] ?? 'products',
			'capability' => $args[5] ?? 'post',
			'menu_position' => $args[6] ?? 40,
			'menu_icon' => $args[7] ?? 'admin-settings',
		];
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates custom post type class file.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'label',
					'description' => 'The label of the custom taxonomy to show in WP admin.',
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
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$label = $assocArgs['label'];
		$slug = $this->prepareSlug($assocArgs['slug']);
		$rewriteUrl = $this->prepareSlug($assocArgs['rewrite_url']);
		$restEndpointSlug = $this->prepareSlug($assocArgs['rest_endpoint_slug']);
		$capability = $assocArgs['capability'] ?? '';
		$menuPosition = (string) $assocArgs['menu_position'] ?? '';
		$menuIcon = $assocArgs['menu_icon'] ?? '';

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
			->searchReplaceString('Example Name', $label);

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
