<?php

namespace Tests\Unit\I18n;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\I18n\I18nCli;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new I18nCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('I18n CLI command will correctly copy the I18n class with defaults', function () {
	$mock = $this->mock;
	$mock([], getMockArgs());

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', "I18n{$sep}I18n.php"));

	$this->assertStringContainsString('class I18n implements ServiceInterface', $output);
	$this->assertStringContainsString('@package Infinum\I18n', $output);
	$this->assertStringContainsString('namespace Infinum\I18n', $output);
	$this->assertStringNotContainsString('footer.php', $output);
});

test('I18n CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
