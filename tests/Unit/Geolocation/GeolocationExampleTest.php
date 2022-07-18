<?php

namespace Tests\Unit\Geolocation;

use EightshiftBoilerplate\Geolocation\GeolocationExample;
use Exception;

beforeEach(function () {
	$this->geolocation = new GeolocationExample();
});

test('Register method will call correct hooks', function () {
	$this->geolocation->register();

	expect(\method_exists($this->geolocation, 'register'))->toBeTrue();
	expect(\has_filter('init', [$this->geolocation, 'setLocationCookie']))->toBe(10);
});

test('getGeolocationCookieName will return correct cookie name', function () {
	$this->geolocation->getGeolocationCookieName();

	expect($this->geolocation->getGeolocationCookieName())->toEqual('%cookie_name%');
});

test('getGeolocationPharLocation will throw Exception if file is missing in path', function () {
	$this->geolocation->getGeolocationPharLocation();
})->throws(Exception::class);

test('getGeolocationDbLocation will throw Exception if file is missing in path', function () {
	$this->geolocation->getGeolocationDbLocation();
})->throws(Exception::class);
