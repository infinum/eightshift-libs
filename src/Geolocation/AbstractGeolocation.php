<?php

/**
 * File that holds base abstract class for geolocation.
 *
 * @package EightshiftLibs\Geolocation
 */

declare(strict_types=1);

namespace EightshiftLibs\Geolocation;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Services\ServiceInterface;
use Throwable;

/**
 * Geolocation
 */
abstract class AbstractGeolocation implements ServiceInterface
{
	/**
	 * Internal countries list stored in a variable for caching.
	 *
	 * @var array<string>
	 */
	private $countries = [];

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
	 * Tooggle geolocation usage based on this flag.
	 *
	 * @return boolean
	 */
	public function useGeolocation(): bool
	{
		return true;
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

		\ob_start();

		\setcookie(
			$cookieName,
			$this->getGeolocation(),
			\time() + \DAY_IN_SECONDS,
			'/'
		);

		\ob_get_clean();
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
					'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR',
					'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK',
					'SI', 'ES', 'SE', 'AL', 'AD', 'AM', 'BY', 'BA', 'FO', 'GE', 'GI', 'IS',
					'IM', 'XK', 'LI', 'MK', 'MD', 'MC', 'NO', 'RU', 'SM', 'RS', 'CH', 'TR',
					'UA', 'GB', 'VA',
				],
			],
			[
				'label' => \__('European Union', 'eightshift-libs'),
				'value' => 'european-union',
				'group' => [
					'BE', 'EL', 'LT', 'PT', 'BG', 'ES', 'LU', 'RO', 'CZ',
					'FR', 'HU', 'SI', 'DK', 'HR', 'MT', 'SK', 'DE', 'IT',
					'NL', 'FI', 'EE', 'CY', 'AT', 'SE', 'IE', 'LV', 'PL',
				],
			],
			[
				'label' => \__('Ex Yugoslavia', 'eightshift-libs'),
				'value' => 'ex-yugoslavia',
				'group' => [
					'HR', 'RS', 'BA', 'ME', 'SI', 'MK'
				],
			],
		];

		// Save to internal cache so we don't read manifest all the time.
		if (!$this->countries) {
			$this->countries = Components::getManifestDirect(__DIR__);
		}

		foreach ($this->countries as $country) {
			$code = $country['Code'];

			$output[] = [
				'label' => $country['Name'],
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
	 * Gets the 2-digit location code provided by the project.
	 *
	 * @return string
	 */
	private function getGeolocation(): string
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
			try {
				$phar = $this->getGeolocationPharLocation();

				// Get data from the local DB.
				require_once $phar;

				$db = $this->getGeolocationDbLocation();

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
