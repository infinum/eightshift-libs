<?php

namespace Tests\Unit\Geolocation;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Geolocation\GeolocationExample;
use EightshiftLibs\Helpers\Components;
use Exception;

use function Tests\mock;

beforeEach(function () {
	setBeforeEach();

	$this->geolocation = new GeolocationExample();
	$this->germanIp = '54.93.127.0';
});

afterEach(function () {
	setAfterEach();

	unset($this->geolocation);
	unset($this->germanIp);
});

//---------------------------------------------------------------------------------//

test('useGeolocation return true', function () {

	$use = $this->geolocation->useGeolocation();

	expect($use)->toBeTrue();
});

//---------------------------------------------------------------------------------//

test('getAdditionalCountries return empty array', function () {
	expect($this->geolocation->getAdditionalCountries())->toBeArray()->toBeEmpty();
});

//---------------------------------------------------------------------------------//

test('getIpAddress return empty string', function () {
	expect($this->geolocation->getIpAddress())->toBeString()->toBeEmpty();
});

//---------------------------------------------------------------------------------//

test('setLocationCookie will exit if is not frontend', function () {
	$action = 'is_admin';

	Functions\when($action)->justReturn(putenv("ES_SIDEAFFECT_1={$action}"));

	$this->geolocation->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT_1'))->toEqual($action);
});

test('setLocationCookie will exit if useGeolocation is false', function () {
	$action = 'is_not_used';

	$mock = mock(GeolocationExample::class)->makePartial();
	$mock->shouldReceive('useGeolocation')->andReturn(false);

	Functions\when($action)->justReturn(putenv("ES_SIDEAFFECT_1={$action}"));

	$mock->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT_1'))->toEqual($action);
});

test('setLocationCookie will exit if cookie is set', function () {
	$action = 'is_cookie_set';

	$cookieName = $this->geolocation->getGeolocationCookieName();

	$_COOKIE[$cookieName] = 'HR';

	if (isset($cookieName)) {
		putenv("ES_SIDEAFFECT_1={$action}");
	}

	$this->geolocation->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT_1'))->toEqual($action);

	unset($_COOKIE[$cookieName]);
});

test('setLocationCookie will set cookie to localhost.', function () {
	$mock = mock(GeolocationExample::class)->makePartial();
	$mock->shouldReceive('setCookie')->withArgs(function (string $name, string $value) {
		putenv("ES_SIDEAFFECT_1={$name}");
		putenv("ES_SIDEAFFECT_2={$value}");
	});

	$mock->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT_1'))->toEqual($this->geolocation->getGeolocationCookieName());
	expect(getenv('ES_SIDEAFFECT_2'))->toEqual('localhost');
});

test('setLocationCookie will set cookie based on the server location.', function () {
	$mock = mock(GeolocationExample::class)->makePartial();

	$sep = \DIRECTORY_SEPARATOR;

	$mock->shouldReceive('getGeolocationPharLocation')->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.phar"));
	$mock->shouldReceive('getGeolocationDbLocation')->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.mmdb"));
	$mock->shouldReceive('setCookie')->withArgs(function (string $name, string $value) {
		putenv("ES_SIDEAFFECT_1={$name}");
		putenv("ES_SIDEAFFECT_2={$value}");
	});

	$_SERVER['REMOTE_ADDR'] = $this->germanIp;

	$mock->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT_2'))->toEqual('DE');

	unset($_SERVER['REMOTE_ADDR']);
});

test('setLocationCookie will set cookie based on the provided manual ip.', function () {
	$mock = mock(GeolocationExample::class)->makePartial();

	$sep = \DIRECTORY_SEPARATOR;

	$mock->shouldReceive('getGeolocationPharLocation')->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.phar"));
	$mock->shouldReceive('getGeolocationDbLocation')->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.mmdb"));
	$mock->shouldReceive('getIpAddress')->andReturn($this->germanIp);
	$mock->shouldReceive('setCookie')->withArgs(function (string $name, string $value) {
		putenv("ES_SIDEAFFECT_1={$name}");
		putenv("ES_SIDEAFFECT_2={$value}");
	});

	$mock->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT_2'))->toEqual('DE');
});

test('setLocationCookie will throw and error if something is wrong.', function () {
	$mock = mock(GeolocationExample::class)->makePartial();

	$sep = \DIRECTORY_SEPARATOR;

	$mock->shouldReceive('getGeolocationPharLocation')->andReturn(Components::getProjectPaths('testsData', "geolocation{$sep}geoip.phar"));

	$mock->shouldReceive('getGeolocationDbLocation')->andThrow(new Exception('test'));
	$mock->shouldReceive('getIpAddress')->andReturn($this->germanIp);
	$mock->shouldReceive('setCookie')->withArgs(function (string $name, string $value) {
		putenv("ES_SIDEAFFECT_1={$name}");
		putenv("ES_SIDEAFFECT_2={$value}");
	});

	$mock->setLocationCookie();

	expect(getenv('ES_SIDEAFFECT_2'))->toEqual('ERROR: test');
});

//---------------------------------------------------------------------------------//

test('getCountries returns array of correct defaults from manifest and code.', function () {
	$countries = $this->geolocation->getCountries();

	expect($countries[0])->toBeArray()->toHaveKeys(['label', 'value', 'group'])
		->and(count($countries))->toEqual(252)
		->and($countries[0]['value'])->toEqual('europe')
		->and($countries[1]['value'])->toEqual('european-union')
		->and($countries[2]['value'])->toEqual('ex-yugoslavia')
		->and($countries[3]['value'])->toEqual('AF');
});

test('getCountries returns array of defaults with additional items.', function () {
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
