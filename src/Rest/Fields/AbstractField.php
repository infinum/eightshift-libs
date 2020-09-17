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
	 * @return void
	 */
	public function fieldRegisterCallback(): void
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
	 * @return string|array
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
	 * @return array Either an array of options for the endpoint, or an array of arrays for multiple methods.
	 */
	abstract protected function getCallbackArguments(): array;
}
