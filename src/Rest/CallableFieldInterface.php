<?php

/**
 * File containing Rest/Api field callable interface
 *
 * @package EightshiftLibs\Rest
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest;

/**
 * Interface that adds/extends fields in routes.
 */
interface CallableFieldInterface
{

	/**
	 * Method that returns rest response for custom fields
	 *
	 * @param object|array $object Post or custom post type object of the request.
	 * @param string       $attr Rest field/attr string identifier from the second parameter
	 *                           of your register_rest_field() declaration.
	 * @param object       $request Full request payload - as a WP_REST_Request object.
	 * @param string       $objectType The object type which the field is registered against.
	 *                                 Typically first parameter of your register_rest_field() declaration.
	 *
	 * @return mixed If response generated an error, WP_Error, if response
	 *               is already an instance, WP_HTTP_Response, otherwise
	 *               returns a new WP_REST_Response instance.
	 */
	public function fieldCallback($object, string $attr, object $request, string $objectType);
}
