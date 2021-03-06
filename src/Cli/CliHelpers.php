<?php

/**
 * WPCLI Helper methods.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use WP_CLI\ExitException;

/**
 * CliHelpers trait
 */
trait CliHelpers
{

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
		$class = explode('_', str_replace('-', '_', str_replace(' ', '_', $fileName)));

		$className = array_map(
			function ($item) {
				return ucfirst($item);
			},
			$class
		);

		return implode('', $className);
	}

	/**
	 * Get template file content and throw error if template is missing
	 *
	 * @param string $currentDir Absolute path to dir where example is.
	 * @param string $fileName File Name of example.
	 * @param bool   $skipMissing Skip existing file.
	 *
	 * @return string
	 */
	public function getExampleTemplate(string $currentDir, string $fileName, bool $skipMissing = false): string
	{
		$templateFile = '';

		// If you pass file name with extension the version will be used.
		if (strpos($fileName, '.') !== false) {
			$path = "{$currentDir}/{$fileName}";
		} else {
			$path = "{$currentDir}/{$this->getExampleFileName( $fileName )}.php";
		}

		// Read the template contents, and replace the placeholders with provided variables.
		if (file_exists($path)) {
			$templateFile = file_get_contents($path);
		} else {
			if ($skipMissing) {
				$templateFile = '';
			} else {
				self::cliError("The template {$path} seems to be missing.");
			}
		}

		return (string)$templateFile;
	}

	/**
	 * Generate example template file/class name
	 *
	 * @param string $string File name.
	 *
	 * @return string
	 */
	public function getExampleFileName(string $string): string
	{
		return "{$string}Example";
	}

	/**
	 * Open an updated file and create it on output location
	 *
	 * @param string $outputDir Absolute path to output from project root dir.
	 * @param string $outputFile Absolute path to output file.
	 * @param string $class Modified class.
	 * @param array  $args Optional arguments.
	 *
	 * @return void
	 */
	public function outputWrite(string $outputDir, string $outputFile, string $class, array $args = []): void
	{

		// Set optional arguments.
		$skipExisting = $this->getSkipExisting($args);

		// Set output paths.
		$outputDir = $this->getOutputDir($outputDir);

		// Set output file path.
		$outputFile = $this->getOutputFile($outputFile);
		$outputFile = "{$outputDir}{$outputFile}";

		// Bailout if file already exists.
		if (file_exists($outputFile) && $skipExisting === false) {
			self::cliError("The file {$outputFile} can\'t be generated because it already exists.");
		}

		// Create output dir if it doesn't exist.
		if (!is_dir($outputDir)) {
			mkdir($outputDir, 0755, true);
		}

		// Open a new file on output.
		// If there is any error bailout. For example, user permission.
		if (fopen($outputFile, "wb") !== false) {
			$fp = fopen($outputFile, "wb");

			// Write and close.
			fwrite($fp, $class);
			fclose($fp);

			// Return success.
			if ($skipExisting) {
				\WP_CLI::success("File {$outputFile} successfully renamed.");
			} else {
				\WP_CLI::success("File {$outputFile} successfully created.");
			}
			return;
		}

		self::cliError("File {$outputFile} couldn\'t be created. There was an error.");
	}

	/**
	 * Get full output dir path
	 *
	 * @param string $path Project specific path.
	 *
	 * @return string
	 */
	public function getOutputDir(string $path = ''): string
	{
		if (function_exists('\add_action')) {
			$root = $this->getProjectRootPath();
		} else {
			$root = $this->getProjectRootPath(true) . '/cliOutput';
		}

		$root = rtrim($root, '/');
		$root = trim($root, '/');

		$path = rtrim($path, '/');
		$path = trim($path, '/');

		return "/{$root}/{$path}";
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
		$file = rtrim($file, '/');
		$file = trim($file, '/');

		if (strpos($file, '.') !== false) {
			return "/{$file}";
		}

		return "/{$file}.php";
	}

	/**
	 * Replace namespace EightshiftBoilerplateVendor\ in class
	 *
	 * @param array  $args CLI args array.
	 * @param string $string Full class as a string.
	 *
	 * @return string
	 */
	public function renameNamespace(array $args = [], string $string = ''): string
	{
		$output = $string;
		$namespace = $this->getNamespace($args);
		$vendorPrefix = $this->getVendorPrefix($args);

		if (function_exists('\add_action')) {
			$output = str_replace(
				"namespace {$vendorPrefix}\EightshiftBoilerplate\\",
				"namespace {$namespace}\\",
				$output
			);
		} else {
			$output = str_replace(
				'namespace EightshiftBoilerplate\\',
				"namespace {$namespace}\\",
				$output
			);
		}

		$output = str_replace(
			'@package EightshiftBoilerplate',
			"@package {$namespace}",
			$output
		);

		return (string)$output;
	}

	/**
	 * Replace use in class
	 *
	 * @param array  $args CLI args array.
	 * @param string $string Full class as a string.
	 *
	 * @return string
	 */
	public function renameUse(array $args = [], string $string = ''): string
	{
		$output = $string;

		$vendorPrefix = $this->getVendorPrefix($args);
		$namespace = $this->getNamespace($args);

		$prefix = 'use';

		if (function_exists('\add_action')) {
			$output = str_replace(
				"{$prefix} EightshiftBoilerplateVendor\\",
				"{$prefix} {$vendorPrefix}\\",
				$output
			);

			$output = str_replace(
				"{$prefix} {$vendorPrefix}\EightshiftBoilerplate\\",
				"{$prefix} {$namespace}\\",
				$output
			);
		} else {
			$output = str_replace(
				"{$prefix} EightshiftBoilerplate\\",
				"{$prefix} {$namespace}\\",
				$output
			);
		}

		return (string)$output;
	}

	/**
	 * Replace use in frontend libs views.
	 *
	 * @param array  $args CLI args array.
	 * @param string $string Full class as a string.
	 *
	 * @return string
	 */
	public function renameUseFrontendLibs(array $args = [], string $string = ''): string
	{
		$output = $string;

		$vendorPrefix = $this->getVendorPrefix($args);
		$namespace = $this->getNamespace($args);

		$prefix = 'use';

		$output = str_replace(
			"{$prefix} EightshiftBoilerplateVendor\\",
			"{$prefix} {$vendorPrefix}\\",
			$output
		);

		$output = str_replace(
			"{$prefix} EightshiftBoilerplate\\",
			"{$prefix} {$namespace}\\",
			$output
		);

		return (string)$output;
	}

	/**
	 * Replace text domain in class
	 *
	 * @param array  $args CLI args array.
	 * @param string $string Full class as a string.
	 *
	 * @return string
	 */
	public function renameTextDomain(array $args = [], string $string = ''): string
	{
		$namespace = $this->getNamespace($args);

		return str_replace(
			'eightshift-libs',
			$namespace,
			$string
		);
	}

	/**
	 * Replace text domain in class for frontend libs
	 *
	 * @param array  $args CLI args array.
	 * @param string $string Full class as a string.
	 *
	 * @return string
	 */
	public function renameTextDomainFrontendLibs(array $args = [], string $string = ''): string
	{
		$namespace = $this->getNamespace($args);

		return str_replace(
			'eightshift-frontend-libs',
			$namespace,
			$string
		);
	}

	/**
	 * Replace project file name
	 *
	 * @param array  $args CLI args array.
	 * @param string $string Full class as a string.
	 *
	 * @return string
	 */
	public function renameProjectName(array $args = [], string $string = ''): string
	{
		$projectName = 'eightshift-boilerplate';

		if (function_exists('\add_action')) {
			$projectName = basename(dirname(__DIR__, 5));
		}

		if (isset($args['project_name'])) {
			$projectName = $args['project_name'];
		}

		return str_replace(
			'eightshift-boilerplate',
			$projectName,
			$string
		);
	}

	/**
	 * Replace project file type
	 *
	 * @param array  $args CLI args array.
	 * @param string $string Full class as a string.
	 *
	 * @return string
	 */
	public function renameProjectType(array $args = [], string $string = ''): string
	{
		$projectType = 'themes';

		if (function_exists('\add_action')) {
			$projectType = basename(dirname(__DIR__, 6));
		}

		if (isset($args['project_type'])) {
			$projectType = $args['project_type'];
		}

		return str_replace(
			'themes',
			$projectType,
			$string
		);
	}

	/**
	 * Change Class full name
	 *
	 * @param string $className Class Name.
	 * @param string $string Full class as a string.
	 *
	 * @return string
	 */
	public function renameClassName(string $className, string $string): string
	{
		return str_replace($this->getExampleFileName($className), $className, $string);
	}

	/**
	 * Change Class full name with prefix
	 *
	 * @param string $templateName Current template.
	 * @param string $newName New Class Name.
	 * @param string $string Full class as a string.
	 *
	 * @return string
	 */
	public function renameClassNameWithPrefix(string $templateName, string $newName, string $string): string
	{
		return str_replace($this->getExampleFileName($templateName), $newName, $string);
	}

	/**
	 * Get composer from project or lib
	 *
	 * @param array $args CLI args array.
	 *
	 * @return array
	 */
	public function getComposer(array $args = []): array
	{
		if (!isset($args['config_path'])) {
			if (function_exists('\add_action')) {
				$composerPath = $this->getProjectRootPath() . '/composer.json';
			} else {
				$composerPath = $this->getProjectRootPath(true) . '/composer.json';
			}
		} else {
			$composerPath = $args['config_path'];
		}

		$composerFile = file_get_contents($composerPath);

		if ($composerFile === false) {
			self::cliError("The composer on {$composerPath} path seems to be missing.");
		}

		return json_decode((string)$composerFile, true);
	}

	/**
	 * Get composers defined namespace
	 *
	 * @param array $args CLI args array.
	 *
	 * @return string
	 */
	public function getNamespace(array $args = []): string
	{
		$namespace = '';

		if (isset($args['namespace'])) {
			$namespace = $args['namespace'];
		}

		if (empty($namespace)) {
			$composer = $this->getComposer($args);

			$namespace = rtrim($this->arrayKeyFirstChild($composer['autoload']['psr-4']), '\\');
		}

		return $namespace;
	}

	/**
	 * Array_key_first polyfill function
	 *
	 * @param array $array Array to search.
	 *
	 * @return string
	 */
	public function arrayKeyFirstChild(array $array): string
	{
		foreach ($array as $key => $unused) {
			return $key;
		}

		return '';
	}

	/**
	 * Get composers defined vendor prefix
	 *
	 * @param array $args CLI args array.
	 *
	 * @return string
	 */
	public function getVendorPrefix(array $args = []): string
	{
		$vendorPrefix = '';

		if (isset($args['vendor_prefix'])) {
			$vendorPrefix = $args['vendor_prefix'];
		}

		if (empty($vendorPrefix)) {
			$composer = $this->getComposer($args);

			$vendorPrefix = $composer['extra']['imposter']['namespace'] ?? 'EightshiftLibs';
		}

		return $vendorPrefix;
	}

	/**
	 * Convert user input string to slug safe format
	 *
	 * Convert _ to -, empty space to - and convert everything to lowercase.
	 *
	 * @param string $string String to convert.
	 *
	 * @return string
	 */
	public function prepareSlug(string $string): string
	{
		if (strpos($string, ' ') !== false) {
			$string = strtolower($string);
		}

		return str_replace('_', '-', str_replace(' ', '-', $string));
	}

	/**
	 * Loop array of classes and output the commands
	 *
	 * @param array $items Array of classes.
	 * @param bool  $run Run or log output.
	 *
	 * @return void
	 */
	public function getEvalLoop(array $items = [], bool $run = false): void
	{
		foreach ($items as $item) {
			try {
				$reflectionClass = new \ReflectionClass($item);
			} catch (\ReflectionException $e) {
				exit("{$e->getCode()}: {$e->getMessage()}");
			}

			$class = $reflectionClass->newInstanceArgs(['null']);

			if (method_exists($class, 'getCommandName')) {
				if (!$run) {
					\WP_CLI::log("wp eval-file bin/cli.php {$class->getCommandName()} --skip-wordpress");
				} else {
					\WP_CLI::runcommand("eval-file bin/cli.php {$class->getCommandName()} --skip-wordpress");
				}
			}
		}
	}

	/**
	 * Run reset command in develop mode only
	 *
	 * @return void
	 */
	public function runReset(): void
	{
		$reset = new CliReset('');
		\WP_CLI::runcommand("eval-file bin/cli.php {$reset->getCommandName()} --skip-wordpress");
	}

	/**
	 * Returns projects root folder based on the environment
	 *
	 * @param bool $isDev Returns path based on the env.
	 *
	 * @return string
	 */
	public function getProjectRootPath(bool $isDev = false): string
	{
		$output = dirname(__DIR__, 5);

		if ($isDev) {
			$output = dirname(__DIR__, 2);
		}

		return $output;
	}

	/**
	 * Returns projects root where config is installed based on the environment
	 *
	 * @param bool $isDev Returns path based on the env.
	 *
	 * @return string
	 */
	public function getProjectConfigRootPath(bool $isDev = false): string
	{
		$output = dirname(__DIR__, 8);

		if ($isDev) {
			$output = dirname(__DIR__, 2);
		}

		return $output;
	}

	/**
	 * Returns Eightshift frontend libs path
	 *
	 * @param string $path Additional path.
	 *
	 * @return string
	 */
	public function getFrontendLibsPath(string $path = ''): string
	{
		return "{$this->getProjectRootPath()}/node_modules/@eightshift/frontend-libs/{$path}";
	}

	/**
	 * Returns Eightshift libs path
	 *
	 * @param string $path Additional path.
	 *
	 * @return string
	 */
	public function getLibsPath(string $path = ''): string
	{
		return "{$this->getProjectRootPath()}/vendor/infinum/eightshift-libs/{$path}";
	}

	/**
	 * Returns Eightshift frontend libs blocks init path.
	 *
	 * @return string
	 */
	public function getFrontendLibsBlockPath(): string
	{
		return $this->getFrontendLibsPath('blocks/init');
	}

	/**
	 * Full blocks files list used for renaming
	 *
	 * @param string $name Block name.
	 *
	 * @return array
	 */
	public function getFullBlocksFiles(string $name): array
	{
		return [
			"{$name}.php",
			"{$name}-block.js",
			"{$name}-hooks.js",
			"{$name}-transforms.js",
			"{$name}.js",
			"docs/story.js",
			"components/{$name}-editor.js",
			"components/{$name}-toolbar.js",
			"components/{$name}-options.js",
		];
	}

	/**
	 * Check and prepare default value for skip_existing arg.
	 *
	 * @param array $args Optional arguments.
	 *
	 * @return boolean
	 */
	public function getSkipExisting(array $args): bool
	{
		return isset($args['skip_existing']) ? (bool) $args['skip_existing'] : false;
	}

	/**
	 * Prepare Command Doc for output
	 *
	 * @param array $docs Command docs array.
	 * @param array $docsGlobal Global docs array.
	 *
	 * @throws \RuntimeException Error in case the shortdesc is missing in command docs.
	 *
	 * @return array
	 */
	public function prepareCommandDocs(array $docs, array $docsGlobal): array
	{
		$shortdesc = $docs['shortdesc'] ?? '';

		if (! $shortdesc) {
			throw new \RuntimeException('CLI Short description is missing.');
		}

		$synopsis = $docs['synopsis'] ?? [];

		return [
			'shortdesc' => $shortdesc,
			'synopsis' => array_merge(
				$docsGlobal['synopsis'],
				$synopsis
			)
		];
	}

	/**
	 * Manually prepare arguments to pass to runcommand method.
	 *
	 * @param array $args Array of arguments.
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
	 * WP CLI error logging helper
	 *
	 * A wrapper for the WP_CLI::error with error handling.
	 *
	 * @param string $errorMessage Error message to log in the CLI.
	 *
	 * @return void
	 */
	public static function cliError(string $errorMessage): void
	{
		try {
			\WP_CLI::error($errorMessage);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}
	}
}
