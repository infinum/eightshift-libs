<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\AdminMenus\AdminReusableBlocksMenuCli;

use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->cpt = new AdminReusableBlocksMenuCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Admin reusable blocks menu CLI command will correctly copy the admin reusable blocks menu example class with defaults', function () {
	$cpt = $this->cpt;
	$cpt([], $cpt->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/AdminReusableBlocksMenu.php');

	$this->assertStringContainsString('class AdminReusableBlocksMenu extends AbstractAdminMenu', $generatedCPT, 'Class name not correctly set');
	$this->assertStringContainsString('Reusable Blocks', $generatedCPT, 'Menu title not correctly replaced');
	$this->assertStringNotContainsString('product', $generatedCPT);
});


test('Admin reusable blocks menu CLI command will correctly copy the admin reusable blocks menu class with set arguments', function () {
	$cpt = $this->cpt;
	$cpt([], [
		'title' => 'Reusable Blocks',
		'menu_title' => 'Reusable Blocks',
		'capability' => 'edit_posts',
		'menu_slug' => 'edit.php?post_type=wp_block',
		'menu_icon' => 'dashicons-editor-table',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/AdminReusableBlocksMenu.php');

	$this->assertStringContainsString('class AdminReusableBlocksMenu extends AbstractAdminMenu', $generatedCPT, 'Class name not correctly set');
	$this->assertStringContainsString('Reusable Blocks', $generatedCPT, 'Menu title not correctly replaced');
	$this->assertStringContainsString('edit_posts', $generatedCPT, 'Capability not correctly replaced');
	$this->assertStringContainsString('4', $generatedCPT, 'Menu position not correctly replaced');
	$this->assertStringContainsString('dashicons-editor-table', $generatedCPT, 'Icon not correctly replaced');
	$this->assertStringNotContainsString('dashicons-analytics', $generatedCPT, 'String found that should not be here');
});


test('Admin reusable blocks menu CLI documentation is correct', function () {
	$cpt = $this->cpt;

	$documentation = $cpt->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertSame('Generates reusable blocks admin menu class file.', $documentation[$key]);
});
