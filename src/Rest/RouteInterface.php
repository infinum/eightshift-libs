<?php

/**
 * File containing Request type interface
 *
 * @package EightshiftLibs\Rest
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest;

/**
 * Interface that adds aliases of HTTP verbs.
 */
interface RouteInterface
{
	/**
	 * Alias for GET transport method.
	 *
	 * @var string
	 */
	public const READABLE = 'GET';

	/**
	 * Alias for POST transport method.
	 *
	 * @var string
	 */
	public const CREATABLE = 'POST';

	/**
	 * Alias for PATCH transport method.
	 *
	 * @var string
	 */
	public const EDITABLE = 'PATCH';

	/**
	 * Alias for PUT transport method.
	 *
	 * @var string
	 */
	public const UPDATEABLE = 'PUT';

	/**
	 * Alias for DELETE transport method.
	 *
	 * @var string
	 */
	public const DELETABLE = 'DELETE';
}
