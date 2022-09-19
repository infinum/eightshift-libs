<?php

namespace Tests\Unit\Geolocation;

use Brain\Monkey\Functions;
use EightshiftBoilerplate\Geolocation\GeolocationExample;
use EightshiftLibs\Helpers\Components;
use Exception;
use GeoIp2\Database\Reader;

use function Tests\mock;

beforeEach(function () {
	$this->geolocation = new GeolocationExample();
	$this->germanIp = '54.93.127.0';
});

afterEach(function () {
	unset($this->geolocation);
	unset($this->germanIp);
});

//---------------------------------------------------------------------------------//

test('useGeolocation function will return true', function () {
	expect($this->geolocation->useGeolocation())->toBeTrue();
});

//---------------------------------------------------------------------------------//

test('getAdditionalCountries will return an empty array', function () {
	expect($this->geolocation->getAdditionalCountries())
		->toBeArray()
		->toBeEmpty();
});

//---------------------------------------------------------------------------------//

test('getIpAddress will return an empty string', function () {
	expect($this->geolocation->getIpAddress())
		->toBeString()
		->toBeEmpty();
});

//---------------------------------------------------------------------------------//

test('setLocationCookie will exit on the WordPress admin pages', function () {
	$action = 'is_admin';

	Functions\when($action)->justReturn(putenv("IS_ADMIN_ACTION={$action}"));

	$this->geolocation->setLocationCookie();

	expect(getenv('IS_ADMIN_ACTION'))->toEqual($action);
});

test('setLocationCookie will exit if useGeolocation is false', function () {
	Functions\when('is_admin')->justReturn(false);

	$mock = mock(GeolocationExample::class)->makePartial();
	$mock->shouldReceive('useGeolocation')->andReturn(false);

	expect($mock->setLocationCookie())->toBeNull();
});

test('setLocationCookie will exit if cookie is set', function () {
	$cookieName = $this->geolocation->getGeolocationCookieName();

	$_COOKIE[$cookieName] = 'HR';

	expect($this->geolocation->setLocationCookie())->toBeNull();

	unset($_COOKIE[$cookieName]);
});

test('setLocationCookie will set cookie to localhost', function () {
	$mock = mock(GeolocationExample::class)->makePartial();
	$mock->shouldReceive('setCookie')
		->withArgs(function (string $name, string $value) {
			putenv("ES_SIDEAFFECT_1={$name}");
			putenv("ES_SIDEAFFECT_2={$value}");
		})->andReturnTrue();

	$mock->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT_1'))
		->toEqual($this->geolocation->getGeolocationCookieName())
		->and(getenv('ES_SIDEAFFECT_2'))->toEqual('localhost');
});

test('setLocationCookie will set cookie based on the server location', function () {
	$mock = mock(GeolocationExample::class)->makePartial();

	$sep = \DIRECTORY_SEPARATOR;

	$mock->shouldReceive('getGeolocationPharLocation')
		->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.phar"));
	$mock->shouldReceive('getGeolocationDbLocation')
		->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.mmdb"));
	$mock->shouldReceive('setCookie')->withArgs(function (string $name, string $value) {
		putenv("ES_SIDEAFFECT_1={$name}");
		putenv("ES_SIDEAFFECT_2={$value}");
	});

	$_SERVER['REMOTE_ADDR'] = $this->germanIp;

	$mock->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT_2'))->toEqual('DE');

	unset($_SERVER['REMOTE_ADDR']);
});

test('setLocationCookie will set cookie based on the provided manual ip', function () {
	$mock = mock(GeolocationExample::class)->makePartial();

	$sep = \DIRECTORY_SEPARATOR;

	$mock->shouldReceive('getGeolocationPharLocation')
		->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.phar"));
	$mock->shouldReceive('getGeolocationDbLocation')
		->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.mmdb"));
	$mock->shouldReceive('getIpAddress')->andReturn($this->germanIp);
	$mock->shouldReceive('setCookie')->withArgs(function (string $name, string $value) {
		putenv("ES_SIDEAFFECT_1={$name}");
		putenv("ES_SIDEAFFECT_2={$value}");
	});

	$mock->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT_2'))->toEqual('DE');
});

test('setLocationCookie will exit if geolocation DB is missing', function () {
	$mock = mock(GeolocationExample::class)->makePartial();

	$sep = \DIRECTORY_SEPARATOR;

	$mock->shouldReceive('getGeolocationPharLocation')
		->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.phar"));

	$mock->shouldReceive('getGeolocationDbLocation')->andReturn('');
	$mock->shouldReceive('getIpAddress')->andReturn($this->germanIp);

	$mock->setLocationCookie();
	expect($this->geolocation->setLocationCookie())->toBeNull();
});

test('getGeolocation will throw error if geolocation phar is missing', function () {
	$mock = mock(GeolocationExample::class)->makePartial();

	$sep = \DIRECTORY_SEPARATOR;

	$mock->shouldReceive('getGeolocationPharLocation')
		->andReturn('');

	$mock->shouldReceive('getGeolocationDbLocation')
		->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.mmdb"));
	$mock->shouldReceive('getIpAddress')->andReturn($this->germanIp);

	$mock->getGeolocation();
})->expectExceptionMessage('Missing Geolocation phar on this location ');

test('getGeolocation will return error if GeoIp reader throws an error', function () {
	$mock = mock(GeolocationExample::class)->makePartial();

	$sep = \DIRECTORY_SEPARATOR;

	$mock->shouldReceive('getGeolocationPharLocation')
		->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.phar"));

	$mock->shouldReceive('getGeolocationDbLocation')
		->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.mmdb"));

	$mock->shouldReceive('getIpAddress')->andReturn('0.0.0.0');

	expect($mock->getGeolocation())
	->toBe('ERROR: The address 0.0.0.0 is not in the database.');
});

//---------------------------------------------------------------------------------//

test('getCountries returns array of correct defaults from manifest and code', function () {
	$countries = $this->geolocation->getCountries();

	expect($countries[0])->toBeArray()->toHaveKeys(['label', 'value', 'group'])
		->and(count($countries))->toEqual(252)
		->and($countries[0]['value'])->toEqual('europe')
		->and($countries[1]['value'])->toEqual('european-union')
		->and($countries[2]['value'])->toEqual('ex-yugoslavia')
		->and($countries[3]['value'])->toEqual('AF');
});

test('getCountries returns array of defaults with additional items', function () {
	$mock = mock(GeolocationExample::class)->makePartial();
	$mock->shouldReceive('getAdditionalCountries')->andReturn([
		[
			'label' => 'test',
			'value' => 'test',
			'group' => ['test'],
		]
	]);

	$countries = $mock->getCountries();

	expect(count($countries))->toEqual(253)
		->and($countries[0]['value'])->toEqual('europe')
		->and($countries[252]['value'])->toEqual('test');
});

//---------------------------------------------------------------------------------//
