<?php

namespace Tests\Unit\Cli;

use Brain\Monkey;
use EightshiftLibs\Cli\AbstractCli;

use function Tests\deleteCliOutput;

class AbstractTest extends AbstractCli {
	public function __construct(string $commandParentName)
	{
		parent::__construct($commandParentName);
	}

	public function __invoke(array $args, array $assocArgs)
	{
		// TODO: Implement __invoke() method.
	}

	public function getDoc(): array
	{
		return [];
	}
};

/**
 * Mock before tests.
 */
beforeEach(function () {
	Monkey\setUp();

	$wpCliMock = \Mockery::mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnUsing(function ($message) {
			putenv("ERROR_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('log')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturn(putenv("INIT_CALLED=true"));
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);

	putenv('ERROR_HAPPENED');
	putenv('INIT_CALLED');

	Monkey\tearDown();
});


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
