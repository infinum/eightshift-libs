<?php

/**
 * Class that registers WPCLI command for Custom Taxonomy.
 *
 * @package EightshiftLibs\CustomTaxonomy
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomTaxonomy;

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
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'label' => 'Location',
			'plural_label' => 'Locations',
			'slug' => 'location',
			'rest_endpoint_slug' => 'locations',
			'post_type_slug' => 'post',
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
					'name' => 'plural_label',
					'description' => 'The label of the custom taxonomy to show in WP admin but as a plural version.',
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

				Used to create custom taxonomy for all your custom data.

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
		$label = $this->getArg($assocArgs, 'label');
		$pluralLabel = $this->getArg($assocArgs, 'plural_label');
		$slug = $this->prepareSlug($this->getArg($assocArgs, 'slug'));
		$restEndpointSlug = $this->prepareSlug($this->getArg($assocArgs, 'rest_endpoint_slug'));
		$postTypeSlug = $this->prepareSlug($this->getArg($assocArgs, 'post_type_slug'));

		// Get full class name.
		$className = $this->getFileName($slug);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->renameTextDomain($assocArgs)
			->searchReplaceString($this->getArgTemplate('slug'), $slug)
			->searchReplaceString($this->getArgTemplate('rest_endpoint_slug'), $restEndpointSlug)
			->searchReplaceString($this->getArgTemplate('post_type_slug'), $postTypeSlug)
			->searchReplaceString($this->getArgTemplate('label'), $label)
			->searchReplaceString($this->getArgTemplate('label_lowercaps'), \strtolower($label))
			->searchReplaceString($this->getArgTemplate('plural_label'), $pluralLabel)
			->searchReplaceString($this->getArgTemplate('plural_label_lowecaps'), \strtolower($pluralLabel))
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
