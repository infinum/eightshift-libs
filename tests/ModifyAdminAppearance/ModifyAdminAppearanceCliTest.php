<?php

namespace Tests\Unit\ModifyAdminAppearance;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\ModifyAdminAppearance\ModifyAdminAppearanceCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new ModifyAdminAppearanceCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('ModifyAdminAppearance CLI command will correctly copy the ModifyAdminAppearance class with defaults', function () {
	$mock = $this->mock;
	$mock([], []);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}ModifyAdminAppearance{$sep}ModifyAdminAppearance.php"));

	$this->assertStringContainsString('class ModifyAdminAppearance implements ServiceInterface', $output);
	$this->assertStringContainsString('@package EightshiftLibs\ModifyAdminAppearance', $output);
	$this->assertStringContainsString('namespace EightshiftLibs\ModifyAdminAppearance', $output);
	$this->assertStringNotContainsString('footer.php', $output);
});

test('ModifyAdminAppearance CLI command will correctly copy the ModifyAdminAppearance class with set arguments', function () {
	$mock = $this->mock;
	$mock([], [
		'namespace' => 'CoolTheme',
	]);

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}ModifyAdminAppearance{$sep}ModifyAdminAppearance.php"));

	$this->assertStringContainsString('namespace CoolTheme\ModifyAdminAppearance;', $output);
});


test('ModifyAdminAppearance CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
