<?php

/**
 * Class that registers WPCLI command for Blocks.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI;

/**
 * Class BlocksCli
 */
class BlocksCli extends AbstractCli
{
	/**
	 * Toggle to see if this is running inside tests or not
	 *
	 * @var bool
	 */
	private $isTest;

	/**
	 * Output dir relative path
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src' . \DIRECTORY_SEPARATOR . 'Blocks';

	/**
	 * List of components only used in the project init.
	 * All components are read from the disc path.
	 *
	 * @var string[]
	 */
	public const COMPONENTS = [
		'button',
		'card',
		'copyright',
		'drawer',
		'footer',
		'hamburger',
		'head',
		'header',
		'heading',
		'icon',
		'image',
		'layout-three-columns',
		'lists',
		'logo',
		'menu',
		'paragraph',
		'tracking-before-body-end',
		'tracking-head',
	];

	/**
	 * List of blocks only used in the project init.
	 * All blocks are read from the disc path.
	 *
	 * @var string[]
	 */
	public const BLOCKS = [
		'button',
		'card',
		'group',
		'heading',
		'image',
		'lists',
		'paragraph',
	];

	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return 'create';
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'blocks';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates Blocks class.',
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameNamespace($assocArgs)
			->renameTextDomainFrontendLibs($assocArgs)
			->renameUse($assocArgs);

		if (! \defined('ES_DEVELOP_MODE')) {
			if (!$this->isTest && \function_exists('\add_action')) {
				$this->blocksInit($assocArgs);
			}
		}

		// Output final class to new file/folder and finish.
		$class->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}

	/**
	 * Copy blocks from Eightshift-frontend-libs to project
	 *
	 * @param string[] $args Arguments array.
	 *
	 * @return void
	 */
	private function blocksInit(array $args): void
	{
		$root = $this->getProjectRootPath();
		$rootNode = $this->getFrontendLibsBlockPath();

		$folders = [
			'assetsGlobal' => "{$root}/assets",
			'blocks' => "{$root}/src/Blocks",
			'assets' => "{$root}/src/Blocks/assets",
			'components' => "{$root}/src/Blocks/components",
			'custom' => "{$root}/src/Blocks/custom",
			'variations' => "{$root}/src/Blocks/variations",
		];

		foreach ($folders as $folder) {
			if (!\file_exists($folder)) {
				\mkdir($folder);
			}
		}

		$this->copyRecursively("{$rootNode}/assets/", "{$folders['assetsGlobal']}/");
		$this->copyRecursively("{$rootNode}/src/Blocks/assets/", "{$folders['assets']}/");
		$this->copyRecursively("{$rootNode}/src/Blocks/variations/", "{$folders['variations']}/");
		\copy("{$rootNode}/src/Blocks/manifest.json", "{$folders['blocks']}/manifest.json");

		WP_CLI::runcommand("{$this->commandParentName} use_wrapper {$this->prepareArgsManual($args)}");

		foreach (static::COMPONENTS as $component) {
			WP_CLI::runcommand("{$this->commandParentName} use_component --name={$component} {$this->prepareArgsManual($args)}");
		}

		foreach (static::BLOCKS as $block) {
			WP_CLI::runcommand("{$this->commandParentName} use_block --name={$block} {$this->prepareArgsManual($args)}");
		}

		WP_CLI::success('Blocks successfully set.');
	}

	/**
	 * Used when running tests.
	 *
	 * @return void
	 */
	public function setTest(): void
	{
		$this->isTest = true;
	}
}
