<?php

/**
 * Class that registers WPCLI command for Custom Taxonomy.
 *
 * @package EightshiftLibs\BlockPatterns
 */

declare(strict_types=1);

namespace EightshiftLibs\BlockPatterns;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;

/**
 * Class BlockPatternCli
 */
class BlockPatternCli extends AbstractCli
{
	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'BlockPatterns';

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
		return 'block_pattern';
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
			'title' => $args[1] ?? 'Something',
			'name' => $args[2] ?? 'eightshift-boilerplate/something',
			'description' => $args[3] ?? 'This is an example block pattern',
			'content' => $args[4] ?? '',
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
				],
				[
					'type' => 'assoc',
					'name' => 'description',
					'description' => 'Description of the pattern.',
					'optional' => true,
					'default' => '',
				],
				[
					'type' => 'assoc',
					'name' => 'content',
					'description' => 'Content of the pattern. Needs to be the WP block markup (tho most likely you\'d add this manually after you generate the pattern)',
					'optional' => true,
					'default' => 'Description of this pattern',
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create service class to register custom block pattern.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --title='Call to action' --menu_title='content' --capability='edit_posts' --menu_slug='es-content'

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
		$title = $assocArgs['title'] ?? '';
		$name = isset($assocArgs['name']) ? $assocArgs['name'] : $this->generateName($title);
		$content = $assocArgs['content'] ?? '';
		$description = isset($assocArgs['description']) ? $assocArgs['description'] : 'Description of this pattern';

		$className = $this->getFileName($title);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->searchReplaceString('example-name', $name)
			->searchReplaceString('example-title', $title)
			->searchReplaceString('example-description', $description)
			->searchReplaceString('example-content', $content);

		// Output final class to new file/folder and finish.
		$class->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
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
