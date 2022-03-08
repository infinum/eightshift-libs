<?php

/**
 * Class that registers WPCLI command for Blocks Storybook.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;

/**
 * Class BlocksStorybookCli
 */
class BlocksStorybookCli extends AbstractCli
{
	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates Storybook config.',
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		if (function_exists('\add_action')) {
			$root = $this->getProjectRootPath();
			$rootNode = $this->getFrontendLibsBlockPath();

			$folder = "{$root}/.storybook";

			if (!file_exists($folder)) {
				mkdir($folder);
			}

			$this->copyRecursively("{$rootNode}/storybook/", "{$folder}/");

			\WP_CLI::success('Storybook config successfully set.');

			\WP_CLI::log('--------------------------------------------------');

			\WP_CLI::log((string)shell_exec('npm install @eightshift/storybook --save-dev --legacy-peer-deps')); // phpcs:ignore

			\WP_CLI::success('Storybook package successfully installed.');

			\WP_CLI::log('--------------------------------------------------');

			\WP_CLI::success('Storybook successfully set.');
			\WP_CLI::log('Please open you package.json and add this commands in your scripts:');
			\WP_CLI::log('"storybookBuild": "build-storybook -s public -o storybook"');
			\WP_CLI::log('"storybook": "start-storybook -s public"');

			\WP_CLI::log('--------------------------------------------------');

			\WP_CLI::success('To start storybook please run this command `npm run storybook`.');
		}
	}
}
