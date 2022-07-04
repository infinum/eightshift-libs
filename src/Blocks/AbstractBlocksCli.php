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

/**
 * Abstract class used for Blocks and Components
 */
abstract class AbstractBlocksCli extends AbstractCli
{
	/**
	 * Move items for the block editor to project folder.
	 *
	 * @param array<string, mixed> $assocArgs Array of arguments from WP-CLI command.
	 * @param string $source Source path.
	 * @param string $destination Destination path.
	 * @param bool $isSingleFolder Is single folder item.
	 *
	 * @return void
	 */
	protected function moveItems(array $assocArgs, string $source, string $destination, bool $isSingleFolder = false): void
	{
		$sep = \DIRECTORY_SEPARATOR;

		// Get Props.
		$skipExisting = $this->getSkipExisting($assocArgs);
		$groupOutput = $assocArgs['groupOutput'] ?? false;

		// Clean up name.
		$name = $this->getArg($assocArgs, 'name');
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
					'File `%s` path doesn\'t exist. Please check if you have eightshift-frontend-libs instaled.',
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
					'File `%s` path doesn\'t contain anything. Please check if you have eightshift-frontend-libs instaled.',
					$source
				)
			);
		}

		foreach ($itemsList as $item) {
			if (!in_array($item, $sourceItems, true)) {
				self::cliError(
					\sprintf(
						'Requested item with the name `%s` doesn\'t exist in our library please review you search.\nYou can find all available items on this link: https://infinum.github.io/eightshift-docs/storybook/, \nor use this list for available items you can type: \n%s',
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
						'File on this `%s` path exists. If you want to override the destination folder plase use --skip_existing="true" argument.',
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
					$class->renameProjectName($assocArgs)
						->renameNamespace($assocArgs)
						->renameTextDomainFrontendLibs($assocArgs)
						->renameUseFrontendLibs($assocArgs)
						->outputWrite($fullDestination, $innerItem, [
							'skip_existing' => true,
							'groupOutput' => $groupOutput,
						]);
				}
			}

			$this->cliLog(
				\sprintf(
					"File `%s` successfully moved to your project on this path `%s`.",
					$item,
					$destination
				)
			);
		}
	}
}
