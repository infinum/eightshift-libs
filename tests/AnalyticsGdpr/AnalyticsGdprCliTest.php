<?php

namespace Tests\Unit\AnalyticsGdpr;

use EightshiftLibs\AnalyticsGdpr\AnalyticsGdprCli;

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

	$this->analyticsGdpr = new AnalyticsGdprCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});


test('Custom Analytics & GDPR Settings CLI command will correctly copy the AnalyticsGdpr class with defaults', function () {
	$analyticsGdpr = $this->analyticsGdpr;
	$analyticsGdpr([], []);

	// Check the output dir if the generated method is correctly generated.
	$generatedMeta = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/AnalyticsGdpr/AnalyticsGdpr.php');

	$this->assertStringContainsString('class AnalyticsGdpr implements ServiceInterface', $generatedMeta);
	$this->assertStringContainsString('acf_add_options_page', $generatedMeta);
	$this->assertStringContainsString('acf_add_local_field_group', $generatedMeta);
	$this->assertStringContainsString('createAnalyticsPage', $generatedMeta);
	$this->assertStringContainsString('registerAnalytics', $generatedMeta);
	$this->assertStringContainsString('createGdprModalPage', $generatedMeta);
	$this->assertStringContainsString('registerGdprModalSettings', $generatedMeta);
	$this->assertStringContainsString('prepareGdprModalData', $generatedMeta);
	$this->assertStringNotContainsString('someRandomMethod', $generatedMeta);
});

test('Custom GDPR settings CLI documentation is correct', function () {
	$analyticsGdpr = $this->analyticsGdpr;

	$documentation = $analyticsGdpr->getDoc();

	$descKey = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($descKey, $documentation);
	$this->assertSame('Generates project Analytics and GDPR Settings classes using ACF.', $documentation[$descKey]);
});
