<?php

namespace Tests\Unit\CustomTaxonomy;

use Brain\Monkey\Functions;
use EightshiftBoilerplate\CustomTaxonomy\TaxonomyExample;

beforeEach(function() {
	$this->example = new TaxonomyExample();
});

afterEach(function () {
	unset($this->example);
});

test('if taxonomy actions are registered', function () {
	$this->example->register();

	$this->assertTrue(\method_exists($this->example, 'register'));
	$this->assertSame(10, has_action('init', [$this->example, 'taxonomyRegisterCallback']));
});

test('if taxonomy is registered', function () {
	$action = 'taxonomy_registered';
	Functions\when('register_taxonomy')->justReturn(putenv("SIDEAFFECT={$action}"));

	$this->example->taxonomyRegisterCallback();

	$this->assertSame(\getenv('SIDEAFFECT'), $action);

	// Cleanup.
	putenv('SIDEAFFECT=');
});

