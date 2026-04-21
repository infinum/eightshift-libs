<?php

/**
 * Tests for AbstractEscapedView class
 *
 * @package EightshiftLibs\Tests\Unit\View
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\View;

use Brain\Monkey;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\View\AbstractEscapedView;

/**
 * AbstractEscapedViewTest class
 */
class AbstractEscapedViewTest extends BaseTestCase
{
	/**
	 * Set up before each test
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tear down after each test
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test that AbstractEscapedView implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$view = new ConcreteEscapedView();

		$this->assertInstanceOf(ServiceInterface::class, $view);
	}

	/**
	 * Test that SVG constant exists
	 *
	 * @return void
	 */
	public function testSvgConstantExists(): void
	{
		$this->assertTrue(\defined('EightshiftLibs\View\AbstractEscapedView::SVG'));
		$this->assertIsArray(AbstractEscapedView::SVG);
	}

	/**
	 * Test that SVG constant contains expected tags
	 *
	 * @return void
	 */
	public function testSvgConstantContainsExpectedTags(): void
	{
		$this->assertArrayHasKey('svg', AbstractEscapedView::SVG);
		$this->assertArrayHasKey('path', AbstractEscapedView::SVG);
		$this->assertArrayHasKey('defs', AbstractEscapedView::SVG);
		$this->assertArrayHasKey('circle', AbstractEscapedView::SVG);
		$this->assertArrayHasKey('ellipse', AbstractEscapedView::SVG);
		$this->assertArrayHasKey('line', AbstractEscapedView::SVG);
		$this->assertArrayHasKey('g', AbstractEscapedView::SVG);
		$this->assertArrayHasKey('filter', AbstractEscapedView::SVG);
		$this->assertArrayHasKey('use', AbstractEscapedView::SVG);
		$this->assertArrayHasKey('mask', AbstractEscapedView::SVG);
	}

	/**
	 * Test that SVG svg tag has expected attributes
	 *
	 * @return void
	 */
	public function testSvgTagHasExpectedAttributes(): void
	{
		$svgAttrs = AbstractEscapedView::SVG['svg'];

		$this->assertArrayHasKey('viewbox', $svgAttrs);
		$this->assertArrayHasKey('xmlns', $svgAttrs);
		$this->assertArrayHasKey('height', $svgAttrs);
		$this->assertArrayHasKey('width', $svgAttrs);
		$this->assertArrayHasKey('class', $svgAttrs);
		$this->assertArrayHasKey('fill', $svgAttrs);
	}

	/**
	 * Test that FORM constant exists and contains expected tags
	 *
	 * @return void
	 */
	public function testFormConstantContainsExpectedTags(): void
	{
		$this->assertIsArray(AbstractEscapedView::FORM);
		$this->assertArrayHasKey('input', AbstractEscapedView::FORM);
		$this->assertArrayHasKey('select', AbstractEscapedView::FORM);
		$this->assertArrayHasKey('option', AbstractEscapedView::FORM);
		$this->assertArrayHasKey('form', AbstractEscapedView::FORM);
		$this->assertArrayHasKey('iframe', AbstractEscapedView::FORM);
		$this->assertArrayHasKey('button', AbstractEscapedView::FORM);
	}

	/**
	 * Test that FORM input tag has expected attributes
	 *
	 * @return void
	 */
	public function testFormInputTagHasExpectedAttributes(): void
	{
		$inputAttrs = AbstractEscapedView::FORM['input'];

		$this->assertArrayHasKey('name', $inputAttrs);
		$this->assertArrayHasKey('value', $inputAttrs);
		$this->assertArrayHasKey('type', $inputAttrs);
		$this->assertArrayHasKey('placeholder', $inputAttrs);
		$this->assertArrayHasKey('class', $inputAttrs);
		$this->assertArrayHasKey('id', $inputAttrs);
	}

	/**
	 * Test that IFRAME constant exists and contains iframe tag
	 *
	 * @return void
	 */
	public function testIframeConstantContainsIframeTag(): void
	{
		$this->assertIsArray(AbstractEscapedView::IFRAME);
		$this->assertArrayHasKey('iframe', AbstractEscapedView::IFRAME);
		$this->assertArrayHasKey('src', AbstractEscapedView::IFRAME['iframe']);
		$this->assertArrayHasKey('class', AbstractEscapedView::IFRAME['iframe']);
	}

	/**
	 * Test that HEAD constant exists and contains expected tags
	 *
	 * @return void
	 */
	public function testHeadConstantContainsExpectedTags(): void
	{
		$this->assertIsArray(AbstractEscapedView::HEAD);
		$this->assertArrayHasKey('meta', AbstractEscapedView::HEAD);
		$this->assertArrayHasKey('link', AbstractEscapedView::HEAD);
		$this->assertArrayHasKey('content', AbstractEscapedView::HEAD['meta']);
		$this->assertArrayHasKey('name', AbstractEscapedView::HEAD['meta']);
		$this->assertArrayHasKey('rel', AbstractEscapedView::HEAD['link']);
		$this->assertArrayHasKey('href', AbstractEscapedView::HEAD['link']);
	}


}

/**
 * Concrete implementation of AbstractEscapedView for testing
 */
class ConcreteEscapedView extends AbstractEscapedView
{
	/**
	 * Register the service
	 *
	 * @return void
	 */
	public function register(): void
	{
		// No registration needed for testing
	}


}
