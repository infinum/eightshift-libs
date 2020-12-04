<?php

/**
 * Class that registers WPCLI command for Custom Taxonomy.
 *
 * @package EightshiftLibs\CustomTaxonomy
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomTaxonomy;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class TaxonomyCli
 */
class TaxonomyCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src/CustomTaxonomy';

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
			'label' => $args[1] ?? 'Locations',
			'slug' => $args[2] ?? 'location',
			'rest_endpoint_slug' => $args[3] ?? 'locations',
			'post_type_slug' => $args[4] ?? 'post',
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
			'shortdesc' => 'Generates custom taxonomy class file.',
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
					'description' => 'The name of the custom taxonomy slug. Example: location.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'rest_endpoint_slug',
					'description' => 'The name of the custom taxonomy REST-API endpoint slug. Example: locations.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'post_type_slug',
					'description' => 'The position where to assign the new custom taxonomy. Example: post.',
					'optional' => false,
				],
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$label = $assocArgs['label'];
		$slug = $this->prepareSlug($assocArgs['slug']);
		$restEndpointSlug = $this->prepareSlug($assocArgs['rest_endpoint_slug']);
		$postTypeSlug = $this->prepareSlug($assocArgs['post_type_slug']);

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
			->searchReplaceString('example-endpoint-slug', $restEndpointSlug)
			->searchReplaceString("'post'", "'{$postTypeSlug}'")
			->searchReplaceString('Example Name', $label)
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
