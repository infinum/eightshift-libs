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

		// Clean up name.
		$name = $args['name'] ?? '';
		$name = \str_replace(' ', '', $name);
		$name = \trim($name, \DIRECTORY_SEPARATOR);

		$isFile = \strpos($name, '.') !== false;

		$itemsList = [$name];

		if (\strpos($name, ',') !== false || \strpos($name, ', ') !== false) {
			$itemsList = \explode(',', $name);
		}

		if (!\is_dir($source)) {
			self::cliError(
				\sprintf(
					// translators: %s will be replaced with type of item, and shorten cli path.
					"%s files doesn't exist on this path: `%s`. Please check if you have eightshift-frontend-libs installed.",
					$type,
					$this->getShortenCliPathOutput($source)
				)
			);
		}

		$sourceItems = \array_diff(\scandir($source), ['..', '.']);
		$sourceItems = \array_values($sourceItems);

		if ($isSingleFolder || $isFile) {
			$sourceItems = [
				$name,
			];
		}

		if (!$sourceItems) {
			self::cliError(
				\sprintf(
					// translators: %s will be replaced with type of item, and shorten cli path.
					"%s files doesn't exist on this path: `%s`. Please check if you have eightshift-frontend-libs installed.",
					$type,
					$this->getShortenCliPathOutput($source)
				)
			);
		}

		foreach ($itemsList as $item) {
			if (!\in_array($item, $sourceItems, true)) {
				self::cliError(
					\sprintf(
						// translators: %s will be replaced with type of item, item name and shorten cli path.
						"Requested %s with the name `%s` doesn't exist in our library. Please review you search.\nYou can find all available items on this list: \n\n%s\n\nOr find them on this link: https://eightshift.com/storybook/",
						$type,
						$item,
						\implode(\PHP_EOL, $sourceItems)
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
						// translators: %s will be replaced with type of item, and shorten cli path.
						"%s files exist on this path: `%s`. If you want to override the destination folder please use --skip_existing='true' argument.",
						$type,
						$this->getShortenCliPathOutput($fullDestination)
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

			// Check if we have partials folder. If so output that folder with items in it.
			if (\is_dir($partialsPath)) {
				$partials = \array_diff(\scandir($partialsPath), ['..', '.']);
				$partials = \array_values($partials);

				$partialsOutput = \array_map(
					static function ($item) use ($sep) {
						return "partials{$sep}{$item}";
					},
					$partials
				);
			}

			$innerItems = \array_merge(
				$this->getFullBlocksFiles($item),
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
							'groupOutput' => true,
						]);
				}
			}

			if ($type === 'component' || $type === 'block') {
				WP_CLI::success(
					\sprintf(
						// translators: %s will be replaced with type of item, item name and shorten cli path.
						"Added %s `%s` at `%s`.",
						$type,
						$item,
						$this->getShortenCliPathOutput($destination)
					)
				);

				$checkDependency = $args['checkDependency'] ?? true;

				if ($checkDependency) {
					$this->outputDependencyItems($fullSource, $type);
				}

				$this->outputNodeModuleDependencyItems($fullSource, $type);
			} else {
				WP_CLI::success(
					\sprintf(
						// translators: %s will be replaced with type of item, and shorten cli path.
						"`%s` created at `%s`.",
						$type,
						$this->getShortenCliPathOutput($destination)
					)
				);
			}
		}
	}

	/**
	 * Determine if the item has dependencies and output helper commands.
	 *
	 * @param string $source Source or the item.
	 * @param string $type Type for log.
	 *
	 * @return void
	 */
	private function outputDependencyItems(string $source, string $type): void
	{
		$manifest = Components::getManifestDirect($source);

		// Component dependency.
		$componentsDependencies = $manifest['components'] ?? [];
		$innerBlocksDependency = $manifest['innerBlocksDependency'] ?? [];

		$dependencies = \array_merge($componentsDependencies, $innerBlocksDependency);

		if ($dependencies) {
			$this->cliLog('');
			$this->cliLog('Dependency note:', 'B');
			$this->cliLog(
				\sprintf(
					"We have found that this %s has dependencies, please run these commands also if you don't have it in your project:",
					$type
				)
			);
			$componentsCommandName = UseComponentCli::COMMAND_NAME;
			$allDependencies = \array_map(
				static function ($item) {
					return Components::camelToKebabCase($item);
				},
				$dependencies
			);
			$allDependencies = \implode(', ', \array_unique(\array_values($allDependencies)));
			$this->cliLog("wp boilerplate {$this->getCommandParentName()} {$componentsCommandName} --name='{$allDependencies}'", 'C');
		}
	}

	/**
	 * Determine if the item has node_module dependencies and output helper commands.
	 *
	 * @param string $source Source or the item.
	 * @param string $type Type for log.
	 *
	 * @return void
	 */
	private function outputNodeModuleDependencyItems(string $source, string $type): void
	{
		$manifest = Components::getManifestDirect($source);

		// Node_module dependency.
		$nodeDependencies = $manifest['nodeDependency'] ?? [];

		if ($nodeDependencies) {
			$this->cliLog('');
			$this->cliLog('Node_modules Note:', 'B');
			$this->cliLog(
				\sprintf(
					"We have found that this %s has some node_module dependencies, please run these commands also if you don't have it in your project:",
					$type
				)
			);

			foreach ($nodeDependencies as $nitem) {
				$this->cliLog("npm install {$nitem}", 'C');
			}
		}
	}
}
