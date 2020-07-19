<?php
/**
 * File containing Request type interface
 *
 * @package EightshiftLibs\Rest
 */

declare( strict_types=1 );

namespace EightshiftLibs\Rest;

/**
 * Route interface that adds routes
 */
interface RouteInterface {

  /**
   * Alias for GET transport method.
   *
   * @var string
   */
  const READABLE = 'GET';

  /**
   * Alias for POST transport method.
   *
   * @var string
   */
  const CREATABLE = 'POST';

    /**
   * Alias for PATCH transport method.
   *
   * @var string
   */
  const EDITABLE = 'PATCH';

  /**
   * Alias for PUT transport method.
   *
   * @var string
   */
  const UPDATEABLE = 'PUT';

  /**
   * Alias for DELETE transport method.
   *
   * @var string
   */
  const DELETABLE = 'DELETE';
}
