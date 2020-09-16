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
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = 'src/Blocks';

	public const COMPONENTS = [
		'button',
		'heading',
		'image',
		'link',
		'lists',
		'paragraph',
		'tracking',
		'video',
		'header',
		'footer',
		'logo',
		'drawer',
		'menu',
		'hamburger',
		'copyright',
		'page-overlay',
	];

	public const BLOCKS = [
		'button',
		'heading',
		'image',
		'link',
		'lists',
		'paragraph',
		'video',
		'example',
	];

	/**
	 * Get WPCLI command doc.
	 *
	 * @return string
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

		if (function_exists('add_action')) {
			$this->blocksInit();
		}
	}

	/**
	 * Copy blocks from Eightshift-frontend-libs to project.
	 *
	 * @param bool $all Copy all from Eightshift-frontend-libs to project or selective from the list.
	 *
	 * @return void
	 */
	public function blocksInit(bool $all = false): void
	{
		$root     = $this->getProjectRootPath();
		$rootNode = $this->getFrontendLibsBlockPath();

		system("cp -R {$rootNode}/assets {$root}/assets");
		system("cp -R {$rootNode}/storybook {$root}/.storybook");

		if ($all) {
			system("cp -R {$rootNode}/src/Blocks {$root}/src/Blocks");
		} else {
			system("cp -R {$rootNode}/src/Blocks/Assets {$root}/src/Blocks/Assets/");
			system("cp -R {$rootNode}/src/Blocks/Variations {$root}/src/Blocks/Variations/");
			system("cp -R {$rootNode}/src/blocks/Wrapper {$root}/src/Blocks/Wrapper/");
			system("cp -R {$rootNode}/src/Blocks/manifest.json {$root}/src/Blocks/");

			foreach (static::COMPONENTS as $component) {
				system("mkdir -p {$root}/src/Blocks/Components/{$component}/");
				system("cp -R {$rootNode}/src/Blocks/Components/{$component}/. {$root}/src/Blocks/Components/{$component}/");
			}

			foreach (static::BLOCKS as $block) {
				system("mkdir -p {$root}/src/Blocks/Custom/{$block}/");
				system("cp -R {$rootNode}/src/Blocks/Custom/{$block}/. {$root}/src/Blocks/Custom/{$block}/");
			}
		}

		\WP_CLI::success('Blocks successfully set.');
	}
}
