<?php

/**
 * Class that registers WPCLI command for Rest Fields.
 *
 * @package EightshiftLibs\Rest\Fields
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest\Fields;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use WP_CLI;

/**
 * Class FieldCli
 */
class FieldCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'Rest' . \DIRECTORY_SEPARATOR . 'Fields';

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
		return 'rest_field';
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
			'field_name' => $args[1] ?? 'title',
			'object_type' => $args[2] ?? 'post',
		];
	}

	/**
	 * Get WP CLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create REST-API field service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'field_name',
					'description' => 'The name of the endpoint slug. Example: title.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'object_type',
					'description' => 'Object(s) the field is being registered to. Example: post.',
					'optional' => true,
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create REST-API service class to register custom field.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --field_name='title'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Rest/Fields/FieldExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$fieldName = $this->prepareSlug($assocArgs['field_name'] ?? 'title');

		// If field name is empty throw error.
		if (empty($fieldName)) {
			WP_CLI::error("Empty slug provided, please set the slug using --endpoint_slug=\"slug-name\"");
		}

		$objectType = $this->prepareSlug($assocArgs['object_type'] ?? 'post');

		// Get full class name.
		$className = $this->getFileName($fieldName);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->searchReplaceString('example-post-type', $objectType)
			->searchReplaceString('example-field', $fieldName)
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
