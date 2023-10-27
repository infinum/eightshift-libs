<?php

namespace Tests\Unit\Cli;

use EightshiftLibs\Cli\AbstractCli;
use ReflectionClass;
use RuntimeException;


class AbstractTest extends AbstractCli {
	protected string $fileContents = 'use EightshiftBoilerplateVendor\Service; use EightshiftBoilerplate\Test;';

	public function __construct(string $commandParentName)
	{
		parent::__construct($commandParentName);
	}

	public function __invoke(array $args, array $assocArgs)
	{
	}

	public function getDoc(): array
	{
		return [];
	}

	public function getCommandParentName(): string
	{
		return '';
	}

	public function getCommandName(): string
	{
		return '';
	}

};





test('Register method will call the cli_init hooks', function() {
	$abstractMock = new AbstractTest('test');

	$abstractMock->register();

	$this->assertSame(10, has_action('cli_init', 'Tests\Unit\Cli\AbstractTest->registerCommand()'));
});


test('Global CLI synopsis works', function() {
	$abstractMock = new AbstractTest('test');

	$synopsis = $abstractMock->getGlobalSynopsis();

	$this->assertIsArray($synopsis);
	$this->assertArrayHasKey('synopsis', $synopsis);

	foreach ($synopsis['synopsis'] as $descriptions) {
		$this->assertArrayHasKey('type', $descriptions);
		$this->assertArrayHasKey('name', $descriptions);
		$this->assertArrayHasKey('description', $descriptions);
		$this->assertArrayHasKey('optional', $descriptions);
	}
});


test('Prepare command docs fails if shortdesc doesn\'t exist', function() {
	$abstractMock = new AbstractTest('test');

	$abstractMock->prepareCommandDocs([], []);
})->throws(RuntimeException::class, 'CLI Short description is missing.');


test('Prepare command docs returns correct doc', function() {
	$abstractMock = new AbstractTest('test');

	$docs = [
		'shortdesc' => 'Some description',
		'synopsis' => [
			[
				'type' => 'assoc',
				'name' => 'random',
				'description' => 'Random description.',
				'optional' => true,
				'default' => 'random',
			],
		],
	];

	$preparedDocs = $abstractMock->prepareCommandDocs($docs, $abstractMock->getGlobalSynopsis());

	$this->assertIsArray($preparedDocs);
	$this->assertArrayHasKey('shortdesc', $preparedDocs);
	$this->assertArrayHasKey('synopsis', $preparedDocs);

	$addedSynopsis = \array_filter($preparedDocs['synopsis'], function($descArr) {
		return $descArr['name'] === 'random';
	});
	// Check if the synopsis was added to the global one.
	$this->assertNotEmpty($addedSynopsis);
});


test('Manually preparing arguments works', function() {
	$abstractMock = new AbstractTest('test');

	$output = $abstractMock->prepareArgsManual([
		'color' => '#EFEFEF',
	]);

	$this->assertSame('--color=\'#EFEFEF\' ', $output);

	$outputEmpty = $abstractMock->prepareArgsManual([]);

	$this->assertEmpty($outputEmpty, 'Argument output should be empty');
});


test('Block full file list helper works', function() {
	$abstractMock = new AbstractTest('test');

	$output = $abstractMock->getFullBlocksFiles('button');

	$this->assertIsArray($output);

	$this->assertTrue(\array_key_exists('button.php', \array_flip($output)), 'button.php is missing.');
	$this->assertTrue(\array_key_exists('button-block.js', \array_flip($output)), 'button-block.js is missing.');
	$this->assertTrue(\array_key_exists('button-hooks.js', \array_flip($output)), 'button-hooks.js is missing.');
	$this->assertTrue(\array_key_exists('button-transforms.js', \array_flip($output)), 'button-transforms.js is missing.');
	$this->assertTrue(\array_key_exists('button.js', \array_flip($output)), 'button.js is missing.');
	$this->assertTrue(\array_key_exists('components/button-editor.js', \array_flip($output)), 'components/button-editor.js is missing.');
	$this->assertTrue(\array_key_exists('components/button-toolbar.js', \array_flip($output)), 'components/button-toolbar.js is missing.');
	$this->assertTrue(\array_key_exists('components/button-options.js', \array_flip($output)), 'components/button-options.js is missing.');
});

test('Preparing slug works', function($slugs) {
	$abstractMock = new AbstractTest('test');

	$output = $abstractMock->prepareSlug($slugs);

	$this->assertIsString($output);
	$this->assertFalse(\strpos($output, '_'), 'Prepared string contains _');
	$this->assertFalse(\strpos($output, ' '), 'Prepared string contains empty space');
})->with('inputSlugs');


test('Register command fails if class doesn\'t exist', function() {
	$abstractMock = new AbstractTest('nonexistent');

	$abstractMock->registerCommand();
})->throws(RuntimeException::class);


test('Getting vendor prefix works correctly if set', function() {
	$abstractMock = new AbstractTest('nonexistent');

	$prefix = $abstractMock->getVendorPrefix(['vendor_prefix' => 'test']);

	$this->assertIsString($prefix);
	$this->assertSame('test', $prefix);
});


test('Replacing use in frontend libs views works', function() {
	$abstractMock = new AbstractTest('test');

	$abstractMock->renameUseFrontendLibs([]);

	$reflection = new ReflectionClass($abstractMock);
	$property = $reflection->getProperty('fileContents');
	$property->setAccessible(true);
	$contents = $property->getValue($abstractMock);

	$this->assertSame('use EightshiftLibs\Service; use EightshiftLibs\Test;', $contents);
});
