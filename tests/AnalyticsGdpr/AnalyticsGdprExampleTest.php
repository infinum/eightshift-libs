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
	$this->assertNull($this->example->register());
});

test('Analytics and GDPR Settings actions are registered', function () {
	$this->example->register();

	$this->assertTrue(\method_exists($this->example, 'register'));
	$this->assertSame(11, has_action('acf/init', [$this->example, 'createAnalyticsPage']));
	$this->assertSame(12, has_action('acf/init', [$this->example, 'registerAnalytics']));
	
	$this->assertSame(12, has_action('acf/init', [$this->example, 'createGdprModalPage']));
	$this->assertSame(13, has_action('acf/init', [$this->example, 'registerGdprModalSettings']));
});

test('Filter for GDPR Modal data is registered', function () {
	$this->example->register();

	$this->assertSame(10, has_filter(AnalyticsGdprExample::GET_GDPR_MODAL_DATA, 'EightshiftBoilerplate\AnalyticsGdpr\AnalyticsGdprExample->prepareGdprModalData()'));
});

test('Method for adding Analytics Settings as ACF Options page exists', function () {
	$this->example->createAnalyticsPage();

	$this->assertTrue(\method_exists($this->example, 'createAnalyticsPage'));
	$this->assertTrue(\function_exists('acf_add_options_page'));
});

test('Method for adding ACF fields to Analytics Settings page exists', function () {
	$this->example->registerAnalytics();

	$this->assertTrue(\method_exists($this->example, 'registerAnalytics'));
	$this->assertTrue(\function_exists('acf_add_local_field_group'));
});


test('Method for adding GDPR Settings as ACF Options subpage exists', function () {
	$this->example->createGdprModalPage();

	$this->assertTrue(\method_exists($this->example, 'createGdprModalPage'));
	$this->assertTrue(\function_exists('acf_add_options_sub_page'));
	$this->assertTrue(\function_exists('current_user_can'));
});

test('Method for adding ACF fields to GDPR Settings page exists', function () {
	$this->example->registerGdprModalSettings();

	$this->assertTrue(\method_exists($this->example, 'registerGdprModalSettings'));
	$this->assertTrue(\function_exists('acf_add_local_field_group'));
});

test('Method for preparing GDPR modal data exists and will return an array', function () {
	$gdprModalData = $this->example->prepareGdprModalData();

	$this->assertTrue(\method_exists($this->example, 'prepareGdprModalData'));
	$this->assertTrue(\function_exists('get_field'));
	$this->assertTrue(\function_exists('esc_html__'));

	$this->assertIsArray($gdprModalData);
});
