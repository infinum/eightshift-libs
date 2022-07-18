<?php

namespace Tests\Unit\AnalyticsGdpr;

use Brain\Monkey;
use EightshiftBoilerplate\AnalyticsGdpr\AnalyticsGdprExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->example = new AnalyticsGdprExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will bail out if ACF is not registered/activated', function () {
	expect($this->example->register())->toBeNull();
});

test('Analytics and GDPR Settings actions are registered', function () {
	$this->example->register();

	expect(\method_exists($this->example, 'register'))->toBeTrue();
	expect(has_action('acf/init', [$this->example, 'createAnalyticsPage']))->toBe(11);
	expect(has_action('acf/init', [$this->example, 'registerAnalytics']))->toBe(12);

	expect(has_action('acf/init', [$this->example, 'createGdprModalPage']))->toBe(12);
	expect(has_action('acf/init', [$this->example, 'registerGdprModalSettings']))->toBe(13);
});

test('Filter for GDPR Modal data is registered', function () {
	$this->example->register();

	expect(has_filter(AnalyticsGdprExample::GET_GDPR_MODAL_DATA, 'EightshiftBoilerplate\AnalyticsGdpr\AnalyticsGdprExample->prepareGdprModalData()'))->toBe(10);
});

test('Method for adding Analytics Settings as ACF Options page exists', function () {
	$this->example->createAnalyticsPage();

	expect(\method_exists($this->example, 'createAnalyticsPage'))->toBeTrue();
});

test('Method for adding ACF fields to Analytics Settings page exists', function () {
	$this->example->registerAnalytics();

	expect(\method_exists($this->example, 'registerAnalytics'))->toBeTrue();
});


test('Method for adding GDPR Settings as ACF Options subpage exists', function () {
	$this->example->createGdprModalPage();

	expect(\method_exists($this->example, 'createGdprModalPage'))->toBeTrue();
});

test('Method for adding ACF fields to GDPR Settings page exists', function () {
	$this->example->registerGdprModalSettings();

	expect(\method_exists($this->example, 'registerGdprModalSettings'))->toBeTrue();
});

test('Method for preparing GDPR modal data exists and will return an array', function () {
	$gdprModalData = $this->example->prepareGdprModalData();

	expect(\method_exists($this->example, 'prepareGdprModalData'))->toBeTrue();

	expect($gdprModalData)->toBeArray();
});
