<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Cli\ParentGroups\CliRun;
use EightshiftLibs\Db\ImportCli;
use Exception;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;
use function Tests\mock;
use function Tests\getDataPath;

beforeEach(function() {
	setBeforeEach();

	$this->import = new ImportCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->import);
});

//---------------------------------------------------------------------------------//

test('getCommandParentName will return correct value', function () {
	expect($this->import->getCommandParentName())
		->toBeString()
		->toEqual(CliRun::COMMAND_NAME);
});

//---------------------------------------------------------------------------------//

test('getCommandName will return correct value', function () {
	expect($this->import->getCommandName())
		->toBeString()
		->toEqual('import');
});

//---------------------------------------------------------------------------------//

test('getDevelopArgs will return correct array', function () {
	expect($this->import->getDevelopArgs([]))
		->toBeArray()
		->toMatchArray([
				'from' => 'production',
				'to' => 'develop',
		]);
});

test('getDefaultArgs will return correct array', function () {
	expect($this->import->getDefaultArgs())
		->toBeArray()
		->toMatchArray([
				'from' => '',
				'to' => '',
				'fileName' => 'setup.json',
		]);
});

//---------------------------------------------------------------------------------//

test('getDoc will return correct array', function () {
	$docs = $this->import->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKey('longdesc')
		->and($docs['shortdesc'])->toEqual('Run database import based on environments.')
		->and(count($docs['synopsis']))->toEqual(2)
		->and($docs['synopsis'][0]['name'])->toEqual('from')
		->and($docs['synopsis'][1]['name'])->toEqual('to');
});

//---------------------------------------------------------------------------------//

test('dbImport will fails if --from parameter is not specified', function () {
	$mock = mock(ImportCli::class)->makePartial();
	$mock->shouldReceive('getProjectConfigRootPath')->andReturn(getDataPath('setup'));

	$mock->__invoke([], []);
})->throws(Exception::class, '--from parameter is mandatory. Please provide one url key from setup.json file.');

test('dbImport will fails if --to parameter is not specified', function () {
	$mock = mock(ImportCli::class)->makePartial();
	$mock->shouldReceive('getProjectConfigRootPath')->andReturn(getDataPath('setup'));

	$mock->__invoke([], [
		'from' => 'staging'
	]);
})->throws(Exception::class, '--to parameter is mandatory. Please provide one url key from setup.json file.');

test('dbImport will fails if setup.json folder is missing', function () {
	$mock = mock(ImportCli::class)->makePartial();
	$mock->shouldReceive('getProjectConfigRootPath')->andReturn(getDataPath('missing'));

	$mock->__invoke([], $mock->getDevelopArgs([]));

})->throws(Exception::class, 'Folder doesn\'t exist on this path: ' . getDataPath('missing'));

test('dbImport will fails if setup.json is missing but folder exists', function () {
	$mock = mock(ImportCli::class)->makePartial();
	$mock->shouldReceive('getProjectConfigRootPath')->andReturn(getDataPath('src'));

	$mock->__invoke([], $mock->getDevelopArgs([]));

})->throws(Exception::class, 'setup.json is missing at this path: ' . getDataPath('src') . '/setup.json');

test('dbImport will fails if setup.json is empty', function () {
	$mock = mock(ImportCli::class)->makePartial();
	$mock->shouldReceive('getProjectConfigRootPath')->andReturn(getDataPath('setup'));

	$mock->__invoke([], array_merge(
		$mock->getDevelopArgs([]),
		[
			'fileName' => 'setup-empty.json',
		]
	));

})->throws(Exception::class, getDataPath('setup') . '/setup-empty.json' . ' is empty.');

test('dbImport will fails if setup.json is missing url keys', function () {
	$mock = mock(ImportCli::class)->makePartial();
	$mock->shouldReceive('getProjectConfigRootPath')->andReturn(getDataPath('setup'));

	$mock->__invoke([], array_merge(
		$mock->getDevelopArgs([]),
		[
			'fileName' => 'setup-missing-urls.json',
		]
	));

})->throws(Exception::class, 'Urls key is missing or empty.');

test('dbImport will fails if setup.json is missing url from key', function () {
	$mock = mock(ImportCli::class)->makePartial();
	$mock->shouldReceive('getProjectConfigRootPath')->andReturn(getDataPath('setup'));

	$mock->__invoke([], array_merge(
		$mock->getDevelopArgs([]),
		[
			'from' => 'test',
		]
	));

})->throws(Exception::class, 'test key is missing or empty in urls.');

test('dbImport will fails if setup.json is missing url to key', function () {
	$mock = mock(ImportCli::class)->makePartial();
	$mock->shouldReceive('getProjectConfigRootPath')->andReturn(getDataPath('setup'));

	$mock->__invoke([], array_merge(
		$mock->getDevelopArgs([]),
		[
			'to' => 'test',
		]
	));

})->throws(Exception::class, 'test key is missing or empty in urls.');

test('dbImport will log correct msg if import is success', function () {
	$mock = mock(ImportCli::class)->makePartial();
	$mock->shouldReceive('getProjectConfigRootPath')->andReturn(getDataPath('setup'));

	$mock->__invoke([], $mock->getDevelopArgs([]));

	expect(\getenv('ES_CLI_SUCCESS_HAPPENED'))->toEqual('Finished! Success!');
});
