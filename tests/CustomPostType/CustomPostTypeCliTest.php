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
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});


test('Custom post type CLI command will correctly copy the Custom post type class with defaults', function () {
	$cpt = $this->cpt;
	$cpt([], $cpt->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/ProductPostType.php');

	$this->assertStringContainsString('class ProductPostType extends AbstractPostType', $generatedCPT);
	$this->assertStringContainsString('admin-settings', $generatedCPT);
	$this->assertStringNotContainsString('dashicons-analytics', $generatedCPT);
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
	$generatedCPT = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/BookPostType.php');

	$this->assertStringContainsString('class BookPostType extends AbstractPostType', $generatedCPT);
	$this->assertStringContainsString('Book', $generatedCPT);
	$this->assertStringContainsString('book', $generatedCPT);
	$this->assertStringContainsString('book', $generatedCPT);
	$this->assertStringContainsString('books', $generatedCPT);
	$this->assertStringContainsString('post', $generatedCPT);
	$this->assertStringContainsString('50', $generatedCPT);
	$this->assertStringContainsString('dashicons-book', $generatedCPT);
	$this->assertStringNotContainsString('dashicons-analytics', $generatedCPT);
});


test('Custom post type CLI documentation is correct', function () {
	$cpt = $this->cpt;

	$documentation = $cpt->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertSame('Generates custom post type class file.', $documentation[$key]);
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
	$generatedCPT = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/BookPostType.php');

	$this->assertStringContainsString('book', $generatedCPT);
	$this->assertStringContainsString('books', $generatedCPT);
	$this->assertStringContainsString('All books', $generatedCPT);
	$this->assertStringContainsString('all books', $generatedCPT);
	$this->assertStringContainsString('$labels', $generatedCPT);
	$this->assertStringContainsString('$nouns[self::SINGULAR_NAME_UC]', $generatedCPT);
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
	$generatedCPT = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/BookPostType.php');

	$this->assertStringContainsString('book', $generatedCPT);
	$this->assertStringContainsString('books', $generatedCPT);
	$this->assertStringContainsString('Books', $generatedCPT);
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
	$generatedCPT = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/BookPostType.php');

	preg_match_all('/\$nouns\s=\s([^]]+)\]/m', $generatedCPT, $matches);

	$newClass = str_replace($matches[0][0]. ';', '', $generatedCPT);

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
	require_once dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/BookPostType.php';

	$cptInstance = new \EightshiftLibs\CustomPostType\BookPostType();

	$reflection = new \ReflectionMethod($cptInstance, 'getGeneratedLabels');
	$reflection->setAccessible(true);
	$reflection->invoke($cptInstance, []); // This should trigger the error.
})->expectException(InvalidNouns::class);
