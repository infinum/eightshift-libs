<?php

namespace Tests\Unit\CustomMeta;

use EightshiftLibs\CustomMeta\AcfMetaCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new AcfMetaCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Custom acf meta CLI command will correctly copy the ACF meta class with defaults', function () {
	$meta = $this->mock;
	$meta([], $meta->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$generatedMeta = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomMeta/TitleAcfMeta.php');

	$this->assertStringContainsString('class TitleAcfMeta extends AbstractAcfMeta', $generatedMeta);
	$this->assertStringContainsString('acf_add_local_field_group', $generatedMeta);
});


test('Custom acf meta CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
