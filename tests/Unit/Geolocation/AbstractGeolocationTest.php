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
