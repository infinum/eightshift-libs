<?php

namespace Tests\Unit\CiExclude;

use EightshiftLibs\CiExclude\CiExcludeCli;
use EightshiftLibs\Cli\ParentGroups\CliProject;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new CiExcludeCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

//---------------------------------------------------------------------------------//

test('getCommandParentName will return correct value', function () {
	expect($this->mock->getCommandParentName())
		->toBeString()
		->toEqual(CliProject::COMMAND_NAME);
});

//---------------------------------------------------------------------------------//

test('getCommandName will return correct value', function () {
	expect($this->mock->getCommandName())
		->toBeString()
		->toEqual('ci_exclude');
});

//---------------------------------------------------------------------------------//

test('getDefaultArgs will return correct array', function () {
	$args = $this->mock->getDefaultArgs();

	expect($args)
		->toBeArray()
		->toHaveKeys(['path', 'project_name', 'project_type'])
		->and($args['project_name'])->toEqual('eightshift-boilerplate')
		->and($args['project_type'])->toEqual('themes');
});

//---------------------------------------------------------------------------------//

test('getDoc will return correct array', function () {
	$docs = $this->mock->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKeys(['shortdesc', 'synopsis', 'longdesc'])
		->and(count($docs['synopsis']))->toEqual(3)
		->and($docs['synopsis'][0]['name'])->toEqual('path')
		->and($docs['synopsis'][1]['name'])->toEqual('project_name')
		->and($docs['synopsis'][2]['name'])->toEqual('project_type');
});

//---------------------------------------------------------------------------------//

test('__invoke will will correctly copy example class with default args', function () {
	$mock = $this->mock;
	$mock([], array_merge(
		$this->mock->getDefaultArgs(),
		[
			'path' => Components::getProjectPaths('cliOutput'),
		]
	));

	$output = \file_get_contents(Components::getProjectPaths('cliOutput', 'ci-exclude.txt'));

	expect($output)
		->toContain(
			'eightshift-boilerplate',
			'themes',
		)
		->not->toContain(
			'%project_type%',
			'%project_type%',
		);
});

test('__invoke will will correctly copy example class with custom args', function () {
	$mock = $this->mock;
	$mock([], [
		'path' => Components::getProjectPaths('cliOutput'),
		'project_name' => 'test',
		'project_type' => 'plugins',
	]);

	$output = \file_get_contents(Components::getProjectPaths('cliOutput', 'ci-exclude.txt'));

	expect($output)
		->toContain(
			'test',
			'plugins',
		)
		->not->toContain(
			'%project_type%',
			'%project_type%',
		);
});
