<?php

namespace Tests\Unit\Login;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Login\LoginCli;

use function Tests\getMockArgs;

beforeEach(function() {
	$this->mock = new LoginCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Login CLI command will correctly copy the Login class with defaults', function () {
	$mock = $this->mock;
	$mock([], getMockArgs());

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', "Login{$sep}Login.php"));

	$this->assertStringContainsString('class Login implements ServiceInterface', $output);
	$this->assertStringContainsString('@package Infinum\Login', $output);
	$this->assertStringContainsString('namespace Infinum\Login', $output);
	$this->assertStringNotContainsString('footer.php', $output);
});

test('Login CLI command will correctly copy the Login class with set arguments', function () {
	$mock = $this->mock;
	$mock([], getMockArgs([
		'namespace' => 'CoolTheme',
	]));

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', "Login{$sep}Login.php"));

	$this->assertStringContainsString('namespace CoolTheme\Login;', $output);
});


test('Login CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
