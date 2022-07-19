<?php

namespace Tests\Unit\Columns\Media;

use EightshiftBoilerplate\Columns\Media\WebPMediaColumnExample;

beforeEach(function () {
	$this->webPMediaColumnExampleMock = new WebPMediaColumnExample();

	$this->webPMediaColumnExampleMockColumns = [
		'image' => 'test.jpg',
		'new' => 'newNew',
	];
});

afterEach(function () {
	unset($this->webPMediaColumnExampleMock, $this->webPMediaColumnExampleMockColumns);
});

test('Check if addColumnName function will return columns with new column name.', function () {
	$mock = $this->webPMediaColumnExampleMock;
	$columns = $this->webPMediaColumnExampleMockColumns;

	$mock = $mock->addColumnName($columns);

	expect($mock)
		->toMatchArray(
			array_merge(
				$columns,
				[
					WebPMediaColumnExample::COLUMN_KEY => 'WebP',
				]
			)
		);
});

test('Check if renderColumnContent function will return icon in the new column name.', function () {
	$mock = $this->webPMediaColumnExampleMock;

	$mock = $mock->renderColumnContent(WebPMediaColumnExample::COLUMN_KEY, 1);

	expect($mock)
		->toBeString('<span class="dashicons dashicons-no"></span>');
});

test('Check if sortAddedColumns function will return columns with new column name.', function () {
	$mock = $this->webPMediaColumnExampleMock;
	$columns = $this->webPMediaColumnExampleMockColumns;

	$mock = $mock->sortAddedColumns($columns);

	expect($mock)
		->toMatchArray(
			array_merge(
				$columns,
				[
					WebPMediaColumnExample::COLUMN_KEY => 'WebP',
				]
			)
		);
});
