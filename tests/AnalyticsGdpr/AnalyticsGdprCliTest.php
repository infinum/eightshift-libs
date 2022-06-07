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

	expect($generatedMeta)
		->toBeString()
		->toContain('class AnalyticsGdpr implements ServiceInterface')
		->toContain('acf_add_options_page')
		->toContain('acf_add_local_field_group')
		->toContain('createAnalyticsPage')
		->toContain('registerAnalytics')
		->toContain('createGdprModalPage')
		->toContain('registerGdprModalSettings')
		->toContain('prepareGdprModalData')
		->not->toContain('someRandomMethod');
});

test('Custom GDPR settings CLI documentation is correct', function () {
	$analyticsGdpr = $this->analyticsGdpr;

	$documentation = $analyticsGdpr->getDoc();

	$descKey = 'shortdesc';

	expect($documentation)
		->toBeArray()
		->toHaveKey($descKey);
	
	expect($documentation[$descKey])
		->toBeString()
		->toBe('Create project Analytics and GDPR Settings classes using ACF plugin.');
});
