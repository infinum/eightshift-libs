<?php

namespace Tests\Unit\EnqueueBlock;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Enqueue\Blocks\EnqueueBlocksExample;

use EightshiftBoilerplate\Manifest\ManifestExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

  $this->projectName = 'NewProject';
  $this->projectVersion = '3.1.23';

  \Mockery::mock('alias:WP_CLI')
    ->shouldReceive('success', 'error')
    ->andReturnArg(0);

  $config = \Mockery::mock('alias:EightshiftBoilerplate\Config\Config');
    
  $config
    ->shouldReceive('getProjectName')
    ->andReturn($this->projectName);

  $config
    ->shouldReceive('getProjectVersion')
    ->andReturn($this->projectVersion);

  $manifestPath = dirname(__FILE__, 2) . '/data/public/manifest.json';
  Functions\when('getManifestFilePath')->justReturn($manifestPath);

  $manifest = new ManifestExample();
	$this->eb = new EnqueueBlocksExample($manifest);
});

afterEach(function() {
	Monkey\tearDown();
});


test('Enqueue Blocks\' register method will set hooks.', function () {
	$this->eb->register();

  $this->assertSame(10, has_action('enqueue_block_editor_assets', 'EightshiftBoilerplate\Enqueue\Blocks\EnqueueBlocksExample->enqueueBlockEditorScript()'));
  $this->assertSame(50, has_action('enqueue_block_editor_assets', 'EightshiftBoilerplate\Enqueue\Blocks\EnqueueBlocksExample->enqueueBlockEditorStyle()'));
  $this->assertSame(50, has_action('enqueue_block_assets', 'EightshiftBoilerplate\Enqueue\Blocks\EnqueueBlocksExample->enqueueBlockStyle()'));
  $this->assertSame(10, has_action('wp_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Blocks\EnqueueBlocksExample->enqueueBlockScript()'));
});

test('Enqueue Blocks will get assets prefix.', function () {
	$assetsPrefix = $this->eb->getAssetsPrefix();

  $this->assertSame($assetsPrefix, $this->projectName);
});

test('Enqueue Blocks will get assets version.', function () {
	$assetsVersion= $this->eb->getAssetsVersion();

  $this->assertSame($assetsVersion, $this->projectVersion);
});
