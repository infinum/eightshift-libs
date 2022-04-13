<?php

namespace Tests\Unit\Columns;

use Brain\Monkey;
use EightshiftLibs\Columns\PostType\AbstractPostTypeColumns;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Hooks are registered for the custom post type columns', function() {
	class PostTypeColumnMock extends AbstractPostTypeColumns {
		public const POST_TYPE = 'example';

		public function addColumnName(array $columns): array
		{
			return [];
		}

		public function renderColumnContent(string $columnName, int $postId): void
		{
		}

		protected function getPostTypeSlugs(): array
		{
			return [self::POST_TYPE];
		}
	};

	$mockPostTypeColumn = new PostTypeColumnMock();

	$mockPostTypeColumn->register();
	$postType = $mockPostTypeColumn::POST_TYPE;

	$this->assertNotFalse(has_filter("manage_{$postType}_posts_columns", 'Tests\Unit\Columns\PostTypeColumnMock->addColumnName()'), 'Filter wasn\'t registered');
	$this->assertNotFalse(has_action("manage_{$postType}_posts_custom_column", 'Tests\Unit\Columns\PostTypeColumnMock->renderColumnContent()'), 'Action wasn\'t registered');
	$this->assertSame(10, has_filter("manage_{$postType}_posts_columns", 'Tests\Unit\Columns\PostTypeColumnMock->addColumnName()'), 'Filter priority was not correct');
	$this->assertSame(10, has_action("manage_{$postType}_posts_custom_column", 'Tests\Unit\Columns\PostTypeColumnMock->renderColumnContent()'), 'Action priority was not correct');
});
