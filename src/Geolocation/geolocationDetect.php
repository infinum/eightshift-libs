<?php

/**
 * File used in combination with WP-Rocket cache plugin to provide and set cookies.
 *
 * @package EightshiftLibs\Geolocation;
 */

declare(strict_types=1);

/**
 * Set geolocation cookie.
 *
 * @param string $name Cookie name.
 * @param string $pharLocation Geolocation phar absolute path.
 * @param string $dbLocation Geolocation db absolute path.
 * @param string $ipAddr Manual IP addres for testing.
 * @param int $expires Cookie expiration time in secounds. Default: current time + 1 day.
 * @param string $path Cookie path.
 *
 * @return void
 */
function setLocationCookie(
	string $name,
	string $pharLocation,
	string $dbLocation,
	string $ipAddr = '',
	int $expires = 0,
	string $path = '/'
): void {
	if (isset($_COOKIE[$name])) {
		return;
	}

	$location = getGeolocation($pharLocation, $dbLocation, $ipAddr);

	if (!$expires) {
		$expires = time() + DAY_IN_SECONDS;
	}

	try {
		setcookie($name, $location, $expires, $path);

		// Manually set cookie name in $_COOKIE global for cache plugin to work.
		$_COOKIE[$name] = $location;
	} catch (Exception $exception) {
		/*
		* The getGeolocation will throw an error if the phar or geo db files are missing,
		* but if we threw an exception here, that would break the execution of the WP app.
		* This way we'll log the exception, but the site should work fine without setting
		* the cookie.
		*/
		error_log("Error code: {$exception->getCode()}, with message: {$exception->getMessage()}"); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		return;
	}
}
/**
 * Gets the 2-digit location code provided by the project.
 *
 * @param string $pharLocation Absolute location of your phar file.
 * @param string $dbLocation Absolute location of your db file.
 * @param string $ipAddr Manual IP set.
 *
 * @throws Exception Throws exception in case the geolocation phar or db file are missing.
 *
 * @return string
 */
function getGeolocation(string $pharLocation, string $dbLocation, string $ipAddr = ''): string
{
	// Find user's remote address.
	if (isset($_SERVER['REMOTE_ADDR']) && !$ipAddr) {
		$ipAddr = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP); //phpcs:ignore
	}

	// Skip if empty for some reason or if you are on local computer.
	if ($ipAddr !== '127.0.0.1' && $ipAddr !== '::1' && !empty($ipAddr)) {
		$phar = $pharLocation;

		if (!file_exists($phar)) {
			// translators: %s will be replaced with the phar location.
			throw new Exception(sprintf(esc_html__('Missing Geolocation phar on this location %s', 'eightshift-libs'), $phar));
		}

		$db = $dbLocation;

		if (!file_exists($db)) {
			// translators: %s will be replaced with the database location.
			throw new Exception(sprintf(esc_html__('Missing Geolocation database on this location %s', 'eightshift-libs'), $db));
		}

		try {
			// Get data from the local DB.
			require_once $phar;

			// phpcs:disable
			$reader = new \GeoIp2\Database\Reader($db); // @phpstan-ignore-line
			// phpcs:enable

			$record = $reader->country($ipAddr); // @phpstan-ignore-line
			$cookieCountry = $record->country;

			if (!empty($cookieCountry)) {
				return strtoupper($cookieCountry->isoCode);
			}

			return '';
		} catch (Throwable $th) {
			return 'ERROR: ' . $th->getMessage();
		}
	} else {
		return 'localhost';
	}
}
