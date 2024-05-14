<?php

/**
 * Class that registers WP-CLI command initial setup of plugin project.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\Cache\ManifestCacheCli;
use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use EightshiftLibs\Config\ConfigPluginCli;
use EightshiftLibs\Main\MainCli;
use ReflectionClass;

/**
 * Class InitPluginCli
 */
class InitPluginCli extends AbstractCli
{
	/**
	 * All classes for initial plugin setup for project.
	 *
	 * @var array<int, mixed>
	 */
	public const COMMANDS = [
		[
			'type' => 'sc',
			'label' => 'Setting service classes:',
			'items' => [
				ManifestCacheCli::class,
				ConfigPluginCli::class,
				MainCli::class,
			],
		],
	];

	/**
	 * Get WP-CLI command parent name.
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliInit::COMMAND_NAME;
	}

	/**
	 * Get WP-CLI command name.
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'plugin';
	}

	/**
	 * Get WP-CLI command doc.
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Kickstart your WordPress plugin with this simple command.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Generates initial plugin setup with all files to create a custom plugin.

				## EXAMPLES

				# Setup plugin:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$groupOutput = $assocArgs[self::ARG_GROUP_OUTPUT];

		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		foreach (static::COMMANDS as $item) {
			$label = $item['label'] ?? '';
			$items = $item['items'] ?? [];
			$type = $item['type'] ?? '';

			if ($label) {
				$this->cliLog($label, 'C');
			}

			if ($items) {
				foreach ($items as $className) {
					$reflectionClass = new ReflectionClass($className);
					$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

					$class->__invoke([], \array_merge(
						$assocArgs,
						[
							self::ARG_GROUP_OUTPUT => $type === 'blocks',
						]
					));
				}
			}

			$this->cliLog('--------------------------------------------------');
		}

		if (!$groupOutput) {
			$this->cliLog('We have moved everything you need to start creating your awesome WordPress plugin.', "M");
			$this->cliLog('Happy developing!', "M");
		}
	}
}
