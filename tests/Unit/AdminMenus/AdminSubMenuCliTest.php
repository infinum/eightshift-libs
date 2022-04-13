<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\AdminMenus\AdminSubMenuCli;

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

	$this->cpt = new AdminSubMenuCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Admin submenu CLI command will correctly copy the admin menu example class with defaults', function () {
	$cpt = $this->cpt;
	$cpt([], $cpt->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/AdminMenus/AdminTitleAdminSubMenu.php');

	$this->assertStringContainsString('class AdminTitleAdminSubMenu extends AbstractAdminSubMenu', $generatedCPT, 'Class name not correctly set');
	$this->assertStringContainsString('Admin Title', $generatedCPT, 'Menu title not correctly replaced');
	$this->assertStringNotContainsString('product', $generatedCPT);
});


test('Admin submenu CLI command will correctly copy the admin menu class with set arguments', function () {
	$cpt = $this->cpt;
	$cpt([], [
		'parent_slug' => 'reusable-blocks',
		'title' => 'Options',
		'menu_title' => 'Options',
		'capability' => 'edit_reusable_blocks',
		'menu_slug' => 'reusable-block-options',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/AdminMenus/ReusableBlockOptionsAdminSubMenu.php');

	$this->assertStringContainsString('class ReusableBlockOptionsAdminSubMenu extends AbstractAdminSubMenu', $generatedCPT, 'Class name not correctly set');
	$this->assertStringContainsString('Options', $generatedCPT, 'Menu title not correctly replaced');
	$this->assertStringContainsString('edit_reusable_blocks', $generatedCPT, 'Capability not correctly replaced');
	$this->assertStringNotContainsString('dashicons-analytics', $generatedCPT, 'String found that should not be here');
});


test('Admin submenu CLI documentation is correct', function () {
	$cpt = $this->cpt;

	$documentation = $cpt->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertSame('Generates admin sub menu class file.', $documentation[$key]);
});
