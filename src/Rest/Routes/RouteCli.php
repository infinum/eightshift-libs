<?php

/**
 * Class that registers WPCLI command for Rest Routes.
 *
 * @package EightshiftLibs\Rest\Routes
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest\Routes;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Helpers\Components;
use WP_CLI;

/**
 * Class RouteCli
 */
class RouteCli extends AbstractCli
{
	/**
	 * Route method enum.
	 *
	 * @var array<string, string>
	 */
	public const VERB_ENUM = [
		'GET' => 'static::READABLE',
		'POST' => 'static::CREATABLE',
		'PATCH' => 'static::EDITABLE',
		'PUT' => 'static::UPDATEABLE',
		'DELETE' => 'static::DELETABLE',
	];

	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliCreate::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'rest_route';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'endpoint_slug' => 'test',
			'method' => 'get',
		];
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, mixed>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Create REST-API route service class.',
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
					'optional' => true,
					'default' => $this->getDefaultArg('method'),
					'options' => [
						'GET',
						'POST',
						'PATCH',
						'PUT',
						'DELETE',
					],
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used to create REST-API service class to register custom route.

				## EXAMPLES

				# Create service class:
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --endpoint_slug='test-route'

				## RESOURCES

				Service class will be created from this example:
				https://github.com/infinum/eightshift-libs/blob/develop/src/Rest/Routes/RouteExample.php
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$this->getIntroText($assocArgs);

		// Get Props.
		$endpointSlug = $this->prepareSlug($this->getArg($assocArgs, 'endpoint_slug'));
		$method = \strtoupper($this->getArg($assocArgs, 'method'));

		// Get full class name.
		$className = $this->getFileName($endpointSlug);
		$className = $className . $this->getClassShortName();


		// If method is invalid throw error.
		if (!isset(self::VERB_ENUM[$method])) {
			WP_CLI::error("Invalid method: $method, please use one of GET, POST, PATCH, PUT, or DELETE");
		}


		// Read the template contents, and replace the placeholders with provided variables.
		$this->getExampleTemplate(__DIR__, $this->getClassShortName())
			->renameClassNameWithPrefix($this->getClassShortName(), $className)
			->renameNamespace($assocArgs)
			->renameUse($assocArgs)
			->searchReplaceString($this->getArgTemplate('endpoint_slug'), $endpointSlug)
			->searchReplaceString("'{$this->getArgTemplate('method')}'", static::VERB_ENUM[$method])
			->outputWrite(Components::getProjectPaths('srcDestination', 'Rest' . \DIRECTORY_SEPARATOR . 'Routes'), "{$className}.php", $assocArgs);
	}
}
