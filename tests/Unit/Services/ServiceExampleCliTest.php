<?php

namespace Tests\Unit\Services;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Services\ServiceExampleCli;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new ServiceExampleCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Services CLI command will correctly copy the Services class with defaults', function () {
	$mock = $this->mock;
	$mock([], getMockArgs());

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', "TestFolder{$sep}Tmp{$sep}TestTest.php"));

	$this->assertStringContainsString('class TestTest implements ServiceInterface', $output);
	$this->assertStringContainsString('namespace Infinum\TestFolder\Tmp', $output);
	$this->assertStringContainsString('@package Infinum\TestFolder\Tmp', $output);
	$this->assertStringNotContainsString('footer.php', $output);
});

test('Services CLI command will correctly copy the Services class with set arguments', function () {
	$mock = $this->mock;
	$mock([], getMockArgs([
		'namespace' => 'CoolTheme',
		'folder' => 'FolderName',
		'file_name' => 'FileName',
	]));

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', "FolderName{$sep}FileName.php"));

	$this->assertStringContainsString('class FileName implements ServiceInterface', $output);
	$this->assertStringContainsString('namespace CoolTheme\FolderName', $output);
	$this->assertStringContainsString('@package CoolTheme\FolderName', $output);
});

test('Services CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
