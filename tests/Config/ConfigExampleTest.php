<?php

namespace Tests\Unit\Config;

use Brain\Monkey;
use EightshiftBoilerplate\Config\ConfigExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->example = new ConfigExample();
});

afterEach(function() {
	Monkey\tearDown();
});


test('Is project name defined and a string', function () {
	$this->assertNotEmpty($this->example::getProjectName());
	$this->assertIsString(gettype($this->example::getProjectName()));
});

test('Is project version defined and a string', function () {
	$this->assertNotEmpty($this->example::getProjectVersion());
	$this->assertIsString(gettype($this->example::getProjectVersion()));
});

test('Is project REST namespace defined, a string and same as project name', function () {
	$this->assertNotEmpty($this->example::getProjectRoutesNamespace());
	$this->assertIsString(gettype($this->example::getProjectRoutesNamespace()));
	$this->assertEquals($this->example::getProjectName(), $this->example::getProjectRoutesNamespace());
});

test('Is project REST route version defined and a string', function () {
	$this->assertNotEmpty($this->example::getProjectRoutesVersion());
	$this->assertIsString(gettype($this->example::getProjectRoutesVersion()));
	$this->assertStringContainsString('v', $this->example::getProjectRoutesVersion());
});

test('Is project path defined and readable', function () {
	$this->assertNotEmpty($this->example::getProjectPath());
	$this->assertDirectoryIsReadable($this->example::getProjectPath());
});

test('Is custom project path defined and readable', function () {
	$this->assertNotEmpty($this->example::getProjectPath());
	$this->assertDirectoryIsReadable($this->example::getProjectPath('data/'));
});

test('If non-existent path throws exception', function () {
	$this->example::getProjectPath('bla/');
})->throws(\EightshiftLibs\Exception\InvalidPath::class);
