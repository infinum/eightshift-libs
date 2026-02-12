<?php

/**
 * Tests for AbstractGeolocation class
 *
 * @package EightshiftLibs\Tests\Unit\Geolocation
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Geolocation;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\Geolocation\AbstractGeolocation;
use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * AbstractGeolocationTest class
 */
class AbstractGeolocationTest extends BaseTestCase
{
	/**
	 * Set up before each test
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();
		Monkey\setUp();

		if (!\defined('DAY_IN_SECONDS')) {
			\define('DAY_IN_SECONDS', 86400);
		}
	}

	/**
	 * Tear down after each test
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test that AbstractGeolocation implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$geolocation = new ConcreteGeolocation();

		$this->assertInstanceOf(ServiceInterface::class, $geolocation);
	}

	/**
	 * Test that getGeolocationCookieName returns expected value
	 *
	 * @return void
	 */
	public function testGetGeolocationCookieNameReturnsExpectedValue(): void
	{
		$geolocation = new ConcreteGeolocation();

		$this->assertEquals('test_geolocation_cookie', $geolocation->getGeolocationCookieName());
	}

	/**
	 * Test that getGeolocationPharLocation returns expected value
	 *
	 * @return void
	 */
	public function testGetGeolocationPharLocationReturnsExpectedValue(): void
	{
		$geolocation = new ConcreteGeolocation();

		$this->assertEquals('/path/to/geoip2.phar', $geolocation->getGeolocationPharLocation());
	}

	/**
	 * Test that getGeolocationDbLocation returns expected value
	 *
	 * @return void
	 */
	public function testGetGeolocationDbLocationReturnsExpectedValue(): void
	{
		$geolocation = new ConcreteGeolocation();

		$this->assertEquals('/path/to/GeoLite2-Country.mmdb', $geolocation->getGeolocationDbLocation());
	}

	/**
	 * Test that useGeolocation returns true by default
	 *
	 * @return void
	 */
	public function testUseGeolocationReturnsTrueByDefault(): void
	{
		$geolocation = new ConcreteGeolocation();

		$this->assertTrue($geolocation->useGeolocation());
	}

	/**
	 * Test that getGeolocationExpiration returns time in the future
	 *
	 * @return void
	 */
	public function testGetGeolocationExpirationReturnsTimeInFuture(): void
	{
		$geolocation = new ConcreteGeolocation();

		$expiration = $geolocation->getGeolocationExpiration();

		$this->assertIsInt($expiration);
		$this->assertGreaterThan(\time(), $expiration);
	}

	/**
	 * Test that getAdditionalCountries returns empty array by default
	 *
	 * @return void
	 */
	public function testGetAdditionalCountriesReturnsEmptyArray(): void
	{
		$geolocation = new ConcreteGeolocation();

		$this->assertEquals([], $geolocation->getAdditionalCountries());
	}

	/**
	 * Test that getIpAddress returns empty string by default
	 *
	 * @return void
	 */
	public function testGetIpAddressReturnsEmptyString(): void
	{
		$geolocation = new ConcreteGeolocation();

		$this->assertEquals('', $geolocation->getIpAddress());
	}

	/**
	 * Test that setLocationCookie bails out on admin pages
	 *
	 * @return void
	 */
	public function testSetLocationCookieBailsOutOnAdmin(): void
	{
		Functions\when('is_admin')->justReturn(true);

		$geolocation = new ConcreteGeolocation();
		$geolocation->setLocationCookie();

		// If we got here without error, admin check worked
		$this->assertTrue(true);
	}

	/**
	 * Test that setLocationCookie bails out when geolocation is disabled
	 *
	 * @return void
	 */
	public function testSetLocationCookieBailsOutWhenDisabled(): void
	{
		Functions\when('is_admin')->justReturn(false);

		$geolocation = new ConcreteGeolocationDisabled();
		$geolocation->setLocationCookie();

		// If we got here without error, useGeolocation check worked
		$this->assertTrue(true);
	}

	/**
	 * Test that setLocationCookie bails out when cookie already exists
	 *
	 * @return void
	 */
	public function testSetLocationCookieBailsOutWhenCookieExists(): void
	{
		Functions\when('is_admin')->justReturn(false);

		$_COOKIE['test_geolocation_cookie'] = 'US';

		$geolocation = new ConcreteGeolocation();
		$geolocation->setLocationCookie();

		unset($_COOKIE['test_geolocation_cookie']);

		// If we got here without error, cookie check worked
		$this->assertTrue(true);
	}

	/**
	 * Test that getGeolocation returns 'localhost' for 127.0.0.1
	 *
	 * @return void
	 */
	public function testGetGeolocationReturnsLocalhostForLoopbackIpv4(): void
	{
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

		$geolocation = new ConcreteGeolocation();
		$result = $geolocation->getGeolocation();

		$this->assertSame('localhost', $result);

		unset($_SERVER['REMOTE_ADDR']);
	}

	/**
	 * Test that getGeolocation returns 'localhost' for ::1 (IPv6 loopback)
	 *
	 * @return void
	 */
	public function testGetGeolocationReturnsLocalhostForLoopbackIpv6(): void
	{
		$_SERVER['REMOTE_ADDR'] = '::1';

		$geolocation = new ConcreteGeolocation();
		$result = $geolocation->getGeolocation();

		$this->assertSame('localhost', $result);

		unset($_SERVER['REMOTE_ADDR']);
	}

	/**
	 * Test that getGeolocation returns 'localhost' when REMOTE_ADDR is not set
	 *
	 * @return void
	 */
	public function testGetGeolocationReturnsLocalhostWhenNoRemoteAddr(): void
	{
		unset($_SERVER['REMOTE_ADDR']);

		$geolocation = new ConcreteGeolocation();
		$result = $geolocation->getGeolocation();

		$this->assertSame('localhost', $result);
	}

	/**
	 * Test that getGeolocation throws when phar file is missing
	 *
	 * @return void
	 */
	public function testGetGeolocationThrowsWhenPharMissing(): void
	{
		$_SERVER['REMOTE_ADDR'] = '8.8.8.8';
		Functions\when('esc_html__')->returnArg();

		Functions\when('file_exists')->justReturn(false);

		$geolocation = new ConcreteGeolocation();

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Missing Geolocation phar');

		try {
			$geolocation->getGeolocation();
		} finally {
			unset($_SERVER['REMOTE_ADDR']);
		}
	}

	/**
	 * Test that getGeolocation throws when db file is missing
	 *
	 * @return void
	 */
	public function testGetGeolocationThrowsWhenDbMissing(): void
	{
		$_SERVER['REMOTE_ADDR'] = '8.8.8.8';
		Functions\when('esc_html__')->returnArg();

		Functions\when('file_exists')->alias(function ($path) {
			// Phar exists, but DB does not.
			if (\str_contains($path, 'phar')) {
				return true;
			}
			return false;
		});

		$geolocation = new ConcreteGeolocation();

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('Missing Geolocation database');

		try {
			$geolocation->getGeolocation();
		} finally {
			unset($_SERVER['REMOTE_ADDR']);
		}
	}

	/**
	 * Test that getGeolocation uses manual IP address when provided
	 *
	 * @return void
	 */
	public function testGetGeolocationUsesManualIpAddress(): void
	{
		// ConcreteGeolocationWithIp has getIpAddress returning '127.0.0.1'.
		$geolocation = new ConcreteGeolocationWithIp();
		$result = $geolocation->getGeolocation();

		// Manual IP is 127.0.0.1 → localhost.
		$this->assertSame('localhost', $result);
	}

	/**
	 * Test that setLocationCookie catches exception and logs error
	 *
	 * @return void
	 */
	public function testSetLocationCookieLogsErrorOnException(): void
	{
		Functions\when('is_admin')->justReturn(false);
		Functions\when('esc_html__')->returnArg();

		$_SERVER['REMOTE_ADDR'] = '8.8.8.8';

		// Make file_exists fail so getGeolocation throws.
		Functions\when('file_exists')->justReturn(false);

		// Mock error_log and verify it gets called with the exception message.
		Functions\expect('error_log')
			->once()
			->with(\Mockery::on(function ($message) {
				return \str_contains($message, 'Missing Geolocation phar');
			}));

		$geolocation = new ConcreteGeolocation();

		// setLocationCookie should catch the exception and not rethrow.
		$geolocation->setLocationCookie();

		unset($_SERVER['REMOTE_ADDR']);
	}

	/**
	 * Test that getCountries returns array with default region groups and individual countries
	 *
	 * @return void
	 */
	public function testGetCountriesReturnsCountriesFromCache(): void
	{
		Functions\when('__')->returnArg();

		// Set up Helpers cache with geolocation countries data.
		$reflection = new \ReflectionClass(Helpers::class);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setAccessible(true);
		$cacheProperty->setValue(null, [
			'geolocation' => [
				'countries' => [
					['Code' => 'us', 'Name' => 'United States'],
					['Code' => 'de', 'Name' => 'Germany'],
				],
			],
		]);

		$geolocation = new ConcreteGeolocation();
		$countries = $geolocation->getCountries();

		$this->assertIsArray($countries);

		// Should contain the 3 default region groups + 2 countries from cache.
		$this->assertGreaterThanOrEqual(5, \count($countries));

		// Check default regions are present.
		$values = \array_column($countries, 'value');
		$this->assertContains('europe', $values);
		$this->assertContains('european-union', $values);
		$this->assertContains('ex-yugoslavia', $values);

		// Check countries from cache are present.
		$this->assertContains('us', $values);
		$this->assertContains('de', $values);

		// Check individual country structure.
		$usEntry = \array_filter($countries, fn($c) => ($c['value'] ?? '') === 'us');
		$usEntry = \array_values($usEntry)[0];
		$this->assertSame('United States', $usEntry['label']);
		$this->assertSame(['US'], $usEntry['group']);

		// Cleanup.
		$cacheProperty->setValue(null, []);
	}

	/**
	 * Test that getCountries includes additional custom countries
	 *
	 * @return void
	 */
	public function testGetCountriesIncludesAdditionalCountries(): void
	{
		Functions\when('__')->returnArg();

		$reflection = new \ReflectionClass(Helpers::class);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setAccessible(true);
		$cacheProperty->setValue(null, [
			'geolocation' => [
				'countries' => [
					['Code' => 'hr', 'Name' => 'Croatia'],
				],
			],
		]);

		$geolocation = new ConcreteGeolocationWithCustomCountries();
		$countries = $geolocation->getCountries();

		$values = \array_column($countries, 'value');
		$this->assertContains('custom-region', $values);

		// Cleanup.
		$cacheProperty->setValue(null, []);
	}

	/**
	 * Test that getCountries skips entries without Code
	 *
	 * @return void
	 */
	public function testGetCountriesSkipsEntriesWithoutCode(): void
	{
		Functions\when('__')->returnArg();

		$reflection = new \ReflectionClass(Helpers::class);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setAccessible(true);
		$cacheProperty->setValue(null, [
			'geolocation' => [
				'countries' => [
					['Code' => '', 'Name' => 'Unknown'],
					['Code' => 'jp', 'Name' => 'Japan'],
					['Name' => 'No Code'],
				],
			],
		]);

		$geolocation = new ConcreteGeolocation();
		$countries = $geolocation->getCountries();

		$values = \array_column($countries, 'value');
		$this->assertContains('jp', $values);
		// Entries without Code should be skipped.
		$this->assertNotContains('', $values);

		$cacheProperty->setValue(null, []);
	}
}

/**
 * Concrete implementation of AbstractGeolocation for testing
 */
class ConcreteGeolocation extends AbstractGeolocation
{
	/**
	 * Register the service
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('init', [$this, 'setLocationCookie']);
	}

	/**
	 * Get geolocation cookie name
	 *
	 * @return string
	 */
	public function getGeolocationCookieName(): string
	{
		return 'test_geolocation_cookie';
	}

	/**
	 * Get geolocation executable phar location
	 *
	 * @return string
	 */
	public function getGeolocationPharLocation(): string
	{
		return '/path/to/geoip2.phar';
	}

	/**
	 * Get geolocation database location
	 *
	 * @return string
	 */
	public function getGeolocationDbLocation(): string
	{
		return '/path/to/GeoLite2-Country.mmdb';
	}
}

/**
 * Concrete implementation with geolocation disabled for testing
 */
class ConcreteGeolocationDisabled extends ConcreteGeolocation
{
	/**
	 * Disable geolocation
	 *
	 * @return bool
	 */
	public function useGeolocation(): bool
	{
		return false;
	}
}

/**
 * Concrete implementation with manual IP address for testing
 */
class ConcreteGeolocationWithIp extends ConcreteGeolocation
{
	/**
	 * Returns a manual IP address for testing.
	 *
	 * @return string
	 */
	public function getIpAddress(): string
	{
		return '127.0.0.1';
	}
}

/**
 * Concrete implementation with additional custom countries for testing
 */
class ConcreteGeolocationWithCustomCountries extends ConcreteGeolocation
{
	/**
	 * Gets additional locations for country list.
	 *
	 * @return array<mixed>
	 */
	public function getAdditionalCountries(): array
	{
		return [
			[
				'label' => 'Custom Region',
				'value' => 'custom-region',
				'group' => ['XX'],
			],
		];
	}
}
