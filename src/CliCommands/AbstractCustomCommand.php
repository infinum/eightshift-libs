<?php

/**
 * The class file that holds abstract class for CLI command registration
 *
 * @package EightshiftLibs\CliCommands
 */

declare(strict_types=1);

namespace EightshiftLibs\CliCommands;

use EightshiftLibs\Cli\CliHelpers;
use EightshiftLibs\Services\ServiceInterface;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use WP_CLI;

/**
 * Abstract base custom command class
 */
abstract class AbstractCustomCommand implements ServiceInterface
{
	/**
	 * CLI helpers trait.
	 */
	use CliHelpers;

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
	 * Get full class name for current class
	 *
	 * @return string
	 */
	public function getClassName(): string
	{
		return \get_class($this);
	}

	/**
	 * Prepare Command Doc for output
	 *
	 * @param array<string, mixed> $docs Command docs array.
	 * @param array<string, mixed> $docsGlobal Global docs array.
	 *
	 * @throws RuntimeException Error in case the shortdesc is missing in command docs.
	 *
	 * @return array<string, mixed>
	 */
	public function prepareCommandDocs(array $docs, array $docsGlobal): array
	{
		$shortdesc = $docs['shortdesc'] ?? '';

		if (!$shortdesc) {
			throw new RuntimeException('CLI Short description is missing.');
		}

		$synopsis = $docs['synopsis'] ?? [];

		return [
			'shortdesc' => $shortdesc,
			'synopsis' => \array_merge(
				$docsGlobal['synopsis'],
				$synopsis
			)
		];
	}

	/**
	 * Define global synopsis for all projects commands
	 *
	 * @return array<string, mixed>
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
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	abstract protected function getDoc(): array;

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	abstract protected function getCommandName(): string;

	/**
	 * Method that creates actual WPCLI command in terminal
	 *
	 * @throws Exception Exception in case the WP_CLI::add_command fails.
	 *
	 * @return void
	 *  phpcs:ignore Squiz.Commenting.FunctionCommentThrowTag.Missing
	 */
	public function registerCommand(): void
	{
		if (!\class_exists($this->getClassName())) {
			throw new RuntimeException('Class doesn\'t exist');
		}

		try {
			$reflectionClass = new ReflectionClass($this->getClassName());
			// @codeCoverageIgnoreStart
		} catch (ReflectionException $e) {
			self::cliError("{$e->getCode()}: {$e->getMessage()}");
		}
		// @codeCoverageIgnoreEnd

		$class = $reflectionClass->newInstanceArgs();

		if (!\is_callable($class)) {
			$className = \get_class($class);
			self::cliError("The class '{$className}' is not callable. Make sure the command class has an __invoke method.");
		}

		WP_CLI::add_command(
			'boilerplate' . ' ' . $this->getCommandName(),
			$class,
			$this->prepareCommandDocs($this->getDoc(), $this->getGlobalSynopsis())
		);
	}
}
