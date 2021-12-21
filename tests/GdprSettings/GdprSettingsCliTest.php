<?php

namespace Tests\Unit\GdprSettings;

use EightshiftLibs\GdprSettings\GdprSettingsCli;

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

	$this->gdprSettings = new GdprSettingsCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});


test('Custom GDPR Settings CLI command will correctly copy the GDPR Settings class with defaults', function () {
	$gdprSettings = $this->gdprSettings;
	$gdprSettings([], []);

	// Check the output dir if the generated method is correctly generated.
	$generatedMeta = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/GdprSettings/GdprSettings.php');

	$this->assertStringContainsString('class GdprSettings implements ServiceInterface', $generatedMeta);
	$this->assertStringContainsString('acf_add_options_page', $generatedMeta);
	$this->assertStringContainsString('acf_add_local_field_group', $generatedMeta);
	$this->assertStringContainsString('createGdprSettingsPage', $generatedMeta);
	$this->assertStringContainsString('registerGdprSettings', $generatedMeta);
	$this->assertStringNotContainsString('someRandomMethod', $generatedMeta);
});

test('Custom GDPR settings CLI documentation is correct', function () {
	$gdprSettings = $this->gdprSettings;

	$documentation = $gdprSettings->getDoc();

	$descKey = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($descKey, $documentation);
	$this->assertSame('Generates project GDPR Settings class using ACF.', $documentation[$descKey]);
});
