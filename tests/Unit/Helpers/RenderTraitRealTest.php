<?php

/**
 * Pure-unit tests that exercise the real RenderTrait code via the Helpers class.
 *
 * These tests complement RenderTraitTest.php, which uses a wrapper class that
 * overrides render(). This file reflects into Helpers (which composes
 * RenderTrait) so the actual trait methods execute and contribute to coverage.
 *
 * render() itself is intentionally not covered here — its behavior is `include $file`
 * plus output buffering, which is inherently sociable and belongs in integration tests.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Tests\BaseTestCase;
use ReflectionClass;
use stdClass;

/**
 * @coversDefaultClass \EightshiftLibs\Helpers\RenderTrait
 */
class RenderTraitRealTest extends BaseTestCase
{
	/**
	 * Private static properties on Helpers that must be reset between tests
	 * so priming in one test does not leak into the next.
	 *
	 * @var array<int, string>
	 */
	private const RESETTABLE_PROPERTIES = [
		'cache',
		'allowedNamesFlipped',
		'renderHandlers',
		'basePaths',
		'pathConfigs',
	];

	protected function setUp(): void
	{
		parent::setUp();
		$this->resetHelpersState();
	}

	protected function tearDown(): void
	{
		$this->resetHelpersState();
		parent::tearDown();
	}

	/**
	 * @covers ::cleanInnerBlocks
	 */
	public function testCleanInnerBlocksReturnsEmptyArrayForEmptyInput(): void
	{
		$result = $this->invokePrivate('cleanInnerBlocks', [[]]);

		$this->assertSame([], $result);
	}

	/**
	 * @covers ::cleanInnerBlocks
	 */
	public function testCleanInnerBlocksExtractsNameAttributesAndInnerBlocks(): void
	{
		$block = $this->makeBlock('core/paragraph', ['content' => 'Hello'], []);

		$result = $this->invokePrivate('cleanInnerBlocks', [[$block]]);

		$this->assertCount(1, $result);
		$this->assertSame('core/paragraph', $result[0]['name']);
		$this->assertSame(['content' => 'Hello'], $result[0]['attributes']);
		$this->assertSame([], $result[0]['innerBlocks']);
	}

	/**
	 * @covers ::cleanInnerBlocks
	 */
	public function testCleanInnerBlocksRecursesIntoNestedBlocks(): void
	{
		$inner = $this->makeBlock('core/text', ['text' => 'leaf'], []);
		$outer = $this->makeBlock('core/group', ['layout' => 'flow'], [$inner]);

		$result = $this->invokePrivate('cleanInnerBlocks', [[$outer]]);

		$this->assertCount(1, $result);
		$this->assertSame('core/group', $result[0]['name']);
		$this->assertCount(1, $result[0]['innerBlocks']);
		$this->assertSame('core/text', $result[0]['innerBlocks'][0]['name']);
		$this->assertSame(['text' => 'leaf'], $result[0]['innerBlocks'][0]['attributes']);
		$this->assertSame([], $result[0]['innerBlocks'][0]['innerBlocks']);
	}

	/**
	 * @covers ::cleanInnerBlocks
	 */
	public function testCleanInnerBlocksHandlesMultipleSiblingBlocks(): void
	{
		$a = $this->makeBlock('core/heading', ['level' => 1], []);
		$b = $this->makeBlock('core/paragraph', ['content' => 'p'], []);

		$result = $this->invokePrivate('cleanInnerBlocks', [[$a, $b]]);

		$this->assertCount(2, $result);
		$this->assertSame('core/heading', $result[0]['name']);
		$this->assertSame('core/paragraph', $result[1]['name']);
	}

	/**
	 * @covers ::cleanInnerBlocks
	 */
	public function testCleanInnerBlocksRecursesMultipleLevelsDeep(): void
	{
		$leaf = $this->makeBlock('core/text', [], []);
		$middle = $this->makeBlock('core/column', [], [$leaf]);
		$root = $this->makeBlock('core/columns', [], [$middle]);

		$result = $this->invokePrivate('cleanInnerBlocks', [[$root]]);

		$this->assertSame('core/text', $result[0]['innerBlocks'][0]['innerBlocks'][0]['name']);
	}

	/**
	 * @covers ::initializeRenderCaches
	 */
	public function testInitializeRenderCachesPopulatesBothCaches(): void
	{
		$this->assertNull($this->getPrivateStaticValue('allowedNamesFlipped'));
		$this->assertNull($this->getPrivateStaticValue('renderHandlers'));

		$this->invokePrivate('initializeRenderCaches', []);

		$allowedNames = $this->getPrivateStaticValue('allowedNamesFlipped');
		$renderHandlers = $this->getPrivateStaticValue('renderHandlers');

		$this->assertIsArray($allowedNames);
		$this->assertSame(0, $allowedNames['src']);
		$this->assertArrayHasKey('blocks', $allowedNames);
		$this->assertArrayHasKey('components', $allowedNames);
		$this->assertArrayHasKey('wrapper', $allowedNames);

		$this->assertIsArray($renderHandlers);
		$this->assertArrayHasKey('components', $renderHandlers);
		$this->assertArrayHasKey('wrapper', $renderHandlers);
		$this->assertArrayHasKey('blocks', $renderHandlers);

		// Handler is a private static method: [Helpers::class, 'handle...Render'].
		// is_callable() returns false from outside, so check structure + method existence.
		foreach (['components', 'wrapper', 'blocks'] as $key) {
			$this->assertIsArray($renderHandlers[$key]);
			$this->assertSame(Helpers::class, $renderHandlers[$key][0]);
			$this->assertTrue(\method_exists($renderHandlers[$key][0], $renderHandlers[$key][1]));
		}
	}

	/**
	 * @covers ::initializeRenderCaches
	 */
	public function testInitializeRenderCachesIsIdempotent(): void
	{
		$this->invokePrivate('initializeRenderCaches', []);
		$firstAllowed = $this->getPrivateStaticValue('allowedNamesFlipped');
		$firstHandlers = $this->getPrivateStaticValue('renderHandlers');

		$this->invokePrivate('initializeRenderCaches', []);

		// Same array references — the guard branches skipped re-initialization.
		$this->assertSame($firstAllowed, $this->getPrivateStaticValue('allowedNamesFlipped'));
		$this->assertSame($firstHandlers, $this->getPrivateStaticValue('renderHandlers'));
	}

	/**
	 * @covers ::handleComponentsRender
	 */
	public function testHandleComponentsRenderWithComponentNameUsesPrefixPath(): void
	{
		$this->primeComponent('forms', ['componentName' => 'forms', 'manifest' => 'c']);

		$result = $this->invokePrivate(
			'handleComponentsRender',
			['button', 'forms/button', 'forms'],
		);

		$this->assertArrayHasKey('path', $result);
		$this->assertArrayHasKey('manifest', $result);
		$this->assertStringContainsString('components', $result['path']);
		$this->assertStringContainsString('forms/button', $result['path']);
		$this->assertStringEndsWith('button.php', $result['path']);
		$this->assertSame(['componentName' => 'forms', 'manifest' => 'c'], $result['manifest']);
	}

	/**
	 * @covers ::handleComponentsRender
	 */
	public function testHandleComponentsRenderWithoutComponentNameNestsRenderName(): void
	{
		$this->primeComponent('button', ['componentName' => 'button']);

		$result = $this->invokePrivate(
			'handleComponentsRender',
			['button', '', ''],
		);

		// Without componentName: path is components/<renderName>/<renderName>.php
		$this->assertStringEndsWith('button' . DIRECTORY_SEPARATOR . 'button.php', $result['path']);
		$this->assertSame(['componentName' => 'button'], $result['manifest']);
	}

	/**
	 * @covers ::handleBlocksRender
	 */
	public function testHandleBlocksRenderWithComponentNameUsesPrefixPath(): void
	{
		$this->primeBlock('sections', ['blockName' => 'sections']);

		$result = $this->invokePrivate(
			'handleBlocksRender',
			['hero', 'sections/hero', 'sections'],
		);

		$this->assertStringContainsString('Blocks' . DIRECTORY_SEPARATOR . 'custom', $result['path']);
		$this->assertStringContainsString('sections/hero', $result['path']);
		$this->assertStringEndsWith('hero.php', $result['path']);
		$this->assertSame(['blockName' => 'sections'], $result['manifest']);
	}

	/**
	 * @covers ::handleBlocksRender
	 */
	public function testHandleBlocksRenderWithoutComponentNameNestsRenderName(): void
	{
		$this->primeBlock('hero', ['blockName' => 'hero']);

		$result = $this->invokePrivate(
			'handleBlocksRender',
			['hero', '', ''],
		);

		$this->assertStringEndsWith('hero' . DIRECTORY_SEPARATOR . 'hero.php', $result['path']);
		$this->assertSame(['blockName' => 'hero'], $result['manifest']);
	}

	/**
	 * @covers ::handleWrapperRender
	 */
	public function testHandleWrapperRenderReturnsWrapperPathAndManifest(): void
	{
		$this->primeWrapper(['wrapperName' => 'default']);

		$result = $this->invokePrivate('handleWrapperRender', ['wrapper']);

		$this->assertStringContainsString('Blocks' . DIRECTORY_SEPARATOR . 'wrapper', $result['path']);
		$this->assertStringEndsWith('wrapper.php', $result['path']);
		$this->assertSame(['wrapperName' => 'default'], $result['manifest']);
	}

	// ---------------------------------------------------------------------
	// Helpers
	// ---------------------------------------------------------------------

	/**
	 * Invoke a private static method on Helpers via reflection.
	 *
	 * @param string $method Method name defined on RenderTrait.
	 * @param array<int, mixed> $args Positional arguments.
	 *
	 * @return mixed
	 */
	private function invokePrivate(string $method, array $args)
	{
		$refl = new ReflectionClass(Helpers::class);
		$m = $refl->getMethod($method);
		$m->setAccessible(true);

		return $m->invokeArgs(null, $args);
	}

	/**
	 * Read a private static property on Helpers.
	 *
	 * @param string $name Property name defined on RenderTrait.
	 */
	private function getPrivateStaticValue(string $name): mixed
	{
		$refl = new ReflectionClass(Helpers::class);
		$prop = $refl->getProperty($name);
		$prop->setAccessible(true);

		return $prop->getValue();
	}

	/**
	 * Set a private static property on Helpers.
	 */
	private function setPrivateStaticValue(string $name, mixed $value): void
	{
		$refl = new ReflectionClass(Helpers::class);
		$prop = $refl->getProperty($name);
		$prop->setAccessible(true);
		$prop->setValue(null, $value);
	}

	/**
	 * Reset Helpers' mutable static state so tests do not leak into each other
	 * (or into other suites that touch Helpers after this one).
	 */
	private function resetHelpersState(): void
	{
		foreach (self::RESETTABLE_PROPERTIES as $name) {
			// cache starts as [] (not null); everything else starts as null.
			$this->setPrivateStaticValue($name, $name === 'cache' ? [] : null);
		}
	}

	/**
	 * Prime the Helpers cache with a single component entry.
	 *
	 * @param array<string, mixed> $data
	 */
	private function primeComponent(string $name, array $data): void
	{
		$cache = $this->getPrivateStaticValue('cache');
		$cache[AbstractManifestCache::TYPE_BLOCKS][AbstractManifestCache::COMPONENTS_KEY][$name] = $data;
		$this->setPrivateStaticValue('cache', $cache);
	}

	/**
	 * Prime the Helpers cache with a single block entry.
	 *
	 * @param array<string, mixed> $data
	 */
	private function primeBlock(string $name, array $data): void
	{
		$cache = $this->getPrivateStaticValue('cache');
		$cache[AbstractManifestCache::TYPE_BLOCKS][AbstractManifestCache::BLOCKS_KEY][$name] = $data;
		$this->setPrivateStaticValue('cache', $cache);
	}

	/**
	 * Prime the Helpers cache with wrapper data.
	 *
	 * @param array<string, mixed> $data
	 */
	private function primeWrapper(array $data): void
	{
		$cache = $this->getPrivateStaticValue('cache');
		$cache[AbstractManifestCache::TYPE_BLOCKS][AbstractManifestCache::WRAPPER_KEY] = $data;
		$this->setPrivateStaticValue('cache', $cache);
	}

	/**
	 * Build an object with the shape cleanInnerBlocks expects (stands in for WP_Block).
	 *
	 * @param array<string, mixed> $attributes
	 * @param array<int, object> $innerBlocks
	 */
	private function makeBlock(string $name, array $attributes, array $innerBlocks): stdClass
	{
		$block = new stdClass();
		$block->name = $name;
		$block->attributes = $attributes;
		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		$block->inner_blocks = $innerBlocks;

		return $block;
	}
}
