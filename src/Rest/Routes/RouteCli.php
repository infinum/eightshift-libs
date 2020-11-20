<?php

/**
 * Class that registers WPCLI command for Rest Routes.
 *
 * @package EightshiftLibs\Rest\Routes
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest\Routes;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI\ExitException;

/**
 * Class RouteCli
 */
class RouteCli extends AbstractCli
{

	/**
	 * Output dir relative path.
	 *
	 * @var string
	 */
	public const OUTPUT_DIR = 'src/Rest/Routes';

	/**
	 * Route method enum.
	 *
	 * @var array
	 */
	public const VERB_ENUM = [
		'GET' => 'static::READABLE',
		'POST' => 'static::CREATABLE',
		'PATCH' => 'static::EDITABLE',
		'PUT' => 'static::UPDATEABLE',
		'DELETE' => 'static::DELETABLE',
	];

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'create_rest_route';
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
		return [
			'endpoint_slug' => $args[1] ?? 'test',
			'method' => $args[2] ?? 'get',
		];
	}

	/**
	 * Get WPCLI command doc.
	 *
	 * @return array
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Generates REST-API Route in your project.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'endpoint_slug',
					'description' => 'The name of the endpoint slug. Example: test-route.',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'method',
					'description' => 'HTTP verb must be one of: GET, POST, PATCH, PUT, or DELETE.',
					'optional' => false,
				],
			],
		];
	}

	public function __invoke(array $args, array $assocArgs) // phpcs:ignore
	{
		// Get Props.
		$endpointSlug = $this->prepareSlug($assocArgs['endpoint_slug']);
		$method = strtoupper($assocArgs['method']);

		// Get full class name.
		$className = $this->getFileName($endpointSlug);
		$className = $className . $this->getClassShortName();

		// Read the template contents, and replace the placeholders with provided variables.
		try {
			$class = $this->getExampleTemplate(__DIR__, $this->getClassShortName());
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}

		// Replace stuff in file.
		$class = $this->renameClassNameWithPrefix($this->getClassShortName(), $className, $class);
		try {
			$class = $this->renameNamespace($assocArgs, $class);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}

		try {
			$class = $this->renameUse($assocArgs, $class);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}

		$class = str_replace('/example-route', "/{$endpointSlug}", $class);
		$class = str_replace('static::READABLE', static::VERB_ENUM[$method], $class);

		// Output final class to new file/folder and finish.
		try {
			$this->outputWrite(static::OUTPUT_DIR, $className, $class, $assocArgs);
		} catch (ExitException $e) {
			exit("{$e->getCode()}: {$e->getMessage()}");
		}
	}
}
