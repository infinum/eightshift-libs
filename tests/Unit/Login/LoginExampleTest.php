<?php

namespace Tests\Unit\Login;

use Brain\Monkey\Functions;
use EightshiftLibs\Login\LoginCli;
use Infinum\Login\Login;

use function Tests\getMockArgs;
use function Tests\reqOutputFiles;

beforeEach(function() {
	$loginCliMock = new LoginCli('boilerplate');
	$loginCliMock([], getMockArgs($loginCliMock->getDefaultArgs()));

	reqOutputFiles(
		'Login/Login.php',
	);
});

test('Register method will call init hook', function () {
	(new Login())->register();

	$this->assertSame(10, has_filter('login_headerurl', 'Infinum\Login\Login->customLoginUrl()'));
});

test('Asserts if customLoginUrl returns string', function () {

	Functions\when('home_url')->justReturn('custom/home/url');

	$output = (new Login())->customLoginUrl();

	$this->assertStringContainsString('custom/home/url', $output);
});
