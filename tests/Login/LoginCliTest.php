<?php

namespace Tests\Unit\Login;

use EightshiftLibs\Login\LoginCli;

use function Tests\deleteCliOutput;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = \Mockery::mock('alias:WP_CLI');

$wpCliMock
	->shouldReceive('success')
	->andReturnArg(0);

$wpCliMock
	->shouldReceive('error')
	->andReturnArg(0);

$this->login = new LoginCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

test('Login CLI command will correctly copy the Login class with defaults', function () {
	$login = $this->login;
	$login([], []);

	// Check the output dir if the generated method is correctly generated.
	$generatedLogin = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/Login/Login.php');

	$this->assertStringContainsString('class Login implements ServiceInterface', $generatedLogin);
	$this->assertStringContainsString('@package EightshiftBoilerplate\Login', $generatedLogin);
	$this->assertStringContainsString('namespace EightshiftLibs\Login', $generatedLogin);
});

test('Login CLI command will correctly copy the Login class with set arguments', function () {
	$login = $this->login;
	$login([], [
		'namespace' => 'CoolTheme',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedLogin = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/Login/Login.php');

	$this->assertStringContainsString('class Login implements ServiceInterface', $generatedLogin);
	$this->assertStringContainsString('namespace CoolTheme\Login;', $generatedLogin);
});


test('Login CLI documentation is correct', function () {
	$login = $this->login;

	$documentation = $login->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayNotHasKey('synopsis', $documentation);
	$this->assertEquals('Generates Login class file.', $documentation[$key]);
});
