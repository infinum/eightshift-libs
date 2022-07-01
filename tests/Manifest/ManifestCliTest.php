<?php

namespace Tests\Unit\Manifest;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Manifest\ManifestCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new ManifestCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Manifest CLI command will correctly copy the Manifest class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}Manifest{$sep}Manifest.php"));

	$this->assertStringContainsString('class Manifest extends AbstractManifest', $output);
	$this->assertStringContainsString('setAssetsManifestRaw', $output);
	$this->assertStringContainsString('manifest-item', $output);
	$this->assertStringNotContainsString('random string', $output);
});

test('Manifest CLI command will correctly copy the Manifest class with set arguments', function () {
	$mock = $this->mock;
	$mock([], [
		'namespace' => 'MyTheme',
	]);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}Manifest{$sep}Manifest.php"));

	$this->assertStringContainsString('namespace MyTheme\Manifest;', $output);
});

test('Manifest CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
