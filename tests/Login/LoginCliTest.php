<?php

namespace Tests\Unit\Login;

use EightshiftLibs\Login\LoginCli;

use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = mock('alias:WP_CLI');

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
	deleteCliOutput();
});

test('Login CLI command will correctly copy the Login class with defaults', function () {
	$login = $this->login;
	$login([], []);

	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/src/Login/Login.php';

	// Check the output dir if the generated method is correctly generated.
	$generatedLogin = \file_get_contents($outputPath);

	$this->assertStringContainsString('class Login implements ServiceInterface', $generatedLogin);
	$this->assertStringContainsString('@package EightshiftLibs\Login', $generatedLogin);
	$this->assertStringContainsString('namespace EightshiftLibs\Login', $generatedLogin);
	$this->assertStringNotContainsString('footer.php', $generatedLogin);
	$this->assertFileExists($outputPath);
});

test('Login CLI command will correctly copy the Login class with set arguments', function () {
	$login = $this->login;
	$login([], [
		'namespace' => 'CoolTheme',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedLogin = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/Login/Login.php');

	$this->assertStringContainsString('namespace CoolTheme\Login;', $generatedLogin);
});


test('Login CLI documentation is correct', function () {
	expect($this->login->getDoc())->toBeArray();
});
