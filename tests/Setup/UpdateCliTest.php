<?php

namespace Tests\Unit\Setup;

use EightshiftLibs\Setup\UpdateCli;
use Exception;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new UpdateCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});


test('Update CLI command will correctly throw an exception if setup.json does not exist or has the wrong filename', function () {
	$update = $this->mock;
	$update([], []);
})->throws(Exception::class);

test('Update CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
