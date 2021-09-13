<?php

/**
 * Class that registers WPCLI command for Rest Routes.
 *
 * @package EightshiftLibs\Rest\Routes
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest\Routes;

use EightshiftLibs\Cli\AbstractCli;

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
	public const OUTPUT_DIR = 'src' . DIRECTORY_SEPARATOR . 'Rest' . DIRECTORY_SEPARATOR . 'Routes';

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

		// If method is invalid throw error.
		if (!isset(self::VERB_ENUM[$method])) {
			\WP_CLI::error("Invalid method: $method, please use one of GET, POST, PATCH, PUT, or DELETE");
		}

		// If slug is empty throw error.
		if (empty($endpointSlug)) {
			\WP_CLI::error("Empty slug provided, please set the slug using --endpoint_slug=\"slug-name\"");
		}

		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->searchReplaceString('/example-route', "/{$endpointSlug}")
			->searchReplaceString('static::READABLE', static::VERB_ENUM[$method])
			->outputWrite(static::OUTPUT_DIR, $className, $assocArgs);
	}
}
