<?php

/**
 * Class that registers WPCLI command for Custom Taxonomy.
 *
 * @package EightshiftLibs\BlockPatterns
 */

declare(strict_types=1);

namespace EightshiftLibs\BlockPatterns;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Components;

/**
 * Class BlockPatternCli
 */
class BlockPatternCli extends AbstractCli
{
	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliBlocks::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'create_block_pattern';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'title' => 'example-title',
			'name' => 'example-name',
			'description' => 'example-description',
			'content' => 'example-content',
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
			'shortdesc' => 'Create block pattern service class.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'title',
					'description' => 'Pattern title',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'name',
					'description' => 'Pattern name with namespace. If not provided will be generated from title. Example: eightshift/pattern-name',
					'optional' => true,
					'default' => $this->getDefaultArg('name'),
				],
				[
					'type' => 'assoc',
					'name' => 'description',
					'description' => 'Description of the pattern.',
					'optional' => true,
					'default' => $this->getDefaultArg('description'),
				],
				[
					'type' => 'assoc',
					'name' => 'content',
					'description' => 'Content of the pattern. Needs to be the WP block markup (tho most likely you\'d add this manually after you generate the pattern)',
					'optional' => true,
					'default' => $this->getDefaultArg('content'),
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create service class to register custom block pattern.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --title='Button' --description='This is description' --capability='edit_posts' --menu_slug='es-content'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/BlockPatterns/BlockPatternExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		// Get Props.
		$title = $this->getArg($assocArgs, 'title');
		$name = $this->getArg($assocArgs, 'name');
		$content = $this->getArg($assocArgs, 'content');
		$description = $this->getArg($assocArgs, 'description');

		if (!$name) {
			$name = $this->generateName($title);
		}

		$className = $this->getFileName($title);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->searchReplaceString($this->getArgTemplate('title'), $title)
			->searchReplaceString($this->getArgTemplate('name'), $name)
			->searchReplaceString($this->getArgTemplate('content'), $content)
			->searchReplaceString($this->getArgTemplate('description'), $description)
			->outputWrite(Components::getProjectPaths('srcDestination', 'BlockPatterns'), "{$className}.php", $assocArgs);
	}

	/**
	 * Generated the name of the block pattern from title.
	 *
	 * @param string $title Title of the pattern.
	 * @return string
	 */
	private function generateName(string $title): string
	{
		return 'eightshift-boilerplate/' . Components::camelToKebabCase($title);
	}
}
