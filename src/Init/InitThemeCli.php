<?php

/**
 * Class that registers WPCLI command initial setup of theme project.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;
use EightshiftLibs\Enqueue\Blocks\EnqueueBlocksCli;
use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;
use EightshiftLibs\Main\MainCli;
use EightshiftLibs\Manifest\ManifestCli;
use EightshiftLibs\Menu\MenuCli;
use ReflectionClass;

/**
 * Class InitThemeCli
 */
class InitThemeCli extends AbstractCli
{
	/**
	 * All classes for initial theme setup for project
	 *
	 * @var array<int, mixed>
	 */
	public const COMMANDS = [
		[
			'type' => 'sc',
			'label' => 'Setting service classes:',
			'items' => [
				ConfigCli::class,
				MainCli::class,
				ManifestCli::class,
				EnqueueAdminCli::class,
				EnqueueBlocksCli::class,
				EnqueueThemeCli::class,
				MenuCli::class,
			],
		],
		[
			'type' => 'blocks',
			'label' => '',
			'items' => [
				InitBlocksCli::class,
			],
		],
	];

	/**
	 * Get WPCLI command parent name
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
		return 'theme';
	}

	/**
	 * Get WP-CLI command doc.
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Kickstart your WordPress theme with this simple command.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Generates initial theme setup with all files to create a custom theme.

				## EXAMPLES

				# Setup theme:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$groupOutput = $assocArgs['groupOutput'] ?? false;

		if (!$groupOutput) {
			$this->getIntroText();
		}

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
							'groupOutput' => $type === 'blocks',
							'introOutput' => false,
						]
					));
				}
			}

			$this->cliLog('--------------------------------------------------');
		}

		if (!$groupOutput) {
			$this->cliLog('We have moved everything you need to start creating your awesome WordPress theme. Please type `npm start` in your terminal to kickstart your assets bundle process.', "M");
			$this->cliLog('Happy developing!', "M");
		}
	}
}
