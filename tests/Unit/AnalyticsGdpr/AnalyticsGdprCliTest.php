<?php

namespace Tests\Unit\AnalyticsGdpr;

use EightshiftLibs\AnalyticsGdpr\AnalyticsGdprCli;
use EightshiftLibs\Helpers\Components;

beforeEach(function () {
	$this->mock = new AnalyticsGdprCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});


test('Custom Analytics & GDPR Settings CLI command will correctly copy the AnalyticsGdpr class with defaults', function () {
	$analyticsGdpr = $this->mock;
	$analyticsGdpr([], []);

	// Check the output dir if the generated method is correctly generated.
	$generatedMeta = file_get_contents(Components::getProjectPaths('srcDestination', 'AnalyticsGdpr/AnalyticsGdpr.php'));

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
	$analyticsGdpr = $this->mock;

	$documentation = $analyticsGdpr->getDoc();

	$descKey = 'shortdesc';

	expect($documentation)
		->toBeArray()
		->toHaveKey($descKey);

	expect($documentation[$descKey])
		->toBeString()
		->toBe('Create project Analytics and GDPR Settings classes using ACF plugin.');
});
