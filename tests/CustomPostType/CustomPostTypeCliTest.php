<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\CustomPostType\PostTypeCli;

use function Tests\deleteCliOutput;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = \Mockery::mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});


test('Custom post type CLI command will correctly copy the Custom post type class with defaults', function () {
	$cpt = new PostTypeCli('boilerplate');
	$cpt([], $cpt->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/CustomPostType/ProductPostType.php');

	$this->assertStringContainsString('class ProductPostType extends AbstractPostType', $generatedCPT);
	$this->assertStringContainsString('admin-settings', $generatedCPT);
	$this->assertStringNotContainsString('dashicons-analytics', $generatedCPT);
});


test('Custom post type CLI command will correctly copy the Custom post type class with set arguments', function () {
	$cpt = new PostTypeCli('boilerplate');
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
	$cpt = new PostTypeCli('boilerplate');

	$documentation = $cpt->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertEquals('Generates custom post type class file.', $documentation[$key]);
});
