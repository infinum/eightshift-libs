<?php

/**
 * Abstract class that holds all methods for WP-CLI options.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\Helpers\Helpers;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use WP_CLI;
// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use Exception;
use WP_CLI\ExitException;

/**
 * Class AbstractCli
 */
abstract class AbstractCli implements CliInterface
{
	/**
	 * Top level commands name.
	 *
	 * @var string
	 */
	protected string $commandParentName;

	/**
	 * Contents of the example class
	 *
	 * When some renaming classes will be called, contents will get
	 * stored in this variable. That way we can chain the commands.
	 *
	 * @var string
	 */
	protected string $fileContents;

	/**
	 * Output template name.
	 *
	 * @var string
	 */
	public const TEMPLATE = '';

	/**
	 * Output project name arg.
	 *
	 * @var string
	 */
	public const ARG_PROJECT_NAME = 'g_project_name';

	/**
	 * Output project description arg.
	 *
	 * @var string
	 */
	public const ARG_PROJECT_DESCRIPTION = 'g_project_description';

	/**
	 * Output project author arg.
	 *
	 * @var string
	 */
	public const ARG_PROJECT_AUTHOR = 'g_project_author';

	/**
	 * Output project author url arg.
	 *
	 * @var string
	 */
	public const ARG_PROJECT_AUTHOR_URL = 'g_project_author_url';

	/**
	 * Output project version arg.
	 *
	 * @var string
	 */
	public const ARG_PROJECT_VERSION = 'g_project_version';

	/**
	 * Output textdomain arg.
	 *
	 * @var string
	 */
	public const ARG_TEXTDOMAIN = 'g_textdomain';

	/**
	 * Output composer_config_path arg.
	 *
	 * @var string
	 */
	public const ARG_COMPOSER_CONFIG_PATH = 'g_composer_config_path';

	/**
	 * Output skip existing arg.
	 *
	 * @var string
	 */
	public const ARG_SKIP_EXISTING = 'g_skip_existing';

	/**
	 * Output site_url arg.
	 *
	 * @var string
	 */
	public const ARG_SITE_URL = 'g_site_url';

	/**
	 * Output project libs version arg.
	 *
	 * @var string
	 */
	public const ARG_LIBS_VERSION = 'g_libs_version';

	/**
	 * Output project frontend libs version arg.
	 *
	 * @var string
	 */
	public const ARG_FRONTEND_LIBS_VERSION = 'g_frontend_libs_version';

	/**
	 * Output project frontend libs type arg.
	 *
	 * @var string
	 */
	public const ARG_FRONTEND_LIBS_TYPE = 'g_frontend_libs_type';

	/**
	 * Output namespace arg.
	 *
	 * @var string
	 */
	public const ARG_NAMESPACE = 'g_namespace';

	/**
	 * Output namespace_vendor_prefix arg.
	 *
	 * @var string
	 */
	public const ARG_NAMESPACE_VENDOR_PREFIX = 'g_namespace_vendor_prefix';

	/**
	 * Output use libs arg.
	 *
	 * @var string
	 */
	public const ARG_USE_LIBS = 'g_use_libs';

	/**
	 * Output group output arg.
	 *
	 * @var string
	 */
	public const ARG_GROUP_OUTPUT = 'g_group_output';

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
	 * Register method for WP-CLI command
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
	 * @return array<string, mixed>
	 */
	public function getGlobalSynopsis(): array
	{
		return [
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => self::ARG_COMPOSER_CONFIG_PATH,
					'description' => 'Define your project composer.json absolute path.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => self::ARG_SKIP_EXISTING,
					'description' => 'If this value is set to true CLI commands will not fail it they find an existing files in your project',
					'optional' => true,
					'options' => [
						'true',
						'false',
					]
				],
				[
					'type' => 'assoc',
					'name' => self::ARG_GROUP_OUTPUT,
					'description' => 'Use this flag if you want to group output messages only used for internal purposes.',
					'optional' => true,
					'options' => [
						'true',
						'false',
					]
				],
			],
		];
	}

	/**
	 * Prepare arguments for all the commands.
	 *
	 * @param array<string, mixed> $args Arguments array.
	 *
	 * @return array<string, mixed>
	 */
	public function prepareArgs(array $args = []): array
	{
		$configPath = $args[self::ARG_COMPOSER_CONFIG_PATH] ?? Helpers::getProjectPaths('', 'composer.json');
		$composerFile = $this->getComposer($configPath);

		$namespace = $composerFile ? \rtrim(\array_key_first($composerFile['autoload']['psr-4']), '\\') : 'EightshiftBoilerplate';

		if (isset($args[self::ARG_GROUP_OUTPUT])) {
			$args[self::ARG_GROUP_OUTPUT] = \filter_var($args[self::ARG_GROUP_OUTPUT], \FILTER_VALIDATE_BOOLEAN);
		}

		if (isset($args[self::ARG_SKIP_EXISTING])) {
			$args[self::ARG_SKIP_EXISTING] = \filter_var($args[self::ARG_SKIP_EXISTING], \FILTER_VALIDATE_BOOLEAN);
		}

		return \array_merge(
			[
				self::ARG_NAMESPACE => $namespace,
				self::ARG_NAMESPACE_VENDOR_PREFIX => $composerFile ? $composerFile['extra']['strauss']['namespace_prefix'] : "{$namespace}Vendor",
				self::ARG_TEXTDOMAIN => Helpers::camelToKebabCase($namespace),
				self::ARG_GROUP_OUTPUT => false,
			],
			$args
		);
	}

	/**
	 * Method that creates actual WP-CLI command in terminal
	 *
	 * @throws Exception Exception in case the WP_CLI::add_command fails.
	 *
	 * @return void
	 *
	 * phpcs:ignore Squiz.Commenting.FunctionCommentThrowTag.Missing
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
			$this->cliError("{$e->getCode()}: {$e->getMessage()}");
		}
		// @codeCoverageIgnoreEnd

		$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

		if (!\is_callable($class)) {
			$className = \get_class($class);
			$this->cliError("Class '{$className}' is not callable.\nMake sure the command class has an __invoke method.");
		}

		WP_CLI::add_command(
			"{$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}",
			$class,
			$this->prepareCommandDocs($this->getDoc(), $this->getGlobalSynopsis())
		);
	}

	/**
	 * Define default props for command.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [];
	}

	/**
	 * Get one argument.
	 *
	 * Use assocArgs props and fallback to default if missing.
	 *
	 * @param array<string, string> $arguments Array of args to check.
	 * @param string $key Argument name to check.
	 *
	 * @return string
	 */
	public function getArg(array $arguments, string $key): string
	{
		return isset($arguments[$key]) ? (string) $arguments[$key] : $this->getDefaultArg($key);
	}

	/**
	 * Get one default argument.
	 *
	 * @param string $key Argument name to get.
	 *
	 * @return string
	 */
	public function getDefaultArg(string $key): string
	{
		$args = $this->getDefaultArgs();

		if (!$args) {
			return '';
		}

		return isset($args[$key]) ? (string) $args[$key] : '';
	}

	/**
	 * Get argument template based on the key.
	 *
	 * @param string $key Key to search.
	 *
	 * @return string
	 */
	public function getArgTemplate(string $key): string
	{
		return "%{$key}%";
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
	 * Get short class name for current class
	 *
	 * @param bool $skipReplace Skip replacing CLI string.
	 *
	 * @throws RuntimeException Exception in the case the class name is missing.
	 *
	 * @return string
	 */
	public function getClassShortName(bool $skipReplace = false): string
	{
		$arr = \explode('\\', $this->getClassName());

		$lastElement = \end($arr);

		if ($skipReplace) {
			return $lastElement;
		}

		return \str_replace('Cli', '', $lastElement);
	}

	/**
	 * Generate correct class name from provided string
	 *
	 * Remove _, - and empty space. Create a camelcase from string.
	 *
	 * @param string $fileName File name from string.
	 *
	 * @return string
	 */
	public function getFileName(string $fileName): string
	{
		$class = \explode('_', \str_replace('-', '_', \str_replace(' ', '_', $fileName)));

		$className = \array_map(
			function ($item) {
				return \ucfirst($item);
			},
			$class
		);

		return \implode('', $className);
	}

	/**
	 * Get template file content and throw error if template is missing
	 *
	 * @param string $currentDir Absolute path to dir where example is.
	 * @param string $fileName File Name of example.
	 * @param bool   $skipMissing Skip existing file.
	 *
	 * @return AbstractCli Current CLI class.
	 */
	public function getExampleTemplate(string $currentDir, string $fileName, bool $skipMissing = false): self
	{
		$ds = \DIRECTORY_SEPARATOR;
		$currentDir = \rtrim($currentDir, $ds);
		$path = "{$currentDir}{$ds}{$this->getExampleFileName($fileName)}.php";

		// If you pass file name with extension the version will be used.
		if (\strpos($fileName, '.') !== false) {
			$path = "{$currentDir}{$ds}{$fileName}";
		}

		$templateFile = '';

		// Read the template contents, and replace the placeholders with provided variables.
		if (\file_exists($path)) {
			$templateFile = \file_get_contents($path);
		} else {
			if ($skipMissing) {
				$this->fileContents = '';
			} else {
				$this->cliError("The template {$path} seems to be missing.");
			}
		}

		$this->fileContents = (string)$templateFile;

		return $this;
	}

	/**
	 * Generate example template file/class name
	 *
	 * @param string $filename File name.
	 *
	 * @return string
	 */
	public function getExampleFileName(string $filename): string
	{
		return "{$filename}Example";
	}

	/**
	 * Open an updated file and create it on output location
	 *
	 * @param string $destination Absolute path to output.
	 * @param string $fileName File name to use on a new file..
	 * @param array<string, mixed> $args Optional arguments.
	 *
	 * @return void
	 */
	public function outputWrite(string $destination, string $fileName, array $args = []): void
	{
		$groupOutput = $args[self::ARG_GROUP_OUTPUT];

		// Set optional arguments.
		$skipExisting = $this->getSkipExisting($args);

		// Set output file path.
		$destinationFile = Helpers::joinPaths([$destination, $fileName]);

		// Bailout if file already exists.
		if (\file_exists($destinationFile) && $skipExisting === false) {
			$this->cliError(
				\sprintf(
					// translators: %s will be replaced with type of item, and shorten cli path.
					"%s is already present in your project.\n\nIf you want to override the destination folder, use --%s='true' parameter.",
					$destinationFile,
					AbstractCli::ARG_SKIP_EXISTING
				)
			);
		}

		// Create output dir if it doesn't exist.
		if (!\is_dir($destination)) {
			\mkdir($destination, 0755, true);
		}

		// Open a new file on output.
		// If there is any error, bailout. For example, user permission.
		if (\fopen($destinationFile, "wb") === false) {
			$this->cliError(
				// translators: %s will be replaced with path.
				\sprintf(
					"%s could not be created.'\n\nAn unknown error occurred.",
					$destinationFile
				),
			);
		}

		$fp = \fopen($destinationFile, "wb");

		// Write and close.
		\fwrite($fp, $this->fileContents);
		\fclose($fp);

		if (!$groupOutput) {
			$this->cliLogAlert(
				// translators: %s will be replaced with path.
				\sprintf(
					'File %s has been created in your project.',
					$destinationFile
				),
				'success',
				'Success'
			);
		}

		return;
	}

	/**
	 * Replace use in class
	 *
	 * @param array<string, mixed> $args CLI args array.
	 *
	 * @return AbstractCli Current CLI class.
	 */
	public function renameUse(array $args = []): self
	{
		$this->fileContents = \str_replace(
			$this->getArgTemplate(self::ARG_USE_LIBS),
			$args[self::ARG_NAMESPACE_VENDOR_PREFIX] . "\EightshiftLibs",
			$this->fileContents
		);

		return $this;
	}

	/**
	 * Replace generic key in class.
	 *
	 * @param string $keyName Key name to replace.
	 * @param array<string, mixed> $args CLI args array.
	 *
	 * @return AbstractCli Current CLI class.
	 */
	public function renameGeneric(string $keyName, array $args): self
	{
		if (isset($args[$keyName])) {
			if ($keyName === self::ARG_PROJECT_NAME) {
				$args[$keyName] = \ucfirst($args[$keyName]);
			}

			$this->fileContents = \str_replace(
				$this->getArgTemplate($keyName),
				$args[$keyName],
				$this->fileContents
			);
		}

		return $this;
	}

	/**
	 * Replace all generic keys in class.
	 *
	 * @param array<string, mixed> $args CLI args array.
	 *
	 * @return AbstractCli Current CLI class.
	 */
	public function renameGlobals(array $args = []): self
	{
		$this->renameGeneric(self::ARG_NAMESPACE, $args)
			->renameGeneric(self::ARG_TEXTDOMAIN, $args)
			->renameUse($args)
			->renameGeneric(self::ARG_PROJECT_NAME, $args)
			->renameGeneric(self::ARG_PROJECT_DESCRIPTION, $args)
			->renameGeneric(self::ARG_PROJECT_AUTHOR, $args)
			->renameGeneric(self::ARG_PROJECT_AUTHOR_URL, $args)
			->renameGeneric(self::ARG_PROJECT_VERSION, $args)
			->renameGeneric(self::ARG_SITE_URL, $args)
			->renameGeneric(self::ARG_NAMESPACE_VENDOR_PREFIX, $args);

		return $this;
	}

	/**
	 * Change Class full name
	 *
	 * @param string $className Class Name.
	 *
	 * @return AbstractCli Current CLI class.
	 */
	public function renameClassName(string $className): self
	{
		$this->fileContents = \str_replace($this->getExampleFileName($className), $className, $this->fileContents);

		return $this;
	}

	/**
	 * Change Class full name with prefix
	 *
	 * @param string $templateName Current template.
	 * @param string $newName New Class Name.
	 *
	 * @return AbstractCli Current CLI class.
	 */
	public function renameClassNameWithPrefix(string $templateName, string $newName): self
	{
		$this->fileContents = \str_replace($this->getExampleFileName($templateName), $newName, $this->fileContents);

		return $this;
	}

	/**
	 * Search and replace wrapper
	 *
	 * This method will do a search and replace in the fileContents member variable and
	 * return the current instance.
	 *
	 * It's a wrapper of str_replace.
	 *
	 * @param string $oldString Old string.
	 * @param string $newString New string.
	 *
	 * @return AbstractCli Current CLI class.
	 */
	public function searchReplaceString(string $oldString, string $newString): self
	{
		$this->fileContents = \str_replace($oldString, $newString, $this->fileContents);

		return $this;
	}

	/**
	 * Get composer from project or lib
	 *
	 * @param string $path Path to composer file.
	 *
	 * @return array<string, mixed>
	 */
	public function getComposer(string $path): array
	{
		$composerFile = \file_get_contents($path);

		if (!$composerFile) {
			$this->cliError("Composer was not found at\n{$path}");
		}

		return \json_decode((string)$composerFile, true);
	}

	/**
	 * Convert user input string to slug safe format
	 *
	 * Convert _ to -, empty space to - and convert everything to lowercase
	 * if the string contains empty space.
	 *
	 * @param string $stringToConvert String to convert.
	 *
	 * @return string
	 */
	public function prepareSlug(string $stringToConvert): string
	{
		if (\strpos($stringToConvert, ' ') !== false) {
			$stringToConvert = \strtolower($stringToConvert);
		}

		return \str_replace('_', '-', \str_replace(' ', '-', $stringToConvert));
	}

	/**
	 * Check and prepare default value for skip_existing arg.
	 *
	 * @param array<string, mixed> $args Optional arguments.
	 *
	 * @return boolean
	 */
	public function getSkipExisting(array $args): bool
	{
		return isset($args[self::ARG_SKIP_EXISTING]) && $args[self::ARG_SKIP_EXISTING];
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

		// Set optional props to false in case of development.
		$synopsis = \array_map(
			static function ($item) {
				$optional = $item['optional'] ?? true;

				$item['optional'] = $optional;

				if (\defined('ES_DEVELOP_MODE')) {
					$item['optional'] = true;
				}

				return $item;
			},
			\array_merge(
				$docs['synopsis'] ?? [],
				$docsGlobal['synopsis']
			)
		);

		return \array_merge(
			$docsGlobal,
			$docs,
			[
				'synopsis' => $synopsis
			]
		);
	}

	/**
	 * Return cli intro.
	 *
	 * @param array<string, mixed> $assocArgs $argument to pass.
	 *
	 * @return void
	 */
	protected function getIntroText(array $assocArgs): void
	{
		if ($assocArgs[self::ARG_GROUP_OUTPUT]) {
			return;
		}

		$this->cliLog($this->prepareLongDesc("
		%w╭──────────────────────────────────────────────────────────╮
		│                                                          │
		│  %R  ███████  ███████  %n   Thank you for using              %w│
		│  %R██       ██       ██%n   %9Eightshift DevKit%n                %w│
		│  %R██       ██       ██%n                                    %w│
		│  %R  ███████  ███████  %n   Read the docs at %Ueightshift.com%n  %w│
		│                                                          │
		╰──────────────────────────────────────────────────────────╯%n
		"), 'mixed');
	}

	/**
	 * Return assets command text.
	 *
	 * @return void
	 */
	protected function getAssetsCommandText(): void
	{
		$this->cliLogAlert(
			"Please run the following command to make sure everything works correctly.\n
			npm start",
			"info",
			'Command requirement',
		);
	}

	/**
	 * Run CLI command.
	 *
	 * @param string $commandClass Command class to run.
	 * @param string $commandParentName Parent name of the command.
	 * @param array<string, mixed> $args Arguments to pass.
	 *
	 * @return void
	 */
	public function runCliCommand(string $commandClass, string $commandParentName, array $args): void
	{
		$reflectionClass = new ReflectionClass($commandClass);
		$class = $reflectionClass->newInstanceArgs([$commandParentName]);

		$class->__invoke([], \array_merge(
			$args,
		));
	}

	/**
	 * WP CLI error logging helper
	 *
	 * A wrapper for the WP_CLI::error with error handling.
	 *
	 * @param string $errorMessage Error message to log in the CLI.
	 *
	 * @return void
	 */
	public function cliError(string $errorMessage): void
	{
		try {
			$this->cliLogAlert($errorMessage, 'error');
			WP_CLI::halt(1);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}"); // phpcs:ignore Eightshift.Security.HelpersEscape.OutputNotEscaped
		}
	}

	/**
	 * Output WP_CLI log with color.
	 *
	 * @param string $msg Msg to output.
	 * @param string $color Color to use from this list https://make.wordpress.org/cli/handbook/references/internal-api/wp-cli-colorize/.
	 *
	 * @return void
	 */
	protected function cliLog(string $msg, string $color = ''): void
	{
		if ($color === 'mixed') {
			WP_CLI::log(WP_CLI::colorize("{$msg}%n"));
			return;
		}

		if ($color) {
			WP_CLI::log(WP_CLI::colorize("%{$color}{$msg}%n"));
			return;
		}

		WP_CLI::log($msg);
	}

	/**
	 * Fancy WP_CLI log output in a box.
	 *
	 * @param string $msg Msg to output.
	 * @param string $type Type of message, either "success", "error", "warning" or "info".
	 * @param string $heading Alert heading.
	 *
	 * @return void
	 */
	protected function cliLogAlert(string $msg, string $type = 'success', string $heading = ''): void
	{
		$colorToUse = '%g';
		$defaultHeading = \__('Success', 'eightshift-libs');

		switch ($type) {
			case 'warning':
				$colorToUse = '%y';
				$defaultHeading = \__('Warning', 'eightshift-libs');
				break;
			case 'info':
				$colorToUse = '%B';
				$defaultHeading = \__('Info', 'eightshift-libs');
				break;
			case 'error':
				$colorToUse = '%R';
				$defaultHeading = \__('Something went wrong', 'eightshift-libs');
				break;
		}

		$headingToUse = empty($heading) ? $defaultHeading : $heading;

		if (\strpos($msg, '\n') !== false) {
			$output = "{$colorToUse}╭\n";
			$output .= "│ {$headingToUse}\n";

			foreach (\explode('\n', $msg) as $line) {
				$modifiedLine = \trim($line);
				$output .= "{$colorToUse}│ %n{$modifiedLine}\n";
			}

			$output .= "{$colorToUse}╰%n";
		} elseif (\preg_match('/\n/', $msg)) {
			$output = "{$colorToUse}╭\n";
			$output .= "│ {$headingToUse}\n";

			foreach (\explode("\n", $msg) as $line) {
				$modifiedLine = \trim($line);
				$output .= "{$colorToUse}│ %n{$modifiedLine}\n";
			}

			$output .= "{$colorToUse}╰%n";
		} else {
			$output = "{$colorToUse}╭\n";
			$output .= "│ {$headingToUse}\n";
			$output .= "│ %n{$msg}{$colorToUse}\n";
			$output .= "╰%n";
		}

		// Handle commands/code.
		$output = \preg_replace('/`(.*)`/', '%_$1%n', $output);

		WP_CLI::log(WP_CLI::colorize($output));
	}

	/**
	 * Return longdesc output for cli.
	 * Removes tabs and replaces them with space.
	 * Adds new line before and after ## heading.
	 *
	 * @param string $string String to convert.
	 *
	 * @return string
	 */
	public function prepareLongDesc(string $string): string
	{
		return \preg_replace('/(##+)(.*)/m', "\n" . '${1}${2}' . "\n", \preg_replace('/\s*^\s*/m', "\n", \trim($string)));
	}
}
