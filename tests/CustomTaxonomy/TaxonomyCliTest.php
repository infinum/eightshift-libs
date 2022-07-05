<?php

namespace Tests\Unit\CustomTaxonomy;

use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\CustomTaxonomy\TaxonomyCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;
use function Tests\getCliOutputFile;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new TaxonomyCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

//---------------------------------------------------------------------------------//

test('getCommandParentName will return correct value', function () {
	expect($this->mock->getCommandParentName())
		->toBeString()
		->toEqual(CliCreate::COMMAND_NAME);
});

//---------------------------------------------------------------------------------//

test('getCommandName will return correct value', function () {
	expect($this->mock->getCommandName())
		->toBeString()
		->toEqual('taxonomy');
});

//---------------------------------------------------------------------------------//

test('getDefaultArgs will return correct array', function () {
	expect($this->mock->getDefaultArgs())
		->toBeArray()
		->toMatchArray([
			'label' => 'Location',
			'plural_label' => 'Locations',
			'slug' => 'location',
			'rest_endpoint_slug' => 'locations',
			'post_type_slug' => 'post',
		]);
});

//---------------------------------------------------------------------------------//

test('getDoc will return correct array', function () {
	$docs = $this->mock->getDoc();

	expect($docs)
		->toBeArray()
		->toHaveKeys(['shortdesc', 'synopsis', 'longdesc'])
		->and(count($docs['synopsis']))->toEqual(5)
		->and($docs['synopsis'][0]['name'])->toEqual('label')
		->and($docs['synopsis'][1]['name'])->toEqual('plural_label')
		->and($docs['synopsis'][2]['name'])->toEqual('slug')
		->and($docs['synopsis'][3]['name'])->toEqual('rest_endpoint_slug')
		->and($docs['synopsis'][4]['name'])->toEqual('post_type_slug');
});

//---------------------------------------------------------------------------------//

test('__invoke will will correctly copy example class with default args', function () {
	$mock = $this->mock;
	$mock([], $this->mock->getDefaultArgs());

	expect(getCliOutputFile('src/CustomTaxonomy/LocationTaxonomy.php'))
		->toContain(
			'class LocationTaxonomy',
			'Location',
			'Locations',
			'location',
			'locations',
			'post'
		)
		->not->toContain(
			'class TaxonomyExample',
			'%label%',
			'%plural_label%',
			'%slug%',
			'%rest_endpoint_slug%',
			'%post_type_slug%',
		);
});

test('__invoke will will correctly copy example class with custom args', function () {
	$mock = $this->mock;
	$mock([], [
		'label' => 'Test',
		'plural_label' => 'Tests',
		'slug' => 'test',
		'rest_endpoint_slug' => 'tests',
		'post_type_slug' => 'product',
	]);

	expect(getCliOutputFile('src/CustomTaxonomy/TestTaxonomy.php'))
		->toContain(
			'class TestTaxonomy',
			'Test',
			'Tests',
			'test',
			'tests',
			'product'
		)
		->not->toContain(
			'class TaxonomyExample',
			'%label%',
			'%plural_label%',
			'%slug%',
			'%rest_endpoint_slug%',
			'%post_type_slug%',
		);
});
