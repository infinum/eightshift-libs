<?php

namespace Tests\Unit\Geolocation;

use EightshiftLibs\Geolocation\GeolocationExample;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;
use Brain\Monkey\Functions;

class GeolocationExampleUseFalseTest extends GeolocationExample {
	public function useGeolocation(): bool
	{
		return false;
	}
};

beforeEach(function () {
	setBeforeEach();

	$this->geolocation = new GeolocationExample();
	$this->geolocationExtendedUseFalse = new GeolocationExampleUseFalseTest();
});

afterEach(function () {
	setAfterEach();
});

test('useGeolocation return true', function () {

	$use = $this->geolocation->useGeolocation();

	expect($use)->toBeTrue();
});

test('setLocationCookie will exit if is not frontend', function () {
	$action = 'is_admin';

	Functions\when($action)->justReturn(putenv("ES_SIDEAFFECT={$action}"));

	$this->geolocation->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT'))->toEqual($action);
});

test('setLocationCookie will exit if useGeolocation is false', function () {
	$action = 'is_not_used';

	Functions\when('is_admin')->justReturn(false);
	Functions\when($action)->justReturn(putenv("ES_SIDEAFFECT={$action}"));

	$this->geolocationExtendedUseFalse->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT'))->toEqual($action);

	Functions\when($action)->justReturn(false);
});

test('setLocationCookie will exit if cookie is set', function () {
	$action = 'is_cookie_set';

	$cookieName = $this->geolocation->getGeolocationCookieName();

	$_COOKIE[$cookieName] = 'HR';

	if (isset($cookieName)) {
		putenv("ES_SIDEAFFECT={$action}");
	}

	$this->geolocation->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT'))->toEqual($action);
});
