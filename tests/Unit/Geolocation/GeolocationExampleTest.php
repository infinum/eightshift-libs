<?php

namespace Tests\Unit\Geolocation;

use EightshiftBoilerplate\Geolocation\GeolocationExample;
use EightshiftLibs\Cache\ManifestCacheCli;
use EightshiftLibs\Geolocation\GeolocationCli;
use Exception;
use Infinum\Cache\ManifestCache;
use Infinum\Geolocation\Geolocation;

use function Tests\getMockArgs;
use function Tests\reqOutputFiles;

beforeEach(function() {
	$manifestCacheCliMock = new ManifestCacheCli('boilerplate');
	$manifestCacheCliMock([], getMockArgs($manifestCacheCliMock->getDefaultArgs()));

	$geolocationCliMock = new GeolocationCli('boilerplate');
	$geolocationCliMock([], getMockArgs($geolocationCliMock->getDefaultArgs()));

	reqOutputFiles(
		'Cache/ManifestCache.php',
		'Geolocation/Geolocation.php',
	);
});

test('Register method will call correct hooks', function () {
	(new Geolocation(new ManifestCache()))->register();

	expect(\method_exists($this->geolocation, 'register'))->toBeTrue();
	expect(\has_action('init', [$this->geolocation, 'setLocationCookie']))->toBe(10);
});

test('getGeolocationCookieName will return correct cookie name', function () {
	expect((new Geolocation(new ManifestCache()))->getGeolocationCookieName())->toEqual('es-geolocation');
});

test('getGeolocationPharLocation will return the location of the geiop2.phar file', function () {
	var_dump((new Geolocation(new ManifestCache()))->getGeolocationPharLocation());
	$reflection = new \ReflectionClass(GeolocationExample::class);
	$path = dirname($reflection->getFileName());


	expect((new Geolocation(new ManifestCache()))->getGeolocationPharLocation())->toEqual($path . \DIRECTORY_SEPARATOR . 'geoip2.phar');
});

test('getGeolocationDbLocation will return the location of the Geolite2-Country.mmdb file', function () {
	$reflection = new \ReflectionClass(GeolocationExample::class);
	$path = dirname($reflection->getFileName());

	expect($this->geolocation->getGeolocationDbLocation())->toEqual($path . \DIRECTORY_SEPARATOR . 'GeoLite2-Country.mmdb');
});
