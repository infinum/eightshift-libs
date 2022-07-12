<?php

namespace Tests\Unit\Enqueue\Admin;

use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new EnqueueAdminCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Custom Enqueue Admin CLI command will correctly copy the Enqueue Admin class', function () {
	$admin = $this->mock;
	$admin([], $admin->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$generatedAdmin = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Enqueue/Admin/EnqueueAdmin.php');

	$this->assertStringContainsString('class EnqueueAdmin extends AbstractEnqueueAdmin', $generatedAdmin);
	$this->assertStringContainsString('admin_enqueue_scripts', $generatedAdmin);
	$this->assertStringNotContainsString('wp_enqueue_scripts', $generatedAdmin);
});


test('Custom Enqueue Admin CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
