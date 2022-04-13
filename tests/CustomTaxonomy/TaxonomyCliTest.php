<?php

namespace Tests\Unit\CustomTaxonomy;

use EightshiftLibs\CustomTaxonomy\TaxonomyCli;

use EightshiftLibs\Exception\InvalidNouns;

use function Tests\deleteCliOutput;
use function Tests\setupMocks;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	setupMocks();
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->tax = new TaxonomyCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});

test('Custom taxonomy CLI command will correctly copy the Custom taxonomy class with defaults', function () {
	$tax = $this->tax;
	$tax([], $tax->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomTaxonomy/LocationTaxonomy.php');

	$this->assertStringContainsString('class LocationTaxonomy extends AbstractTaxonomy', $generatedCPT);
	$this->assertStringContainsString('location', $generatedCPT);
	$this->assertStringContainsString('post', $generatedCPT);
});


test('Custom taxonomy CLI command will correctly copy the Custom taxonomy class with set arguments', function () {
	$tax = $this->tax;
	$tax([], [
		'label' => 'Book',
		'slug' => 'book',
		'rewrite_url' => 'book',
		'rest_endpoint_slug' => 'books',
		'capability' => 'post',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomTaxonomy/BookTaxonomy.php');

	$this->assertStringContainsString('class BookTaxonomy extends AbstractTaxonomy', $generatedCPT);
	$this->assertStringContainsString('Book', $generatedCPT);
	$this->assertStringContainsString('book', $generatedCPT);
	$this->assertStringContainsString('book', $generatedCPT);
	$this->assertStringContainsString('books', $generatedCPT);
	$this->assertStringContainsString('post', $generatedCPT);
	$this->assertStringNotContainsString('dashicons-analytics', $generatedCPT);
});


test('Custom taxonomy CLI documentation is correct', function () {
	$tax = $this->tax;

	$documentation = $tax->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertSame('Generates custom taxonomy class file.', $documentation[$key]);
});


test('Registered taxonomy will have properly created labels', function() {

	$tax = $this->tax;
	$tax([], [
		'label' => 'Book',
		'slug' => 'book',
		'rewrite_url' => 'book',
		'rest_endpoint_slug' => 'books',
		'capability' => 'post',
		'menu_position' => 50,
		'menu_icon' => 'dashicons-book',
		'plural_label' => 'All books'
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomTaxonomy/BookTaxonomy.php');

	$this->assertStringContainsString('book', $generatedCPT);
	$this->assertStringContainsString('books', $generatedCPT);
	$this->assertStringContainsString('All books', $generatedCPT);
	$this->assertStringContainsString('all books', $generatedCPT);
	$this->assertStringContainsString('$labels', $generatedCPT);
	$this->assertStringContainsString('$nouns[0]', $generatedCPT);
});


test('Registered taxonomy will have properly created plural label if the plural is not defined', function() {

	$tax = $this->tax;
	$tax([], [
		'label' => 'Book',
		'slug' => 'book',
		'rewrite_url' => 'book',
		'rest_endpoint_slug' => 'books',
		'capability' => 'post',
		'menu_position' => 50,
		'menu_icon' => 'dashicons-book',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomTaxonomy/BookTaxonomy.php');

	$this->assertStringContainsString('book', $generatedCPT);
	$this->assertStringContainsString('books', $generatedCPT);
	$this->assertStringContainsString('Books', $generatedCPT);
});


test('Missing required noun will trigger the invalid nouns exception', function() {

	$tax = $this->tax;
	$tax([], [
		'label' => 'Book',
		'slug' => 'book',
		'rewrite_url' => 'book',
		'rest_endpoint_slug' => 'books',
		'capability' => 'post',
		'menu_position' => 50,
		'menu_icon' => 'dashicons-book',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomTaxonomy/BookTaxonomy.php');

	preg_match_all('/\$nouns\s=\s([^]]+)\]/m', $generatedCPT, $matches);

	$newClass = \str_replace($matches[0][0]. ';', '', $generatedCPT);

	/**
	 * This part is a bit of a dirty hack.
	 *
	 * We are accessing a protected method. We are doing this because it's being used
	 * in a CPT registration process, as a parameter generator. But we need to make sure that
	 * the correct exception will be thrown in case a noun array for label generation
	 * is missing or empty.
	 * Ideally this will never happen when using WP-CLI, because everything is set up
	 * for you, but if you manually copy class, and forget to add them, you'll get an
	 * error thrown.
	 *
	 * So we need to make sure that the error will indeed be thrown.
	 */
	require_once \dirname(__FILE__, 3) . '/cliOutput/src/CustomTaxonomy/BookTaxonomy.php';

	$taxInstance = new \EightshiftLibs\CustomTaxonomy\BookTaxonomy();

	$reflection = new \ReflectionMethod($taxInstance, 'getGeneratedLabels');
	$reflection->setAccessible(true);
	$reflection->invoke($taxInstance, []); // This should trigger the error.
})->expectException(InvalidNouns::class);
