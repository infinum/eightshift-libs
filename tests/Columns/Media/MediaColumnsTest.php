<?php

namespace Tests\Unit\Columns;

use EightshiftLibs\Columns\Media\AbstractMediaColumns;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function() {
	setBeforeEach();
});

afterEach(function() {
	setAfterEach();
});

test('Hooks are registered for the custom user columns', function() {
	class MediaColumnMock extends AbstractMediaColumns {

		public function addColumnName(array $columns): array
		{
			return ['example'];
		}

		public function renderColumnContent(string $columnName, int $postId): string
		{
			return 'some content';
		}

		public function sortAddedColumns(array $columns): array
		{
			return ['test'];
		}
	};

	$mockUserColumn = new MediaColumnMock();

	$mockUserColumn->register();

	$this->assertNotFalse(has_filter('manage_media_columns', 'Tests\Unit\Columns\MediaColumnMock->addColumnName()'), 'manage_media_columns filter wasn\'t registered');
	$this->assertNotFalse(has_filter('manage_media_custom_column', 'Tests\Unit\Columns\MediaColumnMock->renderColumnContent()'), 'manage_media_custom_column filter wasn\'t registered');
	$this->assertNotFalse(has_filter('manage_media_sortable_columns', 'Tests\Unit\Columns\MediaColumnMock->sortAddedColumns()'), 'manage_media_sortable_columns filter wasn\'t registered');
	$this->assertSame(10, has_filter('manage_media_columns', 'Tests\Unit\Columns\MediaColumnMock->addColumnName()'), 'manage_media_columns filter priority was not correct');
	$this->assertSame(10, has_filter('manage_media_custom_column', 'Tests\Unit\Columns\MediaColumnMock->renderColumnContent()'), 'manage_media_custom_column filter priority was not correct');
	$this->assertSame(10, has_filter('manage_media_sortable_columns', 'Tests\Unit\Columns\MediaColumnMock->sortAddedColumns()'), 'manage_media_sortable_columns filter priority was not correct');
});
