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
	expect(\has_action('init', [$this->geolocation, 'setLocationCookie']))->toBe(10);
});

test('getGeolocationCookieName will return correct cookie name', function () {
	$this->geolocation->getGeolocationCookieName();

	expect($this->geolocation->getGeolocationCookieName())->toEqual('%cookie_name%');
});

test('getGeolocationPharLocation will return the location of the geiop2.phar file', function () {
	$reflection = new \ReflectionClass(GeolocationExample::class);
	$path = dirname($reflection->getFileName());

	expect($this->geolocation->getGeolocationPharLocation())->toEqual($path . \DIRECTORY_SEPARATOR . 'geoip2.phar');
});

test('getGeolocationDbLocation will return the location of the Geolite2-Country.mmdb file', function () {
	$reflection = new \ReflectionClass(GeolocationExample::class);
	$path = dirname($reflection->getFileName());

	expect($this->geolocation->getGeolocationDbLocation())->toEqual($path . \DIRECTORY_SEPARATOR . 'GeoLite2-Country.mmdb');
});
