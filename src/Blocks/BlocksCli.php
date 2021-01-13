<?php

/**
 * Class that registers WPCLI command for Blocks.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class BlocksCli
 */
class BlocksCli extends AbstractCli
{

	/**
	 * Output dir relative path
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src/Blocks';

	/**
	 * List of components
	 *
	 * @var array
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
		'image',
		'layout-three-columns',
		'link',
		'lists',
		'logo',
		'menu',
		'page-overlay',
		'paragraph',
	];

	/**
	 * List of blocks
	 *
	 * @var array
	 */
	public const BLOCKS = [
		'button',
		'card',
		'column',
		'columns',
		'group',
		'heading',
		'image',
		'link',
		'lists',
		'paragraph',
	];

	/**
	 * Get WPCLI command doc
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates Blocks class.',
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		$className = $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		$class = $this->getExampleTemplate(__DIR__, $className)
			->renameClassName($className)
			->renameNamespace($assocArgs)
			->renameTextDomainFrontendLibs($assocArgs)
			->renameUse($assocArgs);

		if (function_exists('\add_action')) {
			$this->blocksInit($assocArgs);
		}

		// Output final class to new file/folder and finish.
		$class->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}

	/**
	 * Copy blocks from Eightshift-frontend-libs to project
	 *
	 * @param array $args Arguments array.
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
			if (!file_exists($folder)) {
				system("mkdir -p {$folder}");
			}
		}

		system("cp -R {$rootNode}/assets/. {$folders['assetsGlobal']}/");

		system("cp -R {$rootNode}/src/Blocks/assets/. {$folders['assets']}/");
		system("cp -R {$rootNode}/src/Blocks/variations/. {$folders['variations']}/");
		system("cp -R {$rootNode}/src/Blocks/manifest.json {$folders['blocks']}/");

		\WP_CLI::runcommand("{$this->commandParentName} use_wrapper {$this->prepareArgsManual($args)}");

		foreach (static::COMPONENTS as $component) {
			\WP_CLI::runcommand("{$this->commandParentName} use_component --name='{$component}' {$this->prepareArgsManual($args)}");
		}

		foreach (static::BLOCKS as $block) {
			\WP_CLI::runcommand("{$this->commandParentName} use_block --name='{$block}' {$this->prepareArgsManual($args)}");
		}

		\WP_CLI::success('Blocks successfully set.');
	}
}
