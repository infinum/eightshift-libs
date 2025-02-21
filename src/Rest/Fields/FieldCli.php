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
use EightshiftLibs\Helpers\Helpers;

/**
 * Class FieldCli
 */
class FieldCli extends AbstractCli
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
		return 'rest-field';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'field_name' => 'title-custom',
			'object_type' => 'example',
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
					'default' => $this->getDefaultArg('object_type'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create REST-API service class to register custom field.

				## EXAMPLES

				# Create service class:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --field_name='title'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Rest/Fields/FieldExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		// Get Props.
		$fieldName = $this->getArg($assocArgs, 'field_name');
		$objectType = $this->prepareSlug($this->getArg($assocArgs, 'object_type'));

		// Get full class name.
		$className = $this->getFileName($fieldName);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameGlobals($assocArgs)
			->searchReplaceString($this->getArgTemplate('object_type'), $objectType)
			->searchReplaceString($this->getArgTemplate('field_name'), $fieldName)
			->outputWrite(Helpers::getProjectPaths('src', ['Rest', 'Fields']), "{$className}.php", $assocArgs);
	}
}
