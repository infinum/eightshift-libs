<?php

/**
 * Tests for AbstractAssets class
 *
 * @package EightshiftLibs\Tests\Unit\Enqueue
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Enqueue;

use Brain\Monkey;
use EightshiftLibs\Enqueue\AbstractAssets;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * AbstractAssetsTest class
 */
class AbstractAssetsTest extends BaseTestCase
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
	 * Test that AbstractAssets implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$assets = new ConcreteAssets();

		$this->assertInstanceOf(ServiceInterface::class, $assets);
	}

	/**
	 * Test media constants
	 *
	 * @return void
	 */
	public function testMediaConstants(): void
	{
		$this->assertEquals('all', AbstractAssets::MEDIA_ALL);
		$this->assertEquals('print', AbstractAssets::MEDIA_PRINT);
		$this->assertEquals('screen', AbstractAssets::MEDIA_SCREEN);
	}

	/**
	 * Test IN_FOOTER constant
	 *
	 * @return void
	 */
	public function testInFooterConstant(): void
	{
		$this->assertTrue(AbstractAssets::IN_FOOTER);
	}

	/**
	 * Test that getLocalizations returns empty array by default
	 *
	 * @return void
	 */
	public function testGetLocalizationsReturnsEmptyArray(): void
	{
		$assets = new ConcreteAssets();

		$reflection = new \ReflectionMethod($assets, 'getLocalizations');

		$this->assertEquals([], $reflection->invoke($assets));
	}

	/**
	 * Test that getMedia returns 'all' by default
	 *
	 * @return void
	 */
	public function testGetMediaReturnsAll(): void
	{
		$assets = new ConcreteAssets();

		$reflection = new \ReflectionMethod($assets, 'getMedia');

		$this->assertEquals('all', $reflection->invoke($assets));
	}

	/**
	 * Test that scriptInFooter returns true by default
	 *
	 * @return void
	 */
	public function testScriptInFooterReturnsTrue(): void
	{
		$assets = new ConcreteAssets();

		$reflection = new \ReflectionMethod($assets, 'scriptInFooter');

		$this->assertTrue($reflection->invoke($assets));
	}

	/**
	 * Test that scriptStrategy returns empty string by default
	 *
	 * @return void
	 */
	public function testScriptStrategyReturnsEmptyString(): void
	{
		$assets = new ConcreteAssets();

		$reflection = new \ReflectionMethod($assets, 'scriptStrategy');

		$this->assertEquals('', $reflection->invoke($assets));
	}

	/**
	 * Test that scriptArgs returns expected structure
	 *
	 * @return void
	 */
	public function testScriptArgsReturnsExpectedStructure(): void
	{
		$assets = new ConcreteAssets();

		$reflection = new \ReflectionMethod($assets, 'scriptArgs');
		$result = $reflection->invoke($assets);

		$this->assertArrayHasKey('strategy', $result);
		$this->assertArrayHasKey('in_footer', $result);
		$this->assertEquals('', $result['strategy']);
		$this->assertTrue($result['in_footer']);
	}

	/**
	 * Test that getAssetsPrefix returns expected value
	 *
	 * @return void
	 */
	public function testGetAssetsPrefixReturnsExpectedValue(): void
	{
		$assets = new ConcreteAssets();

		$this->assertEquals('test', $assets->getAssetsPrefix());
	}

	/**
	 * Test that getAssetsVersion returns expected value
	 *
	 * @return void
	 */
	public function testGetAssetsVersionReturnsExpectedValue(): void
	{
		$assets = new ConcreteAssets();

		$this->assertEquals('1.0.0', $assets->getAssetsVersion());
	}
}

/**
 * Concrete implementation of AbstractAssets for testing
 */
class ConcreteAssets extends AbstractAssets
{
	/**
	 * Register the service
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
	}

	/**
	 * Enqueue assets callback
	 *
	 * @return void
	 */
	public function enqueueAssets(): void
	{
		// Enqueue assets logic
	}

	/**
	 * Get assets prefix
	 *
	 * @return string
	 */
	public function getAssetsPrefix(): string
	{
		return 'test';
	}

	/**
	 * Get assets version
	 *
	 * @return string
	 */
	public function getAssetsVersion(): string
	{
		return '1.0.0';
	}
}
