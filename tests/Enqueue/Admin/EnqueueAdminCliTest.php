<?php

namespace Tests\Unit\Enqueue\Admin;

use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;

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

	$this->enqueueAdmin = new EnqueueAdminCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
//	deleteCliOutput(\dirname(__FILE__, 4) . '/cliOutput');
});

test('Custom Enqueue Admin CLI command will correctly copy the Enqueue Admin class', function () {
	$admin = $this->enqueueAdmin;
	$admin([], $admin->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedAdmin = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Enqueue/Admin/EnqueueAdmin.php');

	expect($generatedAdmin)
		->toContain('class EnqueueAdmin extends AbstractEnqueueAdmin')
		->toContain('admin_enqueue_scripts')
		->not->toContain('wp_enqueue_scripts');
});


test('Custom Enqueue Admin CLI documentation is correct', function () {
	expect($this->enqueueAdmin->getDoc())->toBeArray();
});
