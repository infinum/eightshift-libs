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
		'copyright',
		'drawer',
		'footer',
		'hamburger',
		'head',
		'header',
		'heading',
		'image',
		'layout-three-colums',
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
		$class = $this->getExampleTemplate(__DIR__, $className);

		// Replace stuff in file.
		$class = $this->renameClassName($className, $class);
		$class = $this->renameNamespace($assocArgs, $class);
		$class = $this->renameUse($assocArgs, $class);

		// Output final class to new file/folder and finish.
		$this->outputWrite(static::OUTPUT_DIR, $className, $class);

		if (function_exists('\add_action')) {
			$this->blocksInit();
		}
	}

	/**
	 * Copy blocks from Eightshift-frontend-libs to project
	 *
	 * @param bool $all Copy all from Eightshift-frontend-libs to project or selective from the list.
	 *
	 * @return void
	 */
	public function blocksInit(bool $all = false): void
	{
		$root = $this->getProjectRootPath();
		$rootNode = $this->getFrontendLibsBlockPath();

		system("cp -R {$rootNode}/assets {$root}/assets");
		system("cp -R {$rootNode}/storybook {$root}/.storybook");

		if ($all) {
			system("cp -R {$rootNode}/src/Blocks {$root}/src/Blocks");
		} else {
			system("cp -R {$rootNode}/src/Blocks/assets {$root}/src/Blocks/assets/");
			system("cp -R {$rootNode}/src/Blocks/variations {$root}/src/Blocks/variations/");
			system("cp -R {$rootNode}/src/blocks/wrapper {$root}/src/Blocks/wrapper/");
			system("cp -R {$rootNode}/src/Blocks/manifest.json {$root}/src/Blocks/");

			foreach (static::COMPONENTS as $component) {
				system("mkdir -p {$root}/src/Blocks/components/{$component}/");
				system(
					"cp -R {$rootNode}/src/Blocks/components/{$component}/. {$root}/src/Blocks/components/{$component}/"
				);
			}

			foreach (static::BLOCKS as $block) {
				system("mkdir -p {$root}/src/Blocks/custom/{$block}/");
				system("cp -R {$rootNode}/src/Blocks/custom/{$block}/. {$root}/src/Blocks/custom/{$block}/");
			}
		}

		\WP_CLI::success('Blocks successfully set.');
	}
}
