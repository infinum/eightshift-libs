<?php

namespace Tests\Unit\Media;

use EightshiftLibs\Media\RegenerateWebPMediaCli;

beforeEach(function () {
	$this->regenerateWebPMediaCli = new RegenerateWebPMediaCli('boilerplate');
});

test('Check if CLI command documentation is correct.', function () {
	expect($this->regenerateWebPMediaCli->getDoc())->toBeArray();
});
