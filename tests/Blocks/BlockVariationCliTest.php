<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\UseVariationCli;
use EightshiftLibs\Cli\ParentGroups\CliBlocks;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new UseVariationCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

//---------------------------------------------------------------------------------//

test('getCommandParentName will return correct value', function () {
	expect($this->mock->getCommandParentName())
		->toBeString()
		->toEqual(CliBlocks::COMMAND_NAME);
});

//---------------------------------------------------------------------------------//

test('getCommandName will return correct value', function () {
	expect($this->mock->getCommandName())
		->toBeString()
		->toEqual('use-variation');
});

//---------------------------------------------------------------------------------//

test('getDefaultArgs will return correct array', function () {
	expect($this->mock->getDefaultArgs())
		->toBeArray()
		->toMatchArray([
			'name' => 'button-block',
		]);
});

//---------------------------------------------------------------------------------//

test('getDoc will return correct array', function () {
	$docs = $this->mock->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKeys(['shortdesc', 'synopsis', 'longdesc'])
		->and(count($docs['synopsis']))->toEqual(1)
		->and($docs['synopsis'][0]['name'])->toEqual('name');
});

//---------------------------------------------------------------------------------//

test('__invoke will correctly copy example variation with default args', function () {
	$mock = $this->mock;
	$mock([], $mock->getDefaultArgs());

	$name = $this->mock->getDefaultArgs()['name'];

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('blocksDestinationVariations', "{$name}{$sep}manifest.json"));

	expect($output)
		->toContain(
			'button',
			'button-full-width',
			'Button Full Width',
		)
		->and(\getenv('ES_CLI_LOG_HAPPENED'))->toContain('Please run');
});
