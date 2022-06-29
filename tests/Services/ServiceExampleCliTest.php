<?php

namespace Tests\Unit\Services;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Services\ServiceExampleCli;

use function Tests\deleteCliOutput;
use function Tests\mock;
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new ServiceExampleCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Services CLI command will correctly copy the Services class with defaults', function () {
	$mock = $this->mock;
	$mock([], $mock->getDefaultArgs());

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('testsOutput', "src{$sep}TestFolder{$sep}TMP{$sep}TestTest.php"));

	$this->assertStringContainsString('class TestTest implements ServiceInterface', $output);
	$this->assertStringContainsString('namespace EightshiftLibs\TestFolder\TMP', $output);
	$this->assertStringContainsString('@package EightshiftLibs\TestFolder\TMP', $output);
	$this->assertStringNotContainsString('footer.php', $output);
});

test('Services CLI command will correctly copy the Services class with set arguments', function () {
	$mock = $this->mock;
	$mock([], [
		'namespace' => 'CoolTheme',
		'folder' => 'FolderName',
		'file_name' => 'FileName',
	]);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('testsOutput', "src{$sep}FolderName{$sep}FileName.php"));

	$this->assertStringContainsString('class FileName implements ServiceInterface', $output);
	$this->assertStringContainsString('namespace CoolTheme\FolderName', $output);
	$this->assertStringContainsString('@package CoolTheme\FolderName', $output);
});

test('Services CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
