<?php

/**
 * The class file that holds abstract class for REST fields registration.
 *
 * @package EightshiftLibs\Rest
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest\Fields;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract base field class
 */
abstract class AbstractField implements ServiceInterface
{
	/**
	 * A register method holds register_rest_route function to register or override api field.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('rest_api_init', [$this, 'fieldRegisterCallback']);
	}

	/**
	 * Method that register rest field that is used inside rest_api_init hook.
	 *
	 * @param \WP_REST_Server $wpRestServer Server object.
	 *
	 * @return void
	 */
	public function fieldRegisterCallback(\WP_REST_Server $wpRestServer): void
	{
		\register_rest_field(
			$this->getObjectType(),
			$this->getFieldName(),
			$this->getCallbackArguments()
		);
	}

	/**
	 * Method that returns field object type
	 *
	 * Object(s) the field is being registered to, "post"|"term"|"comment" etc.
	 *
	 * @return string|string[]
	 */
	abstract protected function getObjectType();

	/**
	 * Get the name of the field you want to register or override
	 *
	 * @return string The attribute name.
	 */
	abstract protected function getFieldName(): string;

	/**
	 * Get callback arguments array
	 *
	 * @return array<string, mixed> An array of arguments used to handle the registered field.
	 */
	abstract protected function getCallbackArguments(): array;
}
