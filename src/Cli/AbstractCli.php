<?php

/**
 * Abstract class that holds all methods for WPCLI options.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\Exception\InvalidPath;
use EightshiftLibs\Helpers\Helpers;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use UnexpectedValueException;
use WP_CLI;
// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use Exception;

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
	 * Output is setup arg.
	 *
	 * @var string
	 */
	public const ARG_IS_SETUP = 'g_is_setup';

	/**
	 * Output use libs arg.
	 *
	 * @var string
	 */
	public const ARG_USE_LIBS = 'g_use_libs';

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
		$configPath = $args[self::ARG_COMPOSER_CONFIG_PATH] ?? Helpers::getProjectPaths('root', 'composer.json');
		$composerFile = $this->getComposer($configPath);

		$namespace = $composerFile ? \rtrim(\array_key_first($composerFile['autoload']['psr-4']), '\\') : 'EightshiftBoilerplate';

		return \array_merge(
			[
				self::ARG_NAMESPACE => $namespace,
				self::ARG_NAMESPACE_VENDOR_PREFIX => $composerFile ? $composerFile['extra']['strauss']['namespace_prefix'] : "{$namespace}Vendor",
				self::ARG_TEXTDOMAIN => Helpers::camelToKebabCase($namespace),
			],
			$args
		);
	}

	/**
	 * Prepare arguments for setup commands.
	 *
	 * @param array<string, mixed> $args Arguments array.
	 *
	 * @return array<string, mixed>
	 */
	public function prepareSetupArgs(array $args = []): array
	{
		$namespace = $this->convertToNamespace($args[self::ARG_PROJECT_NAME]);

		return [
			self::ARG_NAMESPACE => $namespace,
			self::ARG_NAMESPACE_VENDOR_PREFIX => "{$namespace}Vendor",
			self::ARG_TEXTDOMAIN => Helpers::camelToKebabCase($namespace),
			self::ARG_PROJECT_NAME => $args[self::ARG_PROJECT_NAME] ?? 'Eightshift Boilerplate',
			self::ARG_PROJECT_DESCRIPTION => $args[self::ARG_PROJECT_DESCRIPTION] ?? 'Eightshift Boilerplate is a WordPress starter theme that helps you build better and faster using the modern development tools.',
			self::ARG_PROJECT_AUTHOR => $args[self::ARG_PROJECT_AUTHOR] ?? 'Team Eightshift',
			self::ARG_PROJECT_AUTHOR_URL => $args[self::ARG_PROJECT_AUTHOR_URL] ?? 'https://eightshift.com/',
			self::ARG_PROJECT_VERSION => $args[self::ARG_PROJECT_VERSION] ?? '1.0.0',
			self::ARG_SITE_URL => $args[self::ARG_SITE_URL] ?? \site_url(),
			self::ARG_LIBS_VERSION => $args[self::ARG_LIBS_VERSION] ?? '',
			self::ARG_IS_SETUP => 'true',
			self::ARG_SKIP_EXISTING => 'true',
		];
	}

	/**
	 * Method that creates actual WPCLI command in terminal
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
			self::cliError("{$e->getCode()}: {$e->getMessage()}");
		}
		// @codeCoverageIgnoreEnd

		$class = $reflectionClass->newInstanceArgs([$this->commandParentName]);

		if (!\is_callable($class)) {
			$className = \get_class($class);
			self::cliError("Class '{$className}' is not callable.\nMake sure the command class has an __invoke method.");
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
				self::cliError("The template {$path} seems to be missing.");
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
		$groupOutput = $args['groupOutput'] ?? false;
		$typeOutput = $args['typeOutput'] ?? \__('Service class', 'eightshift-libs');
		$actionOutput = $args['actionOutput'] ?? null;

		// Set optional arguments.
		$skipExisting = $this->getSkipExisting($args);

		// Set output file path.
		$destinationFile = Helpers::joinPaths([$destination, $fileName]);

		// Bailout if file already exists.
		if (\file_exists($destinationFile) && $skipExisting === false) {
			$path = $this->getShortenCliPathOutput($destinationFile);

			self::cliError(
				\sprintf(
					// translators: %s will be replaced with type of item, and shorten cli path.
					"%1\$s '%2\$s' is already present at\n'%3\$s'\n\nIf you want to override the destination folder, use --%4\$s='true'",
					$typeOutput,
					$fileName,
					$path,
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
			$path = $this->getShortenCliPathOutput($destinationFile);

			self::cliError(
				"{$typeOutput} '{$fileName}' could not be created at\n'{$path}'\n\nAn unknown error ocurred."
			);
		}

		$fp = \fopen($destinationFile, "wb");

		// Write and close.
		\fwrite($fp, $this->fileContents);
		\fclose($fp);

		if (!$groupOutput) {
			// Return success.
			$path = $this->getShortenCliPathOutput($destinationFile);

			if ($skipExisting) {
				$action = $actionOutput ?? 'renamed';
				$this->cliLogAlert($path, 'success', "'{$fileName}' {$action}");
			} else {
				$action = $actionOutput ?? 'created';
				$this->cliLogAlert($path, 'success', "'{$fileName}' {$action}");
			}
		}

		return;
	}

	/**
	 * Get full output dir path
	 *
	 * @param string $file File name.
	 *
	 * @return string
	 */
	public function getOutputFile(string $file): string
	{
		$ds = \DIRECTORY_SEPARATOR;

		$file = \trim($file, $ds);

		if (\strpos($file, '.') !== false) {
			return "{$ds}{$file}";
		}

		return "{$ds}{$file}.php";
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
			!\getenv('ES_TEST') ? $args[self::ARG_NAMESPACE_VENDOR_PREFIX] . "\EightshiftLibs" : 'EightshiftLibs',
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
	 * Clean up initial boilerplate files.
	 *
	 * @return void
	 */
	public function cleanUpInitialBoilerplate(): void
	{
		$this->cliLog('Removing initial boilerplate setup files', 'C');
		WP_CLI::runcommand("eval 'shell_exec(\"rm -rf .github\");'");
		WP_CLI::runcommand("eval 'shell_exec(\"rm CODE_OF_CONDUCT.md\");'");
		WP_CLI::runcommand("eval 'shell_exec(\"rm CHANGELOG.md\");'");
		WP_CLI::runcommand("eval 'shell_exec(\"rm LICENSE.md\");'");
	}

	/**
	 * Run commands after initial setup.
	 *
	 * @return void
	 */
	public function initMandatoryAfter(string $libsVersion): void
	{
		$this->cliLog('Removing old vendor folder', 'C');
		WP_CLI::runcommand("eval 'shell_exec(\"rm -rf vendor\");'");
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog('Removing old compeser.lock', 'C');
		WP_CLI::runcommand("eval 'shell_exec(\"rm composer.lock\");'");
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog('Running composer install', 'C');
		if ($libsVersion) {
			var_dump("eval 'shell_exec(\"composer require eightshift/libs:dev-{$libsVersion} --no-interaction\");'");
			WP_CLI::runcommand("eval 'shell_exec(\"composer require infinum/eightshift-libs:dev-{$libsVersion} --no-interaction\");'");
		} else {
			var_dump("eval 'shell_exec(\"composer require eightshift/libs --no-interaction\");'");
			WP_CLI::runcommand("eval 'shell_exec(\"composer require infinum/eightshift-libs --no-interaction\");'");
		}
		WP_CLI::runcommand("eval 'shell_exec(\"composer reqire --ignore-platform-reqs\");'");
		$this->cliLog('--------------------------------------------------', 'C');
		$this->cliLog('Running npm install', 'C');
		WP_CLI::runcommand("eval 'shell_exec(\"npm install\");'");
		$this->cliLog('--------------------------------------------------', 'C');
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
	 * Change version number as a string.
	 *
	 * @param string $version New version.
	 *
	 * @return AbstractCli Current CLI class.
	 */
	public function renameVersionString(string $version): self
	{
		$this->fileContents = \preg_replace('/Version: .*/', "Version: {$version}", $this->fileContents);
		$this->fileContents = \preg_replace('/"version": ".*"/', "\"version\": \"{$version}\"", $this->fileContents);

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
			self::cliError("Composer was not found at\n{$path}");
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
	 * Get full dir files.
	 *
	 * @param string $path Path to scan.
	 * @param string $sufix Sufix to add to path.
	 *
	 * @return string[]
	 */
	public function getFullDirFiles(string $path, string $sufix = ''): array
	{
		$scanDir = Helpers::joinPaths([$path, $sufix]);

		if (!\is_dir($scanDir)) {
			return [$path];
		}

		$dir = \array_diff(\scandir($scanDir), ['..', '.']);

		return \array_filter(\array_map(
			static function ($item) use ($path, $sufix) {
				if (!\is_dir(Helpers::joinPaths([$path, $sufix, $item]))) {
					if ($sufix) {
						return "{$sufix}/{$item}";
					} else {
						return $item;
					}
				}
			},
			$dir
		));
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
	 * Manually prepare arguments to pass to runcommand method.
	 *
	 * @param array<string, mixed> $args Array of arguments.
	 *
	 * @return string
	 */
	public function prepareArgsManual(array $args): string
	{
		$output = '';
		foreach ($args as $key => $value) {
			$output .= "--{$key}='{$value}' ";
		}

		return $output;
	}

	/**
	 * Recursive copy helper
	 *
	 * @link https://stackoverflow.com/a/7775949/629127
	 *
	 * @param string $source Source path.
	 * @param string $destination Destination path.
	 *
	 * @throws InvalidPath Exception in case the source path is missing.
	 *
	 * @return void
	 */
	protected function copyRecursively(string $source, string $destination): void
	{
		if (!\is_dir($destination)) {
			\mkdir($destination, 0755, true);
		}

		try {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
				RecursiveIteratorIterator::SELF_FIRST
			);
		} catch (UnexpectedValueException $exception) {
			throw InvalidPath::missingFileException($source);
		}

		$ds = \DIRECTORY_SEPARATOR;

		foreach ($iterator as $item) {
			$subPathName = $iterator->getSubPathname();
			$destinationPath = \rtrim($destination, $ds) . $ds . $subPathName;

			if ($item->isDir()) {
				if (!\file_exists($destinationPath)) {
					\mkdir($destinationPath, 0755, true);
				}
			} else {
				\copy($item->getPathname(), $destinationPath);
			}
		}
	}

	/**
	 * Copy item from source to destination.
	 *
	 * @param string $source Source path.
	 * @param string $destination Destination path.
	 *
	 * @return void
	 */
	protected function copyItem(string $source, string $destination): void
	{
		$dir = \dirname($destination);

		if (!\file_exists($dir)) {
			\mkdir($dir, 0755, true);
		}

		\copy($source, $destination);
	}

	/**
	 * Return cli intro.
	 *
	 * @param array<string, mixed> $arg $argument to pass.
	 *
	 * @return void
	 */
	protected function getIntroText(array $arg = []): void
	{
		$introOutput = $arg['introOutput'] ?? true;

		if (!$introOutput) {
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
	 * Get manifest json. Generally used for getting block/components manifest. Used to directly fetch json file.
	 * Used in combination with getManifest helper.
	 *
	 * @param string $path Absolute path to manifest folder.
	 *
	 * @throws InvalidPath Exception in case the manifest file is missing.
	 *
	 * @return array<string, mixed>
	 */
	public function getManifestDirect(string $path): array
	{
		$sep = \DIRECTORY_SEPARATOR;
		$path = \rtrim($path, $sep);

		$manifest = "{$path}{$sep}manifest.json";

		if (!\file_exists($manifest)) {
			throw InvalidPath::missingFileException($manifest);
		}

		return \json_decode(\implode(' ', (array)\file($manifest)), true);
	}

	/**
	 * Convert string to valid namespace.
	 *
	 * @param string $name Name to convert.
	 *
	 * @return string
	 */
	public function convertToNamespace(string $name): string
	{
		// Replace all non-alphanumeric characters with underscores.
		$namespace = \preg_replace('/[^a-zA-Z0-9_]/', '_', $name);

		// Replace multiple underscores with a single underscore.
		$namespace = \preg_replace('/_+/', '_', $namespace);

		// Trim underscores from the start and end of the namespace.
		$namespace = \trim($namespace, '_');

		// Ensure the namespace does not start with a digit.
		if (\ctype_digit($namespace[0])) {
				$namespace = 'N' . $namespace;
		}

		// Convert to PascalCase as an optional style.
		$namespace = \str_replace('_', ' ', $namespace);
		$namespace = \ucwords($namespace);
		$namespace = \str_replace(' ', '', $namespace);

		return $namespace;
	}
}
