<?php

/**
 * Class that registers WPCLI command initial setup for all.
 *
 * @package EightshiftLibs\Init
 */

declare(strict_types=1);

namespace EightshiftLibs\Init;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\Cli;
use EightshiftLibs\Cli\ParentGroups\CliInit;
use ReflectionClass;

/**
 * Class InitAllCli
 */
class InitAllCli extends AbstractCli
{
	/**
	 * All classes for initial theme setup for project.
	 *
	 * @var array<int, mixed>
	 */
	public const COMMANDS = [
		[
			'type' => 'classes',
			'label' => 'Setting classes:',
			'items' => Cli::CREATE_COMMANDS,
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
	 * Get WPCLI command parent name.
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliInit::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name.
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'all';
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Move everything to your project.',
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				This command is used to move everything that we have to your project, all service classes, block editor items, etc.

				## EXAMPLES

				# Setup project:
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);
		$groupOutput = $assocArgs[self::ARG_GROUP_OUTPUT];

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

			if ($type === 'blocks') {
				$assocArgs['use_all'] = true;
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
			$this->cliLog('We have moved everything we have to your project. Please type `npm start` in your terminal to kickstart your assets bundle process.', "M");
			$this->cliLog('Happy developing!', "M");
		}
	}
}
