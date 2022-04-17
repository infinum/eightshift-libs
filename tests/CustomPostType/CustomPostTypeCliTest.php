<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\CustomPostType\PostTypeCli;

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

	$this->cpt = new PostTypeCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});

test('Custom post type CLI command will correctly copy the Custom post type class with defaults', function () {
	$cpt = $this->cpt;
	$cpt([], $cpt->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/ProductPostType.php');

	expect($generatedCPT)
		->toContain('class ProductPostType extends AbstractPostType')
		->toContain('admin-settings')
		->not->toContain('dashicons-analytics');
});


test('Custom post type CLI command will correctly copy the Custom post type class with set arguments', function () {
	$cpt = $this->cpt;
	$cpt([], [
		'label' => 'Book',
		'slug' => 'book',
		'rewrite_url' => 'book',
		'rest_endpoint_slug' => 'books',
		'capability' => 'post',
		'menu_position' => 50,
		'menu_icon' => 'dashicons-book',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/BookPostType.php');

	$this->assertStringContainsString('class BookPostType extends AbstractPostType', $generatedCPT);
	$this->assertStringContainsString('Book', $generatedCPT);
	$this->assertStringContainsString('book', $generatedCPT);
	$this->assertStringContainsString('book', $generatedCPT);
	$this->assertStringContainsString('books', $generatedCPT);
	$this->assertStringContainsString('post', $generatedCPT);
	$this->assertStringContainsString('50', $generatedCPT);
	$this->assertStringContainsString('dashicons-book', $generatedCPT);
	$this->assertStringNotContainsString('dashicons-analytics', $generatedCPT);

	expect($generatedCPT)
	->toContain('class BookPostType extends AbstractPostType')
	->toContain('Book')
	->toContain('book')
	->toContain('books')
	->toContain('post')
	->toContain('50')
	->toContain('dashicons-book')
	->not->toContain('dashicons-analytics');
});


test('Custom post type CLI documentation is correct', function () {
	$cpt = $this->cpt;

	$documentation = $cpt->getDoc();

	$key = 'shortdesc';

	expect($documentation)
	->toBeArray($documentation)
	->toHaveKeys([$key, 'synopsis']);
});


test('Registered post type will have properly created labels', function() {

	$cpt = $this->cpt;
	$cpt([], [
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
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/BookPostType.php');

	expect($generatedCPT)
		->toContain('book')
		->toContain('books')
		->toContain('All books')
		->toContain('all books')
		->toContain('$labels')
		->toContain('$nouns[0]');
});


test('Registered post type will have properly created plural label if the plural is not defined', function() {

	$cpt = $this->cpt;
	$cpt([], [
		'label' => 'Book',
		'slug' => 'book',
		'rewrite_url' => 'book',
		'rest_endpoint_slug' => 'books',
		'capability' => 'post',
		'menu_position' => 50,
		'menu_icon' => 'dashicons-book',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/BookPostType.php');

	expect($generatedCPT)
		->toContain('book')
		->toContain('books')
		->toContain('Books');
});


test('Missing required noun will trigger the invalid nouns exception', function() {

	$cpt = $this->cpt;
	$cpt([], [
		'label' => 'Book',
		'slug' => 'book',
		'rewrite_url' => 'book',
		'rest_endpoint_slug' => 'books',
		'capability' => 'post',
		'menu_position' => 50,
		'menu_icon' => 'dashicons-book',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/BookPostType.php');

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
	require_once \dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/BookPostType.php';

	$cptInstance = new \EightshiftLibs\CustomPostType\BookPostType();

	$reflection = new \ReflectionMethod($cptInstance, 'getGeneratedLabels');
	$reflection->setAccessible(true);
	$reflection->invoke($cptInstance, []); // This should trigger the error.
})->expectException(InvalidNouns::class);
