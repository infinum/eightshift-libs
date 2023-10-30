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
	 * @param array<string, mixed> $args Array of arguments from WP-CLI command.
	 * @param string $source Source path.
	 * @param string $destination Destination path.
	 * @param string $type Type of items used for output log.
	 * @param bool $isSingleFolder Is single folder item.
	 * @param string $sourcePrivate Source private libs path.
	 *
	 * @return void
	 */
	protected function moveItems(array $args, string $source, string $destination, string $type, bool $isSingleFolder = false, string $sourcePrivate = ''): void
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

		$groupOutput = $args['groupOutput'] ?? false;

		if (!\is_dir($source)) {
			self::cliError(
				\sprintf(
					// translators: %s will be replaced with type of item, and shorten cli path.
					"%s file doesn't exist on this path: `%s`. Please check if you have eightshift-frontend-libs installed.",
					$type,
					$this->getShortenCliPathOutput($source)
				)
			);
		}

		$sourceItems = \array_diff(\scandir($source) ?: [], ['..', '.']); // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
		$sourceItems = \array_fill_keys(\array_values($sourceItems), $source);
		$sourceItemsPrivate = [];

		if (\is_dir($sourcePrivate)) {
			$sourceItemsPrivate = \array_diff(\scandir($sourcePrivate) ?: [], ['..', '.']); // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
			$sourceItemsPrivate = \array_fill_keys(\array_values($sourceItemsPrivate), $sourcePrivate);
		}

		$sourceItems = \array_merge($sourceItems, $sourceItemsPrivate);

		if (!$sourceItems) {
			self::cliError(
				\sprintf(
					// translators: %1$s will be replaced with type of item, %2$s the type and %3$s and shorten cli path.
					'%1$s %2$s doesn\'t exist on this path: `%3$s`. Please check if you have eightshift-frontend-libs installed.',
					$type,
					$isFile ? 'file' : 'folder',
					$this->getShortenCliPathOutput($source)
				)
			);
		}

		$itemExists = false;
		foreach ($itemsList as $item) {
			foreach ($sourceItems as $sourceItem => $sourceFolder) {
				if (\strpos($sourceItem, $item) !== false) {
					$itemExists = true;
					break;
				}

				// in the case of folders, we should also check the source folders.
				if (\strpos($sourceFolder, $item) !== false) {
					$itemExists = true;
					break;
				}
			}

			if (!$itemExists) {
				self::cliError(
					\sprintf(
						// translators: %s will be replaced with type of item, item name and shorten cli path.
						"Requested %s with the name `%s` doesn't exist in our library. Please review you search.\nYou can find all available items on this list: \n\n%s\n\nOr find them on this link: https://eightshift.com/storybook/",
						$type,
						$item,
						\implode(\PHP_EOL, \array_keys($sourceItems))
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
				$path = $this->getShortenCliPathOutput($destination);
				$itemName = \ucfirst($item);

				$msgTitle = "{$itemName} {$type} added";

				if ($groupOutput) {
					$this->cliLog("%g│ %n{$msgTitle} %w({$path})%n", 'mixed');
				} else {
					$this->cliLogAlert(\implode("\n", [
						$path,
						'',
						'Run %Unpm start%n to make sure everything works correctly.'
					]), 'success', "{$itemName} {$type} added");
				}

				$checkDependency = $args['checkDependency'] ?? true;

				if ($checkDependency) {
					$this->outputDependencyItems($fullSource, $type);
				}

				$this->outputNodeModuleDependencyItems($fullSource, $type);
			} else {
				$path = $this->getShortenCliPathOutput($destination);

				if ($groupOutput) {
					$this->cliLog("%g│ %n{$type} created %w({$path})%n", 'mixed');
				} else {
					$this->cliLogAlert($path, 'success', "{$type} added");
				}
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
			$componentsCommandName = UseComponentCli::COMMAND_NAME;
			$blocksCommandName = UseBlockCli::COMMAND_NAME;

			$outputComand = [];

			if ($componentsDependencies) {
				$componentsDependenciesAll = \array_map(static fn ($item) => Components::camelToKebabCase($item), $componentsDependencies);
				$componentsDependenciesAll = \implode(', ', \array_unique(\array_values($componentsDependenciesAll)));

				$outputComand[] = "%Uwp boilerplate {$this->getCommandParentName()} {$componentsCommandName} --name='{$componentsDependenciesAll}'%n";
			}

			if ($innerBlocksDependency) {
				$innerBlocksDependencyAll = \array_map(static fn ($item) => Components::camelToKebabCase($item), $innerBlocksDependency);
				$innerBlocksDependencyAll = \implode(', ', \array_unique(\array_values($innerBlocksDependencyAll)));

				$outputComand[] = "%Uwp boilerplate {$this->getCommandParentName()} {$blocksCommandName} --name='{$innerBlocksDependencyAll}'%n";
			}

			if ($outputComand) {
				$this->cliLogAlert(\implode("\n", [
					"This {$type} may need some dependencies to work correctly.",
					'',
					'To add them to your project, run:',
					...$outputComand,
					'',
					'If a dependency already exists in your project, you can skip it.',
				]), 'info', 'Dependencies found');
			}
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
			$this->cliLogAlert(\implode("\n", [
				"This {$type} requires some external dependencies to work correctly.",
				'',
				'To add them to your project, run:',
				...\array_map(fn ($package) => "%Unpm install {$package}%n", $nodeDependencies),
				'',
				'If a dependency already exists in your project, you can skip it.',
			]), 'info', 'Packages needed');
		}
	}
}
