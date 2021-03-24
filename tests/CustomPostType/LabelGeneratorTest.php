<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\CustomPostType\LabelGenerator;
use EightshiftLibs\Exception\InvalidNouns;

use function Tests\setupMocks;

beforeEach(function() {
	setupMocks();

	$this->generator = new LabelGenerator();
});


test('Generating labels will work', function () {
	$nouns = [
		LabelGenerator::SINGULAR_NAME_UC => 'Book',
		LabelGenerator::SINGULAR_NAME_LC => 'book',
		LabelGenerator::PLURAL_NAME_UC => 'Books',
		LabelGenerator::PLURAL_NAME_LC => 'books',
	];

	$expectedArray = [
		'name' => 'Books',
		'singular_name' => 'Book',
		'menu_name' => 'Books',
		'name_admin_bar' => 'Book',
		'archives' => 'Book Archives',
		'attributes' => 'Book Attributes',
		'parent_item_colon' => 'Parent Book:',
		'all_items' => 'All Books',
		'add_new_item' => 'Add New Book',
		'add_new' => 'Add New',
		'new_item' => 'New Book',
		'edit_item' => 'Edit Book',
		'update_item' => 'Update Book',
		'view_item' => 'View Book',
		'view_items' => 'View Books',
		'search_items' => 'Search Book',
		'not_found' => 'Not found',
		'not_found_in_trash' => 'Not found in Trash',
		'featured_image' => 'Featured Image',
		'set_featured_image' => 'Set featured image',
		'remove_featured_image' => 'Remove featured image',
		'use_featured_image' => 'Use as featured image',
		'insert_into_item' => 'Insert into book',
		'uploaded_to_this_item' => 'Uploaded to this book',
		'items_list' => 'Books list',
		'items_list_navigation' => 'Books list navigation',
		'filter_items_list' => 'Filter books list',
	];

	$generatedLabels = $this->generator->getGeneratedLabels($nouns);

	$this->assertSame($expectedArray, $generatedLabels);
});


test('Generating labels will throw exception if noun wasn\'t specified', function () {
	$nouns = [
		LabelGenerator::SINGULAR_NAME_UC => 'Book',
		LabelGenerator::SINGULAR_NAME_LC => 'book',
		LabelGenerator::PLURAL_NAME_UC => 'Books',
	];

	$this->generator->getGeneratedLabels($nouns);
})->throws(InvalidNouns::class, 'The array of nouns passed into the Label_Generator is missing the plural_name_lc noun.');
