<?php

namespace Tests\Unit\Block;

use EightshiftLibs\Blocks\BlockVariationCli;
use EightshiftLibs\Cli\ParentGroups\CliBlocks;

use function Tests\getCliOutputFile;
use function Tests\mock;
use function Tests\setAfterEach;
use function Tests\setBeforeEach;
use function Tests\getCliOutputPath;
use function Tests\getDataPath;
use function Tests\getProjectComposerFile;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new BlockVariationCli('boilerplate');
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
		->toEqual('variation');
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
	$mock = mock(BlockVariationCli::class)->makePartial();
	$mock->shouldReceive('getProjectRootPath')->andReturn(getCliOutputPath());
	$mock->shouldReceive('getFrontendLibsBlockPath')->andReturn(getDataPath());
	$mock->shouldReceive('getComposer')->andReturn(getProjectComposerFile());
	$mock([], $this->mock->getDefaultArgs([]));

	expect(getCliOutputFile('src/Blocks/variations/button-block/manifest.json'))
		->toContain(
			'button',
			'button-full-width',
			'Button Full Width',
		)
		->and(\getenv('ES_CLI_SUCCESS_HAPPENED'))->toContain('Please start');
});

test('__invoke will throw error if variation source folder is missing', function () {
	$mock = mock(BlockVariationCli::class)->makePartial();
	$mock->shouldReceive('getProjectRootPath')->andReturn(getCliOutputPath('test'));
	$mock->shouldReceive('getFrontendLibsBlockPath')->andReturn(getDataPath('test'));
	$mock->shouldReceive('getComposer')->andReturn(getProjectComposerFile());
	$mock([], $this->mock->getDefaultArgs([]));
})->throws('The variation source folder is missing!');
