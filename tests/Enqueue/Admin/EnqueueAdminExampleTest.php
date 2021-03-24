<?php

namespace Tests\Unit\Enqueue\Admin;

use Brain\Monkey;
use EightshiftBoilerplate\Enqueue\Admin\EnqueueAdminExample;
use EightshiftBoilerplate\Manifest\ManifestExample;

use function Tests\setupMocks;
use function Tests\mock;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$manifest = new ManifestExample();
	$this->example = new EnqueueAdminExample($manifest);
});

afterEach(function() {
	Monkey\tearDown();
});


test('Register method will call login_enqueue_scripts and admin_enqueue_scripts hook', function () {
	$this->example->register();

	$this->assertSame(10, has_action('login_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Admin\EnqueueAdminExample->enqueueStyles()'));
	$this->assertSame(50, has_action('admin_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Admin\EnqueueAdminExample->enqueueStyles()'));
	$this->assertSame(10, has_action('admin_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Admin\EnqueueAdminExample->enqueueScripts()'));
	$this->assertNotSame(10, has_action('wp_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Admin\EnqueueAdminExample->enqueueStyles()'));
	$this->assertNotSame(10, has_action('wp_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Admin\EnqueueAdminExample->enqueueScripts()'));
});
