<?php

namespace Tests\Unit\Config;

use EightshiftBoilerplate\Config\ConfigExample;

beforeEach(function() {
	$this->mock = new ConfigExample();
});

afterEach(function () {
	unset($this->mock);
});

test('Is project name defined and a string', function () {
	$this->assertNotEmpty($this->mock::getProjectName());
	$this->assertIsString(\gettype($this->mock::getProjectName()));
});

test('Is project version defined and a string', function () {
	$this->assertNotEmpty($this->mock::getProjectVersion());
	$this->assertIsString(\gettype($this->mock::getProjectVersion()));
});

test('Is project REST namespace defined, a string and same as project name', function () {
	$this->assertNotEmpty($this->mock::getProjectRoutesNamespace());
	$this->assertIsString(\gettype($this->mock::getProjectRoutesNamespace()));
	$this->assertSame($this->mock::getProjectName(), $this->mock::getProjectRoutesNamespace());
});

test('Is project REST route version defined and a string', function () {
	$this->assertNotEmpty($this->mock::getProjectRoutesVersion());
	$this->assertIsString(\gettype($this->mock::getProjectRoutesVersion()));
	$this->assertStringContainsString('v', $this->mock::getProjectRoutesVersion());
});

test('Is project path defined and readable', function () {
	$this->assertNotEmpty($this->mock::getProjectPath());
	$this->assertDirectoryIsReadable($this->mock::getProjectPath());
});

test('Is custom project path defined and readable', function () {
	$this->assertNotEmpty($this->mock::getProjectPath());
	$this->assertDirectoryIsReadable($this->mock::getProjectPath('tests'));
});

test('If non-existent path throws exception', function () {
	$this->mock::getProjectPath('bla/');
})->throws(\EightshiftLibs\Exception\InvalidPath::class);
