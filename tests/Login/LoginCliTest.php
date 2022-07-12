<?php

namespace Tests\Unit\Login;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Login\LoginCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new LoginCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Login CLI command will correctly copy the Login class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}Login{$sep}Login.php"));

	$this->assertStringContainsString('class Login implements ServiceInterface', $output);
	$this->assertStringContainsString('@package EightshiftLibs\Login', $output);
	$this->assertStringContainsString('namespace EightshiftLibs\Login', $output);
	$this->assertStringNotContainsString('footer.php', $output);
});

test('Login CLI command will correctly copy the Login class with set arguments', function () {
	$mock = $this->mock;
	$mock([], [
		'namespace' => 'CoolTheme',
	]);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}Login{$sep}Login.php"));

	$this->assertStringContainsString('namespace CoolTheme\Login;', $output);
});


test('Login CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
