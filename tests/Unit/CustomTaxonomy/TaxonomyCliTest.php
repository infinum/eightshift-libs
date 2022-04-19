<?php

namespace Tests\Unit\CustomTaxonomy;

use EightshiftLibs\CustomTaxonomy\TaxonomyCli;

use EightshiftLibs\Exception\InvalidNouns;

use function Tests\deleteCliOutput;
use function Tests\setupUnitTestMocks;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	setupUnitTestMocks();
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
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/CustomTaxonomy/LocationTaxonomy.php');

	expect($generatedCPT)
		->toContain('class LocationTaxonomy extends AbstractTaxonomy')
		->toContain('location')
		->toContain('post');
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
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/CustomTaxonomy/BookTaxonomy.php');

	expect($generatedCPT)
		->toContain('class BookTaxonomy extends AbstractTaxonom')
		->toContain('Book')
		->toContain('book')
		->toContain('books')
		->toContain('post')
		->not->toContain('dashicons-analytics');
});

test('Custom taxonomy CLI documentation is correct', function () {
	$tax = $this->tax;

	$documentation = $tax->getDoc();

	$key = 'shortdesc';

	expect($documentation)
		->toBeArray($documentation)
		->toHaveKeys([$key, 'synopsis']);
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
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/CustomTaxonomy/BookTaxonomy.php');

	expect($generatedCPT)
		->toContain('book')
		->toContain('books')
		->toContain('All books')
		->toContain('all books')
		->toContain('$labels')
		->toContain('$nouns[0]');
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
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/CustomTaxonomy/BookTaxonomy.php');

	expect($generatedCPT)
		->toContain('book')
		->toContain('books')
		->toContain('Books');
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
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/CustomTaxonomy/BookTaxonomy.php');

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
	require_once \dirname(__FILE__, 4) . '/cliOutput/src/CustomTaxonomy/BookTaxonomy.php';

	$taxInstance = new \EightshiftLibs\CustomTaxonomy\BookTaxonomy();

	$reflection = new \ReflectionMethod($taxInstance, 'getGeneratedLabels');
	$reflection->setAccessible(true);
	$reflection->invoke($taxInstance, []); // This should trigger the error.
})->expectException(InvalidNouns::class);
