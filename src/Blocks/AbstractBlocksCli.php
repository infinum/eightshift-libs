<?php

/**
 * Class that hold abstractions for for Blocks CLI
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Helpers\Helpers;

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

		$groupOutput = $args[self::ARG_GROUP_OUTPUT];

		if (!\is_dir($source)) {
			self::cliError(
				\sprintf(
					// translators: %s will be replaced with type of item and path.
					"%s doesn't exist on this path: '%s'. Please check if you have eightshift-frontend-libs installed.",
					\ucfirst($type),
					$source
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
					// translators: %s will be replaced with type of item and path.
					"%s doesn't exist on this path: '%s'. Please check if you have eightshift-frontend-libs installed.",
					$type,
					$source
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
						// translators: %s will be replaced with type of item, path and keys of sourceItems.
						"Requested %s with the name '%s' doesn't exist in our library. Please review you search.\nYou can find all available items on this list: \n\n%s\n\nOr find them on this link: https://eightshift.com/storybook/",
						$type,
						$item,
						\implode(\PHP_EOL, \array_keys($sourceItems))
					)
				);
			}

			$fullSource = Helpers::joinPaths([$source, $item]);
			$fullDestination = Helpers::joinPaths([$destination, $item]);

			if ($isSingleFolder) {
				$fullSource = $source;
				$fullDestination = $destination;
			}

			if (\file_exists($fullDestination) && $skipExisting === false && !$isSingleFolder) {
				self::cliError(
					\sprintf(
						// translators: %s will be replaced with type of item, path and skip flag.
						"%s %s is already present in your project.\n\nIf you want to override the destination folder, use --%s='true' argument.",
						\ucfirst($type),
						$fullDestination,
						AbstractCli::ARG_SKIP_EXISTING
					)
				);
			}

			// Move item to project folder.
			if ($isFile) {
				$this->copyItem($fullSource, $fullDestination);
			} else {
				$this->copyRecursively($fullSource, $fullDestination);
			}

			$innerItems = \array_merge(
				$this->getFullDirFiles($fullDestination),
				$this->getFullDirFiles($fullDestination, 'components'),
				$this->getFullDirFiles($fullDestination, 'partials'),
			);

			foreach ($innerItems as $innerItem) {
				// Set output file path.
				$class = $this->getExampleTemplate($fullDestination, $innerItem, true);

				if (!empty($class->fileContents)) {
					$class->renameGlobals($args)
						->outputWrite($fullDestination, $innerItem, [
							self::ARG_SKIP_EXISTING => true,
							self::ARG_GROUP_OUTPUT => true,
						]);
				}
			}

			if ($type === 'component' || $type === 'block') {
				if (!$groupOutput) {
					$this->cliLogAlert(
						\sprintf(
							// translators: %s will be replaced with type of item and path.
							"%s %s has been created in your project.",
							\ucfirst($type),
							$fullDestination
						),
						'success',
						"Success"
					);
				}

				$checkDependency = $args['checkDependency'] ?? true;

				if ($checkDependency) {
					$this->outputDependencyItems($fullSource, $type);
				}

				$this->outputNodeModuleDependencyItems($fullSource, $type);
			} else {
				if (!$groupOutput) {
					$this->cliLogAlert(
						\sprintf(
							// translators: %s will be replaced with type of item and path.
							"%s %s has been created in your project.",
							\ucfirst($type),
							$destination
						),
						'success',
						"Success"
					);
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
		$manifest = $this->getManifestDirect($source);

		// Component dependency.
		$componentsDependencies = $manifest['components'] ?? [];
		$innerBlocksDependency = $manifest['innerBlocksDependency'] ?? [];

		$dependencies = \array_merge($componentsDependencies, $innerBlocksDependency);

		if ($dependencies) {
			$componentsCommandName = UseComponentCli::COMMAND_NAME;
			$blocksCommandName = UseBlockCli::COMMAND_NAME;

			$outputComand = [];

			if ($componentsDependencies) {
				$componentsDependenciesAll = \array_map(static fn ($item) => Helpers::camelToKebabCase($item), $componentsDependencies);
				$componentsDependenciesAll = \implode(', ', \array_unique(\array_values($componentsDependenciesAll)));

				$outputComand[] = "%Uwp boilerplate {$this->getCommandParentName()} {$componentsCommandName} --name='{$componentsDependenciesAll}'%n";
			}

			if ($innerBlocksDependency) {
				$innerBlocksDependencyAll = \array_map(static fn ($item) => Helpers::camelToKebabCase($item), $innerBlocksDependency);
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
		$manifest = $this->getManifestDirect($source);

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
