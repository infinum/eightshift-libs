<?php

namespace Tests\Unit\CustomMeta;

use EightshiftLibs\CustomMeta\AcfMetaCli;
use EightshiftLibs\Helpers\Components;

beforeEach(function () {
	$this->mock = new AcfMetaCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Custom acf meta CLI command will correctly copy the ACF meta class with defaults', function () {
	$meta = $this->mock;
	$meta([], $meta->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$generatedMeta = \file_get_contents(Components::getProjectPaths('srcDestination', 'CustomMeta/TitleAcfMeta.php'));

	$this->assertStringContainsString('class TitleAcfMeta extends AbstractAcfMeta', $generatedMeta);
	$this->assertStringContainsString('acf_add_local_field_group', $generatedMeta);
});


test('Custom acf meta CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
