<?php

/**
 * File that holds base abstract class for geolocation.
 *
 * @package EightshiftLibs\Geolocation
 */

declare(strict_types=1);

namespace EightshiftLibs\Geolocation;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Services\ServiceInterface;
use Exception;
use Throwable;

/**
 * Geolocation
 */
abstract class AbstractGeolocation implements ServiceInterface
{
	/**
	 * Get geolocation cookie name.
	 *
	 * @return string
	 */
	abstract public function getGeolocationCookieName(): string;

	/**
	 * Get geolocation executable phar location.
	 *
	 * @return string
	 */
	abstract public function getGeolocationPharLocation(): string;

	/**
	 * Get geolocation database location.
	 *
	 * @return string
	 */
	abstract public function getGeolocationDbLocation(): string;

	/**
	 * Toggle geolocation usage based on this flag.
	 *
	 * @return boolean
	 */
	public function useGeolocation(): bool
	{
		return true;
	}

	/**
	 * Get geolocation expiration time.
	 *
	 * @return int
	 */
	public function getGeolocationExpiration(): int
	{
		return \time() + \DAY_IN_SECONDS;
	}

	/**
	 * Set geolocation cookie.
	 *
	 * @return void
	 */
	public function setLocationCookie(): void
	{
		// Skip admin.
		if (\is_admin()) {
			return;
		}

		// Bailout if not in use.
		if (!$this->useGeolocation()) {
			return;
		}

		$cookieName = $this->getGeolocationCookieName();

		// If the cookie exists, don't set it again.
		if (isset($_COOKIE[$cookieName])) {
			return;
		}

		try {
			$this->setCookie(
				$cookieName,
				$this->getGeolocation(),
				$this->getGeolocationExpiration(),
				'/'
			);
		} catch (Exception $exception) {
			/*
			 * The getGeolocation will throw an error if the phar or geo db files are missing,
			 * but if we threw an exception here, that would break the execution of the WP app.
			 * This way we'll log the exception, but the site should work fine without setting
			 * the cookie.
			 */
			\error_log("Error code: {$exception->getCode()}, with message: {$exception->getMessage()}"); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return;
		}
	}

	/**
	 * Gets additional locations for country list.
	 *
	 * @return array<mixed>
	 */
	public function getAdditionalCountries(): array
	{
		return [];
	}

	/**
	 * Gets an IP address manually. Generally used for development and testing.
	 *
	 * @return string
	 */
	public function getIpAddress(): string
	{
		return '';
	}

	/**
	 * Gets the list of all countries from the manifest.
	 *
	 * @return array<mixed>
	 */
	public function getCountries(): array
	{
		$output = [
			[
				'label' => \__('Europe', 'eightshift-libs'),
				'value' => 'europe',
				'group' => [
					'AT',
					'BE',
					'BG',
					'HR',
					'CY',
					'CZ',
					'DK',
					'EE',
					'FI',
					'FR',
					'DE',
					'GR',
					'HU',
					'IE',
					'IT',
					'LV',
					'LT',
					'LU',
					'MT',
					'NL',
					'PL',
					'PT',
					'RO',
					'SK',
					'SI',
					'ES',
					'SE',
					'AL',
					'AD',
					'AM',
					'BY',
					'BA',
					'FO',
					'GE',
					'GI',
					'IS',
					'IM',
					'XK',
					'LI',
					'MK',
					'MD',
					'MC',
					'NO',
					'RU',
					'SM',
					'RS',
					'CH',
					'TR',
					'UA',
					'GB',
					'VA',
				],
			],
			[
				'label' => \__('European Union', 'eightshift-libs'),
				'value' => 'european-union',
				'group' => [
					'BE',
					'EL',
					'LT',
					'PT',
					'BG',
					'ES',
					'LU',
					'RO',
					'CZ',
					'FR',
					'HU',
					'SI',
					'DK',
					'HR',
					'MT',
					'SK',
					'DE',
					'IT',
					'NL',
					'FI',
					'EE',
					'CY',
					'AT',
					'SE',
					'IE',
					'LV',
					'PL',
				],
			],
			[
				'label' => \__('Ex Yugoslavia', 'eightshift-libs'),
				'value' => 'ex-yugoslavia',
				'group' => [
					'HR',
					'RS',
					'BA',
					'ME',
					'SI',
					'MK'
				],
			],
		];

		$data = Helpers::getGeolocationCountries();

		foreach ($data as $country) {
			$code = $country['Code'] ?? '';

			if (!$code) {
				continue;
			}

			$output[] = [
				'label' => $country['Name'] ?? '',
				'value' => $code,
				'group' => [
					\strtoupper($code),
				],
			];
		}

		// Provide custom countries.
		$additionalLocations = $this->getAdditionalCountries();
		if ($additionalLocations) {
			$output = \array_merge(
				$output,
				$additionalLocations
			);
		}

		return $output;
	}

	/**
	 * Wrapper method for the native PHP setcookie function.
	 *
	 * We are using a wrapper because we cannot easily mock the setcookie function from PHP.
	 * This way, we can just mock our implementation during tests.
	 *
	 * @param string $name Name of cookie.
	 * @param string $value Value to store to cookie.
	 * @param int $expire Expiration time.
	 * @param string $path Path of usage.
	 * @param string $domain Domain of usage.
	 * @param boolean $secure Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client.
	 * @param boolean $httponly When true the cookie will be made accessible only through the HTTP protocol.
	 *
	 * @return bool
	 */
	public function setCookie(
		string $name,
		string $value = "",
		int $expire = 0,
		string $path = "",
		string $domain = "",
		bool $secure = false,
		bool $httponly = false
	): bool {
		return \setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}

	/**
	 * Gets the 2-digit location code provided by the project.
	 *
	 * @return string
	 * @throws Exception Throws exception in case the geolocation phar or db file are missing.
	 */
	public function getGeolocation(): string
	{
		$ipAddr = '';

		// Find user's remote address.
		if (isset($_SERVER['REMOTE_ADDR'])) {
			$ipAddr = \filter_var($_SERVER['REMOTE_ADDR'], \FILTER_VALIDATE_IP); //phpcs:ignore
		}

		if ($this->getIpAddress()) {
			$ipAddr = $this->getIpAddress();
		}

		// Skip if empty for some reason or if you are on local computer.
		if ($ipAddr !== '127.0.0.1' && $ipAddr !== '::1' && !empty($ipAddr)) {
			$phar = $this->getGeolocationPharLocation();

			if (!\file_exists($phar)) {
				// translators: %s will be replaced with the phar location.
				throw new Exception(\sprintf(\esc_html__('Missing Geolocation phar on this location %s', 'eightshift-libs'), $phar));
			}

			$db = $this->getGeolocationDbLocation();

			if (!\file_exists($db)) {
				// translators: %s will be replaced with the database location.
				throw new Exception(\sprintf(\esc_html__('Missing Geolocation database on this location %s', 'eightshift-libs'), $db));
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
					return \strtoupper($cookieCountry->isoCode);
				}

				return '';
			} catch (Throwable $th) {
				return 'ERROR: ' . $th->getMessage();
			}
		} else {
			return 'localhost';
		}
	}
}
