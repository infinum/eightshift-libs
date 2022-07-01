<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Cli\ParentGroups\CliRun;
use EightshiftLibs\Db\ImportCli;
use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Setup\SetupCli;
use Exception;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function() {
	setBeforeEach();

	$this->mock = new ImportCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

//---------------------------------------------------------------------------------//

test('getCommandParentName will return correct value', function () {
	expect($this->mock->getCommandParentName())
		->toBeString()
		->toEqual(CliRun::COMMAND_NAME);
});

//---------------------------------------------------------------------------------//

test('getCommandName will return correct value', function () {
	expect($this->mock->getCommandName())
		->toBeString()
		->toEqual('import');
});

//---------------------------------------------------------------------------------//

test('getDefaultArgs will return correct array', function () {
	expect($this->mock->getDefaultArgs())
		->toBeArray()
		->toHaveKeys(['from', 'to', 'setup_file']);
});

//---------------------------------------------------------------------------------//

test('getDoc will return correct array', function () {
	$docs = $this->mock->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKeys(['shortdesc', 'synopsis', 'longdesc'])
		->and(count($docs['synopsis']))->toEqual(2)
		->and($docs['synopsis'][0]['name'])->toEqual('from')
		->and($docs['synopsis'][1]['name'])->toEqual('to');
});

//---------------------------------------------------------------------------------//

test('__invoke will log correct msg if import is success', function () {
	(new SetupCli('boilerplate'))->__invoke([], [
		'root' => Components::getProjectPaths('cliOutput', 'setup'),
		'source_path' => Components::getProjectPaths('testsData', 'setup'),
	]);

	$mock = $this->mock;
	$mock([], array_merge(
		$mock->getDefaultArgs(),
		[
			'from' => 'production',
			'to' => 'develop',
			'setup_file' => Components::getProjectPaths('cliOutput', 'setup' . \DIRECTORY_SEPARATOR . 'setup.json'),
		]
	));

	expect(\getenv('ES_CLI_SUCCESS_HAPPENED'))->toEqual('Finished! Success!');
});

test('__invoke will fail if --from parameter is not specified', function () {
	$mock = $this->mock;
	$mock([], [
		'to' => 'develop',
	]);
})->throws(Exception::class, '--from parameter is mandatory. Please provide one url key from setup.json file.');

test('__invoke will fail if --to parameter is not specified', function () {
	$mock = $this->mock;
	$mock([], [
		'from' => 'staging'
	]);
})->throws(Exception::class, '--to parameter is mandatory. Please provide one url key from setup.json file.');

test('__invoke will fail if setup.json is empty', function () {
	(new SetupCli('boilerplate'))->__invoke([], [
		'root' => Components::getProjectPaths('cliOutput', 'setup'),
		'file_name' => 'setup-empty.json',
		'source_path' => Components::getProjectPaths('testsData', 'setup'),
	]);

	$mock = $this->mock;
	$mock([], [
		'from' => 'production',
		'to' => 'develop',
		'setup_file' => Components::getProjectPaths('cliOutput', 'setup' . \DIRECTORY_SEPARATOR . 'setup-empty.json'),
	]);

})->throws(Exception::class, 'Setup file is empty on this path: ' . Components::getProjectPaths('cliOutput', 'setup' . \DIRECTORY_SEPARATOR . 'setup-empty.json') . '');

test('__invoke will fail if setup.json is missing url keys', function () {
	(new SetupCli('boilerplate'))->__invoke([], [
		'root' => Components::getProjectPaths('cliOutput', 'setup'),
		'file_name' => 'setup-missing-urls.json',
		'source_path' => Components::getProjectPaths('testsData', 'setup'),
	]);

	$mock = $this->mock;
	$mock([], [
		'from' => 'production',
		'to' => 'develop',
		'setup_file' => Components::getProjectPaths('cliOutput', 'setup' . \DIRECTORY_SEPARATOR . 'setup-missing-urls.json'),
	]);

})->throws(Exception::class, 'Urls key is missing or empty.');

test('__invoke will fail if setup.json is missing url "from" key', function () {
	(new SetupCli('boilerplate'))->__invoke([], [
		'root' => Components::getProjectPaths('cliOutput', 'setup'),
		'source_path' => Components::getProjectPaths('testsData', 'setup'),
	]);

	$mock = $this->mock;
	$mock([], [
		'from' => 'test',
		'to' => 'develop',
	]);

})->throws(Exception::class, 'test key is missing or empty in urls.');

test('__invoke will fail if setup.json is missing url "to" key', function () {
	(new SetupCli('boilerplate'))->__invoke([], [
		'root' => Components::getProjectPaths('cliOutput', 'setup'),
		'source_path' => Components::getProjectPaths('testsData', 'setup'),
	]);

	$mock = $this->mock;
	$mock([], [
		'from' => 'production',
		'to' => 'test',
	]);

})->throws(Exception::class, 'test key is missing or empty in urls.');
