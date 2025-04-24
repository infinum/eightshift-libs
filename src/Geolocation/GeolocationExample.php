<?php

/**
 * Class that adds Geolocation detection.
 *
 * @package %g_namespace%\Geolocation
 */

declare(strict_types=1);

namespace %g_namespace%\Geolocation;

use %g_use_libs%\Geolocation\AbstractGeolocation;
use %g_use_libs%\Helpers\Helpers;

/**
 * Class Geolocation
 */
class GeolocationExample extends AbstractGeolocation
{
	/**
	 * Register all the hooks
	 */
	public function register(): void
	{
		\add_action('init', [$this, 'setLocationCookie']);
	}

	/**
	 * Get geolocation cookie name.
	 *
	 * @return string
	 */
	public function getGeolocationCookieName(): string
	{
		return '%cookie_name%';
	}

	/**
	 * Get geolocation executable phar location.
	 *
	 * @return string
	 */
	public function getGeolocationPharLocation(): string
	{
		return Helpers::getEightshiftOutputPath('geoip2.phar');
	}

	/**
	 * Get geolocation database location.
	 *
	 * @return string
	 */
	public function getGeolocationDbLocation(): string
	{
		return Helpers::getEightshiftOutputPath('GeoLite2-Country.mmdb');
	}
}
