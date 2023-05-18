<?php

/**
 * Abstract class that holds all methods for WPCLI options.
 *
 * @package EightshiftLibs\Cli
 */

declare(strict_types=1);

namespace EightshiftLibs\Cli;

use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Helpers\Components;
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
                    'options' => [
                        'true',
                        'false',
                    ]
                ],
            ],
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
            self::cliError("The class '{$className}' is not callable. Make sure the command class has an __invoke method.");
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
        $typeOutput = $args['typeOutput'] ?? 'service class';

        // Set optional arguments.
        $skipExisting = $this->getSkipExisting($args);

        // Set output file path.
        $destinationFile = Components::joinPaths([$destination, $fileName]);

        // Bailout if file already exists.
        if (\file_exists($destinationFile) && $skipExisting === false) {
            self::cliError(
                \sprintf(
                    "%s file `%s` exist on this path: `%s`. If you want to override the destination folder please use --skip_existing='true' argument.",
                    $typeOutput,
                    $fileName,
                    $this->getShortenCliPathOutput($destinationFile)
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
            self::cliError(
                \sprintf(
                    "%s file `%s` couldn't be created on this path `%s`. There was an unknown error.",
                    $typeOutput,
                    $fileName,
                    $this->getShortenCliPathOutput($destinationFile)
                )
            );
        }

        $fp = \fopen($destinationFile, "wb");

        // Write and close.
        \fwrite($fp, $this->fileContents);
        \fclose($fp);

        if (!$groupOutput) {
            // Return success.
            if ($skipExisting) {
                WP_CLI::success(
                    \sprintf(
                        "`%s` renamed at `%s`.",
                        $fileName,
                        $this->getShortenCliPathOutput($destinationFile)
                    )
                );
            } else {
                WP_CLI::success(
                    \sprintf(
                        "`%s` created at `%s`.",
                        $fileName,
                        $this->getShortenCliPathOutput($destinationFile)
                    )
                );
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
     * Replace namespace EightshiftBoilerplateVendor\ in class
     *
     * @param array<string, mixed> $args CLI args array.
     *
     * @return AbstractCli Current CLI class.
     */
    public function renameNamespace(array $args = []): self
    {
        $output = $this->fileContents;
        $namespace = $this->getNamespace($args);
        $vendorPrefix = $this->getVendorPrefix($args);

        if (\getenv('ES_TEST')) {
            $output = \str_replace(
                'namespace EightshiftBoilerplate\\',
                "namespace {$namespace}\\",
                $output
            );
        } else {
            $output = \str_replace(
                "namespace {$vendorPrefix}\EightshiftBoilerplate\\",
                "namespace {$namespace}\\",
                $output
            );
        }

        $this->fileContents = $output;

        return $this;
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
        $output = $this->fileContents;

        $vendorPrefix = $this->getVendorPrefix($args);
        $namespace = $this->getNamespace($args);

        $prefixUse = 'use';
        $prefixPackage = '@package';

        if (\getenv('ES_TEST')) {
            $output = \str_replace(
                "{$prefixUse} EightshiftBoilerplate\\",
                "{$prefixUse} {$namespace}\\",
                $output
            );
        } else {
            $output = \str_replace(
                "{$prefixUse} EightshiftBoilerplateVendor\\",
                "{$prefixUse} {$vendorPrefix}\\",
                $output
            );

            $output = \str_replace(
                "{$prefixUse} {$vendorPrefix}\EightshiftBoilerplate\\",
                "{$prefixUse} {$namespace}\\",
                $output
            );
        }

        $output = \str_replace(
            "{$prefixPackage} EightshiftBoilerplate",
            "{$prefixPackage} {$namespace}",
            $output
        );

        $this->fileContents = $output;

        return $this;
    }

    /**
     * Replace use in frontend libs views.
     *
     * @param array<string, mixed> $args CLI args array.
     *
     * @return AbstractCli Current CLI class.
     */
    public function renameUseFrontendLibs(array $args = []): self
    {
        $output = $this->fileContents;

        $vendorPrefix = $this->getVendorPrefix($args);
        $namespace = $this->getNamespace($args);

        $prefixUse = 'use';
        $prefixPackage = '@package';

        $output = \str_replace(
            "{$prefixUse} EightshiftBoilerplateVendor\\",
            "{$prefixUse} {$vendorPrefix}\\",
            $output
        );

        $output = \str_replace(
            "{$prefixUse} EightshiftBoilerplate\\",
            "{$prefixUse} {$namespace}\\",
            $output
        );

        $output = \str_replace(
            "{$prefixPackage} EightshiftBoilerplate",
            "{$prefixPackage} {$namespace}",
            $output
        );

        $this->fileContents = $output;

        return $this;
    }

    /**
     * Replace text domain in class
     *
     * @param array<string, mixed> $args CLI args array.
     *
     * @return AbstractCli Current CLI class.
     */
    public function renameTextDomain(array $args = []): self
    {
        $namespace = Components::camelToKebabCase($this->getNamespace($args));

        $this->fileContents = \str_replace(
            'eightshift-libs',
            $namespace,
            $this->fileContents
        );

        return $this;
    }

    /**
     * Replace text domain in class for frontend libs
     *
     * @param array<string, mixed> $args CLI args array.
     *
     * @return AbstractCli Current CLI class.
     */
    public function renameTextDomainFrontendLibs(array $args = []): self
    {
        $namespace = Components::camelToKebabCase($this->getNamespace($args));

        $this->fileContents = \str_replace(
            'eightshift-frontend-libs',
            $namespace,
            $this->fileContents
        );

        return $this;
    }

    /**
     * Replace project file name
     *
     * @param array<string, mixed> $args CLI args array.
     *
     * @return AbstractCli Current CLI class.
     */
    public function renameProjectName(array $args = []): self
    {
        $projectName = 'eightshift-boilerplate';

        // Don't use this option on the tests.
        if (!\getenv('ES_TEST')) {
            $projectName = \basename(Components::getProjectPaths('root'));
        }

        if (isset($args['project_name'])) {
            $projectName = $args['project_name'];
        }

        $this->fileContents = \str_replace(
            'eightshift-boilerplate',
            $projectName,
            $this->fileContents
        );

        return $this;
    }

    /**
     * Replace project file type
     *
     * @param array<string, mixed> $args CLI args array.
     *
     * @return AbstractCli Current CLI class.
     */
    public function renameProjectType(array $args = []): self
    {
        $projectType = 'themes';

        // Don't use this option on the tests.
        if (!\getenv('ES_TEST')) {
            $projectType = \basename(Components::getProjectPaths('wpContent'));
        }

        if (isset($args['project_type'])) {
            $projectType = $args['project_type'];
        }

        $this->fileContents = \str_replace(
            'themes',
            $projectType,
            $this->fileContents
        );

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
     * @param array<string, mixed> $args CLI args array.
     *
     * @return array<string, mixed>
     */
    public function getComposer(array $args = []): array
    {
        if (!isset($args['config_path'])) {
            $composerPath = Components::getProjectPaths('root', 'composer.json');
        } else {
            $composerPath = $args['config_path'];
        }

        $composerFile = \file_get_contents($composerPath);

        if ($composerFile === false) {
            self::cliError("The composer on {$composerPath} path seems to be missing.");
        }

        return \json_decode((string)$composerFile, true);
    }

    /**
     * Get composers defined namespace
     *
     * @param array<string, mixed> $args CLI args array.
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

            $namespace = \rtrim(\array_key_first($composer['autoload']['psr-4']), '\\');
        }

        return $namespace;
    }

    /**
     * Get composers defined vendor prefix
     *
     * @param array<string, mixed> $args CLI args array.
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
     * Loop array of classes and output the commands
     *
     * @param class-string[] $items Array of classes.
     * @param array<string, mixed> $args CLI command args.
     *
     * @return void
     * @throws ReflectionException Reflection exception.
     */
    public function getEvalLoop(array $items = [], array $args = []): void
    {
        foreach ($items as $item) {
            $reflectionClass = new ReflectionClass($item);

            $class = $reflectionClass->newInstanceArgs(['null']);

            if (\method_exists($class, 'getCommandName') && \method_exists($class, 'getCommandParentName')) {
                WP_CLI::runcommand("{$this->commandParentName} {$class->getCommandParentName()} {$class->getCommandName()} {$this->prepareArgsManual($args)}");
            }
        }
    }

    /**
     * Full blocks files list used for renaming
     *
     * @param string $name Block name.
     *
     * @return string[]
     */
    public function getFullBlocksFiles(string $name): array
    {
        $ds = \DIRECTORY_SEPARATOR;
        return [
            "{$name}.php",
            "{$name}-block.js",
            "{$name}-hooks.js",
            "{$name}-transforms.js",
            "{$name}.js",
            "docs{$ds}story.js",
            "components{$ds}{$name}-editor.js",
            "components{$ds}{$name}-toolbar.js",
            "components{$ds}{$name}-options.js",
        ];
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
        return isset($args['skip_existing']) && $args['skip_existing'];
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

    /**
     * Recursive copy helper
     *
     * @link https://stackoverflow.com/a/7775949/629127
     *
     * @param string $source Source path.
     * @param string $destination Destination path.
     *
     * @throws InvalidBlock If block file is missing.
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
            throw InvalidBlock::missingFileException($source);
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

        /*
          ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
        ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
        ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▓▓▓▓▓▓▒▒▒▓▓▒▒▒▒▒▓▓▓▓▒▒▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▓▓▓▓▓▓▒▒▒▒▒▓▓▓▓▓▓▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▓▓▓▓▒▒▒▓▓▓▓▓▓▓▓▒▒▒▒
        ▒▒▒▒▒▒▒▒▒▒▒█████████▒▒▒▒▒▒▒▒▒▒▒▒▒▒█████████▒▒▒▒▒▒▒▒▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒▓▓▒▒▒▓▓▒▒▒▒▒▒▒▒▒▓▓▒▒▒▓▓▒▒▒▒▒▒▓▓▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▒▒▒▒▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒
        ▒▒▒▒▒▒▒▒████▒▒▒▒▒▒▒████▒▒▒▒▒▒▒▒████▒▒▒▒▒▒▒████▒▒▒▒▒▒▒▒▒▒▒▓▓▓▓▒▒▒▒▒▓▓▒▒▒▓▓▒▒▓▓▓▓▒▒▒▓▓▓▓▓▓▓▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒▒▓▓▓▓▒▒▒▒▒▓▓▓▓▓▓▓▒▒▒▓▓▒▒▒▓▓▓▓▒▒▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒
        ▒▒▒▒▒▒███▒▒▒▒▒▒▒▒▒▒▒▒▒███▒▒▒▒███▒▒▒▒▒▒▒▒▒▒▒▒▒███▒▒▒▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒▓▓▒▒▒▓▓▒▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒▒▒▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▒▒▒▒▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒
        ▒▒▒▒▒███▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒█████▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒███▒▒▒▒▒▒▒▒▓▓▓▓▓▓▒▒▒▓▓▒▒▒▒▒▓▓▓▓▒▒▒▒▒▓▓▒▒▒▓▓▒▒▒▒▒▒▓▓▒▒▒▒▒▒▓▓▓▓▓▓▒▒▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▒▒▒▒▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒
        ▒▒▒▒███▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒███▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒███▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
        ▒▒▒▒▒███▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒█████▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒███▒▒▒▒▒▒▒▒▓▓▓▓▓▓▒▒▒▒▒▓▓▓▓▓▓▒▒▒▓▓▒▒▒▒▒▒▓▓▒▒▒▓▓▒▒▒▒▓▓▒▒▒▓▓▒▒▒▓▓▓▓▓▓▓▓▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
        ▒▒▒▒▒▒███▒▒▒▒▒▒▒▒▒▒▒▒▒███▒▒▒███▒▒▒▒▒▒▒▒▒▒▒▒▒▒███▒▒▒▒▒▒▒▒▒▓▓▒▒▒▒▓▓▒▒▒▓▓▒▒▒▒▒▒▒▓▓▒▒▒▒▒▒▓▓▒▒▒▓▓▒▒▓▓▒▒▒▒▒▓▓▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
        ▒▒▒▒▒▒▒▒████▒▒▒▒▒▒▒████▒▒▒▒▒▒▒████▒▒▒▒▒▒▒████▒▒▒▒▒▒▒▒▒▒▒▒▓▓▒▒▒▒▓▓▒▒▒▓▓▓▓▒▒▒▒▒▒▒▓▓▒▒▓▓▒▒▒▒▒▓▓▓▓▒▒▒▒▒▒▒▓▓▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
        ▒▒▒▒▒▒▒▒▒▒▒█████████▒▒▒▒▒▒▒▒▒▒▒▒▒█████████▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▓▓▒▒▒▒▓▓▒▒▒▓▓▒▒▒▒▒▒▒▒▒▓▓▒▒▓▓▒▒▒▒▒▓▓▒▒▓▓▒▒▒▒▒▓▓▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
        ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▓▓▓▓▓▓▒▒▒▒▒▓▓▓▓▓▓▒▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒▓▓▒▒▒▒▓▓▒▒▒▓▓▒▒▒▒▒▒▓▓▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
        ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒
          ▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒

        █ = red, ▓ = current color, ▒ = transparent
        */
        $this->cliLog($this->prepareLongDesc("

		%w╭──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────╮
		│                                                                                                                                                      │
		│ %R                                                  %n    ▟██████   ██    ▟█████     ██   ██   ████████    ▟█████▙   ██    ██   ██   ▟██████  ████████   %w│
		│ %R        ▟█████████▙            ▟█████████▙        %n    ██        ██   ██          ██   ██      ██      ██▘        ██    ██   ██   ██          ██      %w│
		│ %R     ▟███▛      ▜███▙       ▟████▛     ▜████▙     %n    █████     ██   ██   ████   ███████      ██       ▜████▖    ████████   ██   ████        ██      %w│
		│ %R   ▟███▛           ▜███▙  ▟███▛           ▜███    %n    ██        ██   ██     ██   ██   ██      ██           ▝██   ██    ██   ██   ██          ██      %w│
		│ %R  ▟███▛              ▜█████▛               ▜███   %n    ▜██████   ██    ▜█████▛    ██   ██      ██      ▜█████▛    ██    ██   ██   ██          ██      %w│
		│ %R  ███▌                ▐███▌                 ▐███  %n                                                                                                   %w│
		│ %R  ▜███▙               █████▙               ▟███▛  %n    ███████▙    ▟██████   ██      ██   ██   ▟█▛   ██   ████████                                    %w│
		│ %R   ▜███▙           ▟███▛ ▜███▙            ▟███▛   %n    ██     ██   ██        ▜█▖    ▗█▛   ██  ▟█▛    ██      ██                                       %w│
		│ %R     ▜████▙     ▟████▛     ▜████▙     ▟████▛      %n    ██     ██   ████       ▜█▖  ▗█▛    █████      ██      ██                                       %w│
		│ %R         ▜███████▛           ▜█████████▛          %n    ██     ██   ██          ▜█▖▗█▛     ██  ▜█▙    ██      ██                                       %w│
		│ %R                                                  %n    ███████▛    ▜██████      ▜██▛      ██   ▜█▙   ██      ██                                       %w│
		│                                                                                                                                                      │
		├──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
		│                                                                                                                                                      │
		│  %RThank you for using Eightshift DevKit!%w                                                                                                            │
		%w│                                                                                                                                                      │
		│  %nDocumentation can be found on %bhttps://eightshift.com/%w                                                                                               │
		│                                                                                                                                                      │
		╰──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────╯%n

        "), 'mixed');
    }
}
