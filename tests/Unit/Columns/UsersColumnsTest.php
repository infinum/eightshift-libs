<?php

namespace Tests\Unit\Columns;

use Brain\Monkey;
use EightshiftLibs\Columns\User\AbstractUserColumns;

use function Tests\setupUnitTestMocks;

beforeEach(function() {
	Monkey\setUp();
	setupUnitTestMocks();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Hooks are registered for the custom user columns', function() {
	class UserColumnMock extends AbstractUserColumns {

		public function addColumnName(array $columns): array
		{
			return ['example'];
		}

		public function renderColumnContent(string $output, string $columnName, int $userId): string
		{
			return 'some content';
		}

		public function sortAddedColumns(array $columns): array
		{
			return ['test'];
		}
	};

	$mockUserColumn = new UserColumnMock();

	$mockUserColumn->register();

	$this->assertNotFalse(has_filter('manage_users_columns', 'Tests\Unit\Columns\UserColumnMock->addColumnName()'), 'manage_users_columns filter wasn\'t registered');
	$this->assertNotFalse(has_filter('manage_users_custom_column', 'Tests\Unit\Columns\UserColumnMock->renderColumnContent()'), 'manage_users_custom_column filter wasn\'t registered');
	$this->assertNotFalse(has_filter('manage_users_sortable_columns', 'Tests\Unit\Columns\UserColumnMock->sortAddedColumns()'), 'manage_users_sortable_columns filter wasn\'t registered');
	$this->assertSame(10, has_filter('manage_users_columns', 'Tests\Unit\Columns\UserColumnMock->addColumnName()'), 'manage_users_columns filter priority was not correct');
	$this->assertSame(10, has_filter('manage_users_custom_column', 'Tests\Unit\Columns\UserColumnMock->renderColumnContent()'), 'manage_users_custom_column filter priority was not correct');
	$this->assertSame(10, has_filter('manage_users_sortable_columns', 'Tests\Unit\Columns\UserColumnMock->sortAddedColumns()'), 'manage_users_sortable_columns filter priority was not correct');
});
