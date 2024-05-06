<?php

namespace Tests\Unit\ModifyAdminAppearance;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\ModifyAdminAppearance\ModifyAdminAppearanceCli;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new ModifyAdminAppearanceCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('ModifyAdminAppearance CLI command will correctly copy the ModifyAdminAppearance class with defaults', function () {
	$mock = $this->mock;
	$mock([], getMockArgs());

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', "ModifyAdminAppearance{$sep}ModifyAdminAppearance.php"));

	$this->assertStringContainsString('class ModifyAdminAppearance implements ServiceInterface', $output);
	$this->assertStringContainsString('@package Infinum\ModifyAdminAppearance', $output);
	$this->assertStringContainsString('namespace Infinum\ModifyAdminAppearance', $output);
	$this->assertStringNotContainsString('footer.php', $output);
});

test('ModifyAdminAppearance CLI command will correctly copy the ModifyAdminAppearance class with set arguments', function () {
	$mock = $this->mock;
	$mock([], getMockArgs([
		'namespace' => 'CoolTheme',
	]));

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', "ModifyAdminAppearance{$sep}ModifyAdminAppearance.php"));

	$this->assertStringContainsString('namespace CoolTheme\ModifyAdminAppearance;', $output);
});


test('ModifyAdminAppearance CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
