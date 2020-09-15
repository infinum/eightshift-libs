<?php

/**
 * Class that registers WPCLI command for Rest Fields.
 *
 * @package EightshiftLibs\Rest\Fields
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest\Fields;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class FieldCli
 */
class FieldCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src/Rest/Fields';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'create_rest_field';
	}

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
			'field_name'  => $args[1] ?? 'title',
			'object_type' => $args[2] ?? 'post',
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
			'shortdesc' => 'Generates REST-API Field in your project.',
			'synopsis' => [
				[
					'type'        => 'assoc',
					'name'        => 'field_name',
					'description' => 'The name of the endpoint slug. Example: title.',
					'optional'    => false,
				],
				[
					'type'        => 'assoc',
					'name'        => 'object_type',
					'description' => 'Object(s) the field is being registered to. Example: post.',
					'optional'    => false,
				],
			],
		];
	}

	/**
	 * Generate the REST API field class
	 *
	 * @param array $args      Array of arguments form terminal.
	 * @param array $assocArgs Array of associative arguments form terminal.
	 */
	public function __invoke(array $args, array $assocArgs)
	{

		// Get Props.
		$fieldName  = $this->prepareSlug($assocArgs['field_name']);
		$objectType = $this->prepareSlug($assocArgs['object_type']);

		// Get full class name.
		$className = $this->getFileName($fieldName);
		$className = $this->getClassShortName() . $className;

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName());

		// Replace stuff in file.
		$class = $this->renameClassNameWithSuffix($this->getClassShortName(), $className, $class);
		$class = $this->renameNamespace($assocArgs, $class);
		$class = $this->renameUse($assocArgs, $class);
		$class = str_replace('example-post-type', $objectType, $class);
		$class = str_replace('example-field', $fieldName, $class);

		// Output final class to new file/folder and finish.
		$this->outputWrite(static::OUTPUT_DIR, $className, $class);
	}
}
