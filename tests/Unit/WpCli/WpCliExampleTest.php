<?php

namespace Tests\Unit\WpCli;

use EightshiftLibs\Config\ConfigThemeCli;
use EightshiftLibs\WpCli\WpCli;
use Infinum\WpCli\TestWpCli;

use function Tests\getMockArgs;
use function Tests\reqOutputFiles;

beforeEach(function () {
	$configThemeCliMock = new ConfigThemeCli('boilerplate');
	$configThemeCliMock([], getMockArgs($configThemeCliMock->getDefaultArgs()));

	$wpCliMock = new WpCli('boilerplate');
	$wpCliMock([], getMockArgs($wpCliMock->getDefaultArgs()));

	reqOutputFiles(
		'WpCli/TestWpCli.php',
	);
});

test('Register method will call init hook', function () {
	(new TestWpCli())->register();

	expect(has_action('cli_init', 'Infinum\WpCli\TestWpCli->registerCommand()'))
		->toEqual(10);
});

test('Prepare command docs returns correct doc', function() {
	$mock = (new TestWpCli())->getDocs();

	expect($mock)
		->toHaveKeys(['shortdesc']);
});

test('Custom command class is callable', function() {
	expect((new TestWpCli()))->toBeCallable();
});

test('Custom command example documentation is correct', function () {
	expect((new TestWpCli())->getDocs())->toBeArray();
});
