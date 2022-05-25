<?php

/**
 * Class that adds Geolocation detection.
 *
 * @package Geolocation
 */

declare(strict_types=1);

namespace Geolocation;

use EightshiftLibs\Geolocation\AbstractGeolocation;
use Exception;

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
		\add_filter('init', [$this, 'setLocationCookie']);
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
		$path = __DIR__ . '/geoip2.phar';

		if (!\file_exists($path)) {
			// translators: %s will be replaced with the phar location.
			return new Exception(\sprintf(\esc_html__('Missing Geolocation phar on this locaiton %s', 'eightshift-libs'), $path));
		}

		return $path;
	}

	/**
	 * Get geolocation database location.
	 *
	 * @return string
	 */
	public function getGeolocationDbLocation(): string
	{
		$path = __DIR__ . '/GeoLite2-Country.mmdb';

		if (!\file_exists($path)) {
			// translators: %s will be replaced with the database location.
			return new Exception(\sprintf(\esc_html__('Missing Geolocation database on this locaiton %s', 'eightshift-libs'), $path));
		}

		return $path;
	}
}
