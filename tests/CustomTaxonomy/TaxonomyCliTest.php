<?php

namespace Tests\Unit\CustomTaxonomy;

use EightshiftLibs\CustomTaxonomy\TaxonomyCli;

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

	$this->taxonomyCli = new TaxonomyCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Custom taxonomy CLI command will correctly copy the Taxonomy class', function () {
	$taxonomyCli = $this->taxonomyCli;
	$taxonomyCli([], $taxonomyCli->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedTaxonomy = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomTaxonomy/LocationTaxonomy.php');

	$this->assertStringContainsString('The LocationTaxonomy specific functionality.', $generatedTaxonomy);
	$this->assertStringContainsString('class LocationTaxonomy extends AbstractTaxonomy', $generatedTaxonomy);
	$this->assertStringContainsString('getTaxonomySlug', $generatedTaxonomy);
	$this->assertStringContainsString('getPostTypeSlug', $generatedTaxonomy);
	$this->assertStringContainsString('getTaxonomyArguments', $generatedTaxonomy);
	$this->assertStringContainsString('TAXONOMY_SLUG', $generatedTaxonomy);
	$this->assertStringContainsString('REST_API_ENDPOINT_SLUG', $generatedTaxonomy);
	$this->assertStringNotContainsString('someRandomMethod', $generatedTaxonomy);
});


test('Custom acf meta CLI documentation is correct', function () {
	$taxonomyCli = $this->taxonomyCli;

	$documentation = $taxonomyCli->getDoc();

	$descKey = 'shortdesc';
	$synopsisKey = 'synopsis';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($descKey, $documentation);
	$this->assertArrayHasKey($synopsisKey, $documentation);
	$this->assertIsArray($documentation[$synopsisKey]);
	$this->assertSame('Generates custom taxonomy class file.', $documentation[$descKey]);

	$this->assertSame('assoc', $documentation[$synopsisKey][0]['type']);
	$this->assertSame('label', $documentation[$synopsisKey][0]['name']);
	$this->assertSame('The label of the custom taxonomy to show in WP admin.', $documentation[$synopsisKey][0]['description']);
	$this->assertSame(false, $documentation[$synopsisKey][0]['optional']);

	$this->assertSame('assoc', $documentation[$synopsisKey][1]['type']);
	$this->assertSame('slug', $documentation[$synopsisKey][1]['name']);
	$this->assertSame('The name of the custom taxonomy slug. Example: location.', $documentation[$synopsisKey][1]['description']);
	$this->assertSame(false, $documentation[$synopsisKey][1]['optional']);

	$this->assertSame('assoc', $documentation[$synopsisKey][2]['type']);
	$this->assertSame('rest_endpoint_slug', $documentation[$synopsisKey][2]['name']);
	$this->assertSame('The name of the custom taxonomy REST-API endpoint slug. Example: locations.', $documentation[$synopsisKey][2]['description']);
	$this->assertSame(false, $documentation[$synopsisKey][2]['optional']);

	$this->assertSame('assoc', $documentation[$synopsisKey][3]['type']);
	$this->assertSame('post_type_slug', $documentation[$synopsisKey][3]['name']);
	$this->assertSame('The position where to assign the new custom taxonomy. Example: post.', $documentation[$synopsisKey][3]['description']);
	$this->assertSame(false, $documentation[$synopsisKey][3]['optional']);
});
