<?php

/**
 * Abstract class that holds all methods for WPCLI options.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use WP_CLI\ExitException;

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
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = '';

	/**
	 * Output template name.
	 *
	 * @var string
	 */
	public const TEMPLATE = '';

	/**
	 * Construct Method.
	 *
	 * @param string $commandParentName Define top level commands name.
	 */
	public function __construct(string $commandParentName)
	{
		$this->commandParentName = $commandParentName;
	}

	/**
	 * Register method for WPCLI command
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('cli_init', [$this, 'registerCommand']);
	}

	/**
	 * Define global synopsis for all projects commands
	 *
	 * @return array
	 */
	public function getGlobalSynopsis(): array
	{
		return [
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'namespace',
					'description' => 'Define your project namespace. Default is read from composer autoload psr-4 key.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'vendor_prefix',
					'description' => 'Define your project vendor_prefix. Default is read from composer extra, imposter, namespace key.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'config_path',
					'description' => 'Define your project composer absolute path.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'skip_existing',
					'description' => 'If this value is set to true CLI commands will not fail it they find an existing files in your project',
					'optional' => true,
				],
			],
		];
	}

	/**
	 * Method that creates actual WPCLI command in terminal
	 *
	 * @throws \Exception Exception in case the WP_CLI::add_command fails.
	 *
	 * @return void
	 *  phpcs:ignore Squiz.Commenting.FunctionCommentThrowTag.Missing
	 */
	public function registerCommand(): void
	{
		if (! class_exists($this->getClassName())) {
			throw new \RuntimeException('Class doesn\'t exist');
		}

		try {
			$reflectionClass = new \ReflectionClass($this->getClassName());
		} catch (\ReflectionException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}

		$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

		if (!is_callable($class)) {
			try {
				$className = get_class($class);
				\WP_CLI::error(
					"The class '{$className}' is not callable. Make sure the command class has an __invoke method."
				);
			} catch (ExitException $e) {
				exit("{$e->getCode()}: {$e->getMessage()}");
			}
		}

		\WP_CLI::add_command(
			$this->commandParentName . ' ' . $this->getCommandName(),
			$class,
			$this->prepareCommandDocs($this->getDoc(), $this->getGlobalSynopsis())
		);
	}

	/**
	 * Define default develop props
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
	 * Get full class name for current class
	 *
	 * @return string
	 */
	public function getClassName(): string
	{
		return get_class($this);
	}

	/**
	 * Get short class name for current class
	 *
	 * @throws \RuntimeException Exception in the case the class name is missing.
	 *
	 * @return string
	 */
	public function getClassShortName(): string
	{
		$arr = explode('\\', $this->getClassName());

		$lastElement = end($arr);

		if (empty($lastElement)) {
			throw new \RuntimeException('No class name given.');
		}

		return str_replace('Cli', '', $lastElement);
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'create_' . strtolower((string)preg_replace('/(?<!^)[A-Z]/', '_$0', $this->getClassShortName()));
	}
}
