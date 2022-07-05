<?php

/**
 * Class that hold abstractions for for Blocks CLI
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Helpers\Components;
use WP_CLI;

/**
 * Abstract class used for Blocks and Components
 */
abstract class AbstractBlocksCli extends AbstractCli
{
	/**
	 * Move items for the block editor to project folder.
	 *
	 * @param array<string, mixed> $args Array of arguments from WP-CLI command.
	 * @param string $source Source path.
	 * @param string $destination Destination path.
	 * @param string $type Type of items used for output log.
	 * @param bool $isSingleFolder Is single folder item.
	 *
	 * @return void
	 */
	protected function moveItems(array $args, string $source, string $destination, string $type, bool $isSingleFolder = false): void
	{
		$sep = \DIRECTORY_SEPARATOR;

		// Get Props.
		$skipExisting = $this->getSkipExisting($args);
		$groupOutput = $args['groupOutput'] ?? false;

		// Clean up name.
		$name = $args['name'] ?? '';
		$name = str_replace(' ', '', $name);
		$name = \trim($name, \DIRECTORY_SEPARATOR);

		$isFile = \strpos($name, '.') !== false;

		$itemsList = [$name];

		if (\strpos($name, ',') !== false || \strpos($name, ', ') !== false) {
			$itemsList = explode(',', $name);
		}

		if (!is_dir($source)) {
			self::cliError(
				\sprintf(
					"%s files doesn't exist on this path: `%s`. Please check if you have eightshift-frontend-libs instaled.",
					ucfirst($type),
					$source,
				)
			);
		}

		$sourceItems = \array_diff(\scandir($source), ['..', '.']);
		$sourceItems = array_values($sourceItems);

		if ($isSingleFolder || $isFile) {
			$sourceItems = [
				$name,
			];
		}

		$sourceItemsOuput = \implode(\PHP_EOL, $sourceItems);

		if (!$sourceItems) {
			self::cliError(
				\sprintf(
					"%s files doesn't exist on this path: `%s`. Please check if you have eightshift-frontend-libs instaled.",
					ucfirst($type),
					$source
				)
			);
		}

		foreach ($itemsList as $item) {
			if (!in_array($item, $sourceItems, true)) {
				self::cliError(
					\sprintf(
						"Requested %s with the name `%s` doesn't exist in our library please review you search.\nYou can find all available items on this link: https://infinum.github.io/eightshift-docs/storybook/, \nor use this list for available items you can type: \n%s",
						$type,
						$item,
						$sourceItemsOuput
					)
				);
			}

			$fullSource = Components::joinPaths([$source, $item]);
			$fullDestination = Components::joinPaths([$destination, $item]);

			if ($isSingleFolder) {
				$fullSource = $source;
				$fullDestination = $destination;
			}

			if (\file_exists($fullDestination) && $skipExisting === false && !$isSingleFolder) {
				self::cliError(
					\sprintf(
						"%s files exist on this path: `%s`. If you want to override the destination folder plase use --skip_existing='true' argument.",
						ucfirst($type),
						$fullDestination
					)
				);
			}

			// Move item to project folder.
			if ($isFile) {
				$this->copyItem($fullSource, $fullDestination);
			} else {
				$this->copyRecursively($fullSource, $fullDestination);
			}

			$partialsOutput = [];
			$partialsPath = Components::joinPaths([$fullDestination, 'partials']);

			// Check if we have partials folder. If so output and that folder with items in it.
			if (\is_dir($partialsPath)) {
				$partials = \array_diff(\scandir($partialsPath), ['..', '.']);
				$partials = array_values($partials);

				$partialsOutput = \array_map(
					static function ($item) use ($sep) {
						return "partials{$sep}{$item}";
					},
					$partials
				);
			}

			$innerItems = \array_merge(
				$this->getFullBlocksFiles($name),
				$partialsOutput
			);

			foreach ($innerItems as $innerItem) {
				// Set output file path.
				$class = $this->getExampleTemplate($fullDestination, $innerItem, true);

				if (!empty($class->fileContents)) {
					$class->renameProjectName($args)
						->renameNamespace($args)
						->renameTextDomainFrontendLibs($args)
						->renameUseFrontendLibs($args)
						->outputWrite($fullDestination, $innerItem, [
							'skip_existing' => true,
							'groupOutput' => $groupOutput,
						]);
				}
			}

			if ($type === 'component' || $type === 'block') {
				WP_CLI::success(
					\sprintf(
						"%s files with name `%s` were successfully created in your project on this path `%s`.",
						ucfirst($type),
						$item,
						$destination
					)
				);
			} else {
				WP_CLI::success(
					\sprintf(
						"%s files successfully created in your project on this path `%s`.",
						ucfirst($type),
						$destination
					)
				);
			}
		}
	}
}
