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
	 *
	 * @var string
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
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates REST-API Field in your project.',
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
					'optional' => false,
				],
			],
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$fieldName = $this->prepareSlug($assocArgs['field_name']);
		$objectType = $this->prepareSlug($assocArgs['object_type']);

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
