<?php

// phpcs:ignoreFile Generic.Files.LineLength.TooLong

/**
 * Class that registers WPCLI command for Custom Taxonomy.
 *
 * @package EightshiftLibs\CustomTaxonomy
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomTaxonomy;

use EightshiftBoilerplate\CustomTaxonomy\TaxonomyExample;
use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;

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
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'CustomTaxonomy';

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
		return 'taxonomy';
	}

	/**
	 * Define default develop props.
	 *
	 * @param string[] $args WPCLI eval-file arguments.
	 *
	 * @return array<string, mixed>
	 */
	public function getDevelopArgs(array $args): array
	{
		return [
			'label' => $args[1] ?? 'Location',
			'slug' => $args[2] ?? 'location',
			'rest_endpoint_slug' => $args[3] ?? 'locations',
			'post_type_slug' => $args[4] ?? 'post',
			'plural_label' => $args[5] ?? 'Locations',
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
			'shortdesc' => 'Create custom taxonomy service class.',
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
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create custom taxonomy all your custom data.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --label='Job Positions' --slug='job-position' --rest_endpoint_slug='job-positions' --post_type_slug='user'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/CustomTaxonomy/TaxonomyExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$label = $assocArgs['label'] ?? 'Custom Taxonomy';
		$slug = $this->prepareSlug($assocArgs['slug'] ?? TaxonomyExample::TAXONOMY_SLUG);
		$restEndpointSlug = $this->prepareSlug($assocArgs['rest_endpoint_slug'] ?? TaxonomyExample::REST_API_ENDPOINT_SLUG);
		$postTypeSlug = $this->prepareSlug($assocArgs['post_type_slug'] ?? TaxonomyExample::TAXONOMY_POST_TYPE_SLUG);
		$pluralLabel = $assocArgs['plural_label'] ?? $label . 's';

		// Get full class name.
		$className = $this->getFileName($slug);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs)
			->searchReplaceString('example-slug', $slug)
			->searchReplaceString('example-endpoint-slug', $restEndpointSlug)
			->searchReplaceString("'post'", "'{$postTypeSlug}'")
			->searchReplaceString('Blog_Taxonomy', $className)
			->searchReplaceString('Singular Name', $label)
			->searchReplaceString('singular name', \strtolower($label))
			->searchReplaceString('Plural Name', $pluralLabel)
			->searchReplaceString('plural name', \strtolower($pluralLabel))
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
