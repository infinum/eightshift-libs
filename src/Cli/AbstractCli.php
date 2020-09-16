<?php

/**
 * Abstract class that holds all methods for WPCLI options.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

/**
 * Class AbstractCli
 */
abstract class AbstractCli implements CliInterface
{
	/**
	 * CLI helpers trait.
	 */
	use CliHelpers;

	/**
	 * Top level commands name.
	 *
	 * @var string
	 */
	protected $commandParentName;

	/**
	 * Output dir relative path.
	 */
	public const OUTPUT_DIR = '';

	/**
	 * Output template name.
	 */
	public const TEMPLATE = '';

	/**
	 * Construct Method.
	 *
	 * @param string $commandParentName Define top level commands name.
	 *
	 * @return void
	 */
	public function __construct($commandParentName)
	{
		$this->commandParentName = $commandParentName;
	}

	/**
	 * Register method for WPCLI command.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('cli_init', [ $this, 'registerCommand' ]);
	}

	/**
	 * Define global synopsis for all projects commands.
	 *
	 * @return array
	 */
	public function getGlobalSynopsis(): array
	{
		return [
			'synopsis' => [
				[
					'type'        => 'assoc',
					'name'        => 'namespace',
					'description' => 'Define your project namespace. Default is read from composer autoload psr-4 key.',
					'optional'    => true,
				],
				[
					'type'        => 'assoc',
					'name'        => 'vendor_prefix',
					'description' => 'Define your project vendor_prefix. Default is read from composer extra, imposter, namespace key.',
					'optional'    => true,
				],
				[
					'type'        => 'assoc',
					'name'        => 'config_path',
					'description' => 'Define your project composer absolute path.',
					'optional'    => true,
				],
			],
		];
	}

	/**
	 * Method that creates actual WPCLI command in terminal.
	 *
	 * @throws \ReflectionException Exception in the case the class is missing.
	 *
	 * @return void
	 */
	public function registerCommand(): void
	{
		$reflectionClass = new \ReflectionClass($this->getClassName());
		$class           = $reflectionClass->newInstanceArgs([ $this->commandParentName ]);

		\WP_CLI::add_command(
			$this->commandParentName . ' ' . $this->getCommandName(),
			$class,
			array_merge(
				$this->getGlobalSynopsis(),
				$this->getDoc()
			)
		);
	}

	/**
	 * Define default develop props.
	 *
	 * @param array $args WPCLI eval-file arguments.
	 *
	 * @return array
	 */
	public function getDevelopArgs(array $args): array
	{
		return $args;
	}

	/**
	 * Get full class name for current class.
	 *
	 * @return string
	 */
	public function getClassName(): string
	{
		return get_class($this);
	}

	/**
	 * Get short class name for current class.
	 *
	 * @return string
	 */
	public function getClassShortName(): string
	{
		$arr = explode('\\', $this->getClassName());

		return str_replace('Cli', '', end($arr));
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'create_' . strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $this->getClassShortName()));
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return string
	 */
	public function getDoc(): array
	{
		return [];
	}
}
