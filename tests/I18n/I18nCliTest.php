<?php

namespace Tests\Unit\I18n;

use EightshiftLibs\I18n\I18nCli;

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

$this->i18n = new I18nCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = \dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

test('I18n CLI command will correctly copy the I18n class with defaults', function () {
	$i18n = $this->i18n;
	$i18n([], []);

	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/src/I18n/I18n.php';

	// Check the output dir if the generated method is correctly generated.
	$generatedI18n = \file_get_contents($outputPath);

	$this->assertStringContainsString('class I18n implements ServiceInterface', $generatedI18n);
	$this->assertStringContainsString('@package EightshiftLibs\I18n', $generatedI18n);
	$this->assertStringContainsString('namespace EightshiftLibs\I18n', $generatedI18n);
	$this->assertStringNotContainsString('footer.php', $generatedI18n);
	$this->assertFileExists($outputPath);
});

test('I18n CLI command will correctly copy the I18n class with set arguments', function () {
	$i18n = $this->i18n;
	$i18n([], [
		'namespace' => 'CoolTheme',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedI18n = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/I18n/I18n.php');

	$this->assertStringContainsString('namespace CoolTheme\I18n;', $generatedI18n);
});

test('I18n CLI documentation is correct', function () {
	$i18n = $this->i18n;

	$documentation = $i18n->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayNotHasKey('synopsis', $documentation);
	$this->assertSame('Generates i18n language class.', $documentation[$key]);
});
