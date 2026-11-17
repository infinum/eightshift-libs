<?php

/**
 * Regression tests for editor module script registrations.
 *
 * @package EightshiftLibs\Tests\Unit\Enqueue\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Enqueue\Blocks;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use DOMDocument;
use DOMElement;
use EightshiftLibs\Enqueue\Blocks\AbstractEnqueueBlocks;
use EightshiftLibs\Enqueue\Blocks\EditorModule;
use EightshiftLibs\Tests\BaseTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;
use RuntimeException;
use WP_HTML_Tag_Processor;

/**
 * Minimal script registry with WordPress's shallow clone behavior.
 */
#[\AllowDynamicProperties]
final class EditorModuleTestScripts
{
	/** @var array<string, object> */
	public array $registered = [];

	/** @var string[] */
	public array $done = [];

	/** @param string[] $deps */
	public function register(string $handle, array $deps = []): void
	{
		$this->registered[$handle] = (object) [
			'deps' => $deps,
			'extra' => [],
		];
	}

	public function get_data(string $handle, string $key): mixed
	{
		return $this->registered[$handle]->extra[$key] ?? false;
	}
}

/**
 * HTML processor substitute for the script tags used by these tests.
 */
final class EditorModuleTestTagProcessor
{
	private DOMDocument $document;

	/** @var DOMElement[] */
	private array $scripts = [];

	private int $position = -1;

	public function __construct(string $html)
	{
		$this->document = new DOMDocument();
		$this->document->loadHTML($html, \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD);
		foreach ($this->document->getElementsByTagName('script') as $script) {
			$this->scripts[] = $script;
		}
	}

	public function next_tag(array $query): bool
	{
		return $query['tag_name'] === 'SCRIPT' && isset($this->scripts[++$this->position]);
	}

	public function get_attribute(string $name): string|bool|null
	{
		$script = $this->scripts[$this->position];
		if (!$script->hasAttribute($name)) {
			return null;
		}

		if ($name === 'async' || $name === 'defer') {
			return true;
		}

		return $script->getAttribute($name);
	}

	public function set_attribute(string $name, string $value): void
	{
		$this->scripts[$this->position]->setAttribute($name, $value);
	}

	public function get_updated_html(): string
	{
		return $this->document->saveHTML();
	}
}

class EditorModuleEnqueueProbe extends AbstractEnqueueBlocks
{
	public function register(): void {}

	public function getAssetsPrefix(): string
	{
		return 'owned';
	}

	public function getAssetsVersion(): string
	{
		return '1';
	}

	public function setAssetsItem(string $key): string
	{
		return 'https://example.test/editor.js?ver=1';
	}

	protected function readAssetMetadata(string $scriptKey): ?array
	{
		return [
			'format' => 'module',
			'readiness' => 'eightshift-editor-v1',
			'dependencies' => [],
		];
	}

	public function addEditorBodyDebugClass(): void {}
}

final class StrictEditorMetadataProbe extends EditorModuleEnqueueProbe
{
	public ?array $metadata = null;

	protected function readAssetMetadata(string $scriptKey): ?array
	{
		return $this->metadata;
	}

}

final class EditorModuleTest extends BaseTestCase
{
	private const HANDLE = 'editor-module';

	private const SRC = 'https://example.test/editor.js?ver=1';

	private const PREFIXED_CLASS = 'VendorB\\EightshiftLibs\\Enqueue\\Blocks\\EditorModule';

	private EditorModuleTestScripts $scripts;

	private bool $inlineScriptFails = false;

	/** @var callable[] */
	private array $scriptFilters = [];

	/** @var callable[] */
	private array $footerFilters = [];

	/** @var array<string, mixed> */
	private array $moduleState = [];

	protected function setUp(): void
	{
		parent::setUp();

		if (!\class_exists(WP_HTML_Tag_Processor::class)) {
			\class_alias(EditorModuleTestTagProcessor::class, WP_HTML_Tag_Processor::class);
		}

		foreach (['registrations', 'footerPrinted'] as $name) {
			$property = new ReflectionProperty(EditorModule::class, $name);
			$this->moduleState[$name] = $property->getValue();
			$property->setValue(null, null);
		}

		$this->scripts = new EditorModuleTestScripts();
		$this->scripts->register(self::HANDLE);

		Functions\when('wp_scripts')->alias(fn (): EditorModuleTestScripts => $this->scripts);
		Functions\when('wp_script_is')->alias(fn (string $handle): bool => \in_array($handle, $this->scripts->done, true));
		Functions\when('wp_json_encode')->alias('json_encode');
		Functions\when('wp_add_inline_script')->alias(function (string $handle, string $data, string $position): bool {
			if ($this->inlineScriptFails || !isset($this->scripts->registered[$handle])) {
				return false;
			}

			$this->scripts->registered[$handle]->extra[$position][] = $data;
			return true;
		});

		Filters\expectAdded('script_loader_tag')->zeroOrMoreTimes()->with(\Mockery::on(function (callable $callback): bool {
			$this->scriptFilters[] = $callback;
			return true;
		}), \PHP_INT_MAX, 3);
		Filters\expectAdded('print_footer_scripts')->zeroOrMoreTimes()->with(\Mockery::on(function (callable $callback): bool {
			$this->footerFilters[] = $callback;
			return true;
		}), \PHP_INT_MIN, 1);
	}

	protected function tearDown(): void
	{
		foreach ($this->moduleState as $name => $value) {
			(new ReflectionProperty(EditorModule::class, $name))->setValue(null, $value);
			if (\class_exists(self::PREFIXED_CLASS, false)) {
				(new ReflectionProperty(self::PREFIXED_CLASS, $name))->setValue(null, null);
			}
		}

		parent::tearDown();
	}

	public function testIndependentPrefixedCopiesPreserveBothModules(): void
	{
		$prefixedClass = $this->loadPrefixedEditorModule();
		$this->scripts->register('other-module');
		$legacyToken = new \stdClass();
		$this->scripts->eightshiftEditorModuleRegistryToken = $legacyToken;

		EditorModule::configure(self::HANDLE);
		$original = $this->scripts;
		$cloneBeforeOther = clone $original;
		$prefixedClass::configure('other-module');

		$this->assertModuleTag($this->filterTag());
		$this->assertModuleTag($this->filterTag('other-module'), 'other-module');
		self::assertSame($legacyToken, $this->scripts->eightshiftEditorModuleRegistryToken);

		$this->scripts = $cloneBeforeOther;
		$this->assertModuleTag($this->filterTag());
		self::assertSame($this->tag('other-module'), $this->filterTag('other-module'));
		$prefixedClass::configure('other-module');
		$this->assertModuleTag($this->filterTag('other-module'), 'other-module');

		$this->scripts = clone $original;
		EditorModule::configure(self::HANDLE);
		$prefixedClass::configure('other-module');
		$this->assertModuleTag($this->filterTag());
		$this->assertModuleTag($this->filterTag('other-module'), 'other-module');
		self::assertCount(1, $this->scripts->registered[self::HANDLE]->extra['before']);
		self::assertCount(1, $this->scripts->registered['other-module']->extra['before']);
	}

	public function testOlderSnapshotDoesNotInheritLaterModuleConfiguration(): void
	{
		$this->scripts->register('later-module');
		$original = $this->scripts;
		EditorModule::configure(self::HANDLE);
		$snapshot = clone $original;

		EditorModule::configure('later-module');
		$this->assertModuleTag($this->filterTag('later-module'), 'later-module');
		$filterCount = \count($this->scriptFilters);

		$this->scripts = $snapshot;
		$this->assertModuleTag($this->filterTag());
		self::assertSame($this->tag('later-module'), $this->filterTag('later-module'));
		EditorModule::configure('later-module');
		EditorModule::configure('later-module');
		$this->assertModuleTag($this->filterTag('later-module'), 'later-module');
		self::assertCount($filterCount, $this->scriptFilters);
		self::assertCount(1, $original->registered['later-module']->extra['before']);

		$this->scripts = $original;
		$this->assertModuleTag($this->filterTag());
		$this->assertModuleTag($this->filterTag('later-module'), 'later-module');
	}

	public function testConfigurationInSnapshotDoesNotLeakToOriginal(): void
	{
		$this->scripts->register('later-module');
		$original = $this->scripts;
		EditorModule::configure(self::HANDLE);
		$this->scripts = clone $original;

		EditorModule::configure('later-module');
		$this->assertModuleTag($this->filterTag('later-module'), 'later-module');
		$filterCount = \count($this->scriptFilters);

		$this->scripts = $original;
		self::assertSame($this->tag('later-module'), $this->filterTag('later-module'));
		EditorModule::configure('later-module');
		$this->assertModuleTag($this->filterTag('later-module'), 'later-module');
		self::assertCount($filterCount, $this->scriptFilters);
		self::assertCount(1, $original->registered['later-module']->extra['before']);
	}

	public function testSnapshotConfigurationIsIsolatedAcrossPrefixedClasses(): void
	{
		$prefixedClass = $this->loadPrefixedEditorModule();
		$this->scripts->register('prefix-module');
		$this->scripts->register('later-module');
		EditorModule::configure(self::HANDLE);
		$prefixedClass::configure('prefix-module');
		$original = $this->scripts;
		$snapshot = clone $original;

		$prefixedClass::configure('later-module');
		$this->assertModuleTag($this->filterTag('later-module'), 'later-module');
		$filterCount = \count($this->scriptFilters);

		$this->scripts = $snapshot;
		$this->assertModuleTag($this->filterTag());
		$this->assertModuleTag($this->filterTag('prefix-module'), 'prefix-module');
		self::assertFalse($snapshot->get_data('later-module', 'before'));
		self::assertSame($this->tag('later-module'), $this->filterTag('later-module'));
		$prefixedClass::configure('later-module');
		$this->assertModuleTag($this->filterTag('later-module'), 'later-module');
		self::assertCount($filterCount, $this->scriptFilters);
		self::assertCount(1, $original->registered['later-module']->extra['before']);
		self::assertCount(1, $snapshot->registered['later-module']->extra['before']);
	}

	public function testInlineBootstrapDoesNotLeakIntoEarlierSnapshot(): void
	{
		$this->scripts->register('later-module', ['dependency']);
		$this->scripts->registered['later-module']->extra = [
			'before' => ['classicBefore();'],
			'after' => ['classicAfter();'],
			'data' => 'var existing = true;',
		];
		EditorModule::configure(self::HANDLE);
		$original = $this->scripts;
		$originalRegistration = $original->registered['later-module'];
		$snapshot = clone $original;

		EditorModule::configure('later-module');
		self::assertSame(['classicBefore();'], $snapshot->get_data('later-module', 'before'));
		self::assertSame($originalRegistration, $snapshot->registered['later-module']);
		self::assertNotSame($originalRegistration, $original->registered['later-module']);
		self::assertCount(2, $original->get_data('later-module', 'before'));
		self::assertSame('classicBefore();', $original->get_data('later-module', 'before')[0]);
		self::assertStringContainsString('globalThis.eightshiftEditorModules.register("later-module");', $original->get_data('later-module', 'before')[1]);
		self::assertSame(['classicAfter();'], $original->get_data('later-module', 'after'));
		self::assertSame('var existing = true;', $original->get_data('later-module', 'data'));
		self::assertSame(['dependency'], $original->registered['later-module']->deps);

		$this->scripts = $snapshot;
		self::assertSame($this->tag('later-module'), $this->filterTag('later-module'));
		EditorModule::configure('later-module');
		EditorModule::configure('later-module');
		self::assertNotSame($originalRegistration, $snapshot->registered['later-module']);
		self::assertNotSame($original->registered['later-module'], $snapshot->registered['later-module']);
		self::assertCount(2, $snapshot->get_data('later-module', 'before'));
		self::assertSame($original->get_data('later-module', 'before'), $snapshot->get_data('later-module', 'before'));
		$this->assertModuleTag($this->filterTag('later-module'), 'later-module');
	}

	public function testInlineBootstrapIsIsolatedAcrossMultipleCopiedRegistries(): void
	{
		$source = $this->scripts;
		$first = new EditorModuleTestScripts();
		$second = new EditorModuleTestScripts();
		$first->registered = $source->registered;
		$second->registered = $source->registered;

		$this->scripts = $first;
		EditorModule::configure(self::HANDLE);
		self::assertFalse($source->get_data(self::HANDLE, 'before'));
		self::assertFalse($second->get_data(self::HANDLE, 'before'));
		self::assertCount(1, $first->get_data(self::HANDLE, 'before'));
		$filterCount = \count($this->scriptFilters);

		$this->scripts = $second;
		self::assertSame($this->tag(), $this->filterTag());
		EditorModule::configure(self::HANDLE);
		self::assertFalse($source->get_data(self::HANDLE, 'before'));
		self::assertCount(1, $second->get_data(self::HANDLE, 'before'));
		self::assertNotSame($first->registered[self::HANDLE], $second->registered[self::HANDLE]);
		self::assertCount($filterCount, $this->scriptFilters);
		$this->assertModuleTag($this->filterTag());

		$this->scripts = $first;
		$this->assertModuleTag($this->filterTag());
		$this->scripts = $source;
		self::assertSame($this->tag(), $this->filterTag());
	}

	public function testFailedInlineRegistrationRestoresOriginalAndCanRetry(): void
	{
		$original = $this->scripts->registered[self::HANDLE];
		$snapshot = clone $this->scripts;
		$this->inlineScriptFails = true;

		try {
			EditorModule::configure(self::HANDLE);
			self::fail('Expected inline script registration to fail.');
		} catch (RuntimeException $exception) {
			self::assertStringContainsString('Unable to register Eightshift editor module readiness', $exception->getMessage());
		}

		self::assertSame($original, $this->scripts->registered[self::HANDLE]);
		self::assertSame($original, $snapshot->registered[self::HANDLE]);
		self::assertFalse($this->scripts->get_data(self::HANDLE, 'before'));
		self::assertSame($this->tag(), $this->filterTag());

		$this->inlineScriptFails = false;
		EditorModule::configure(self::HANDLE);
		self::assertNotSame($original, $this->scripts->registered[self::HANDLE]);
		self::assertFalse($snapshot->get_data(self::HANDLE, 'before'));
		self::assertCount(1, $this->scripts->get_data(self::HANDLE, 'before'));
		$this->assertModuleTag($this->filterTag());
	}

	public function testEditorRejectsMissingAndClassicMetadata(): void
	{
		Functions\when('is_admin')->justReturn(true);
		$service = new StrictEditorMetadataProbe();
		foreach ([null, ['dependencies' => [], 'format' => 'classic'], ['dependencies' => [], 'format' => 'module'], ['dependencies' => [], 'format' => 'module', 'readiness' => 'invalid']] as $metadata) {
			$service->metadata = $metadata;
			try {
				$service->enqueueBlockEditorScript();
				self::fail('Expected metadata rejection.');
			} catch (RuntimeException $exception) {
				self::assertStringContainsString('metadata', $exception->getMessage());
			}
		}
	}

	public function testEnqueueServiceOwnsConfiguredAndCopiedRegistrations(): void
	{
		Functions\when('is_admin')->justReturn(true);
		Functions\when('is_wp_version_compatible')->justReturn(true);
		Functions\when('wp_enqueue_script')->justReturn(null);
		Functions\when('wp_register_script')->alias(function (string $handle, string $src, array $deps): bool {
			if (isset($this->scripts->registered[$handle])) {
				return false;
			}

			$this->scripts->register($handle, $deps);
			return true;
		});

		$service = new EditorModuleEnqueueProbe();
		$handle = $service->getBlockEditorScriptsHandle();
		self::assertSame('owned-block-editor-scripts', $handle);
		$service->enqueueBlockEditorScript();
		$original = $this->scripts;
		$registration = $original->registered[$handle];
		$service->enqueueBlockEditorScript();
		self::assertSame($registration, $original->registered[$handle]);
		self::assertCount(1, $original->get_data($handle, 'before'));

		$this->scripts = clone $original;
		$service->enqueueBlockEditorScript();
		self::assertSame($registration, $this->scripts->registered[$handle]);
		$this->assertModuleTag($this->filterTag($handle), $handle);

		$this->scripts = new EditorModuleTestScripts();
		$this->scripts->registered = $original->registered;
		$service->enqueueBlockEditorScript();
		self::assertSame($registration, $this->scripts->registered[$handle]);
		self::assertCount(1, $this->scripts->get_data($handle, 'before'));
		$this->assertModuleTag($this->filterTag($handle), $handle);

		$otherService = new EditorModuleEnqueueProbe();
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('handle is already registered: ' . $handle);
		$otherService->enqueueBlockEditorScript();
	}

	public function testClonedRegistryPreservesConfiguredModule(): void
	{
		$original = $this->scripts;
		EditorModule::configure(self::HANDLE);

		$this->scripts = clone $original;
		self::assertNotSame($original, $this->scripts);
		self::assertSame($original->registered[self::HANDLE], $this->scripts->registered[self::HANDLE]);
		$this->assertModuleTag($this->filterTag());
	}

	public function testUnrelatedAndReplacedRegistrationsAreNotConverted(): void
	{
		$original = $this->scripts;
		EditorModule::configure(self::HANDLE);

		$this->scripts = new EditorModuleTestScripts();
		$this->scripts->registered[self::HANDLE] = clone $original->registered[self::HANDLE];
		self::assertSame($this->tag(), $this->filterTag());
		self::assertSame($this->tag('other'), $this->filterTag('other'));

		EditorModule::configure(self::HANDLE);
		$this->assertModuleTag($this->filterTag());
		$this->scripts = $original;
		$this->assertModuleTag($this->filterTag());

		$original->register(self::HANDLE);
		self::assertSame($this->tag(), $this->filterTag());
	}

	public function testRepeatedConfigurationAndCopiedRegistrationAreProcessedOnce(): void
	{
		$original = $this->scripts;
		EditorModule::configure(self::HANDLE);
		EditorModule::configure(self::HANDLE);

		$this->scripts = new EditorModuleTestScripts();
		$this->scripts->registered = $original->registered;
		self::assertSame($this->tag(), $this->filterTag());
		EditorModule::configure(self::HANDLE);
		EditorModule::configure(self::HANDLE);

		self::assertCount(1, $original->registered[self::HANDLE]->extra['before']);
		$this->assertModuleTag($this->filterTag());
		$this->scripts = $original;
		$this->assertModuleTag($this->filterTag());

		$this->scripts = clone $original;
		EditorModule::configure(self::HANDLE);
		$this->assertModuleTag($this->filterTag());
	}

	public function testCloneWithChangedRegistrationDoesNotDuplicateOriginalFilter(): void
	{
		$original = $this->scripts;
		EditorModule::configure(self::HANDLE);

		$this->scripts = clone $original;
		$this->scripts->register(self::HANDLE);
		EditorModule::configure(self::HANDLE);
		$clone = $this->scripts;

		$this->scripts = $original;
		EditorModule::configure(self::HANDLE);
		$this->assertModuleTag($this->filterTag());
		$this->scripts = $clone;
		$this->assertModuleTag($this->filterTag());
	}

	public function testClonedRegistryStillRejectsNonblockingDependenciesAtPrintTime(): void
	{
		$this->scripts->register(self::HANDLE, ['dependency']);
		$this->scripts->register('dependency');
		EditorModule::configure(self::HANDLE);

		$this->scripts = clone $this->scripts;
		$this->scripts->registered['dependency']->extra['strategy'] = 'defer';

		self::assertSame('', $this->filterTag());
	}

	#[DataProvider('invalidModuleTags')]
	public function testInvalidModuleTagsAreStillRejected(string $tag, string $message): void
	{
		EditorModule::configure(self::HANDLE);
		$this->scripts = clone $this->scripts;

		self::assertSame('', $this->filterTag(self::HANDLE, $tag));
	}

	/** @return array<string, array{string, string}> */
	public static function invalidModuleTags(): array
	{
		return [
			'changed source' => ['<script id="editor-module-js" src="https://example.test/other.js"></script>', 'source was changed'],
			'async attribute' => ['<script id="editor-module-js" src="https://example.test/editor.js?ver=1" async></script>', 'cannot have async or defer attributes'],
			'defer attribute' => ['<script id="editor-module-js" src="https://example.test/editor.js?ver=1" defer></script>', 'cannot have async or defer attributes'],
			'missing tag' => ['<script id="other-js" src="https://example.test/editor.js?ver=1"></script>', 'Expected exactly one'],
		];
	}

	public function testCloningAfterFooterPrintingPreservesTheDeadline(): void
	{
		EditorModule::validateRegistration(self::HANDLE, []);
		foreach ($this->footerFilters as $filter) {
			$filter(true);
		}

		$this->scripts = clone $this->scripts;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('must be registered before scripts are printed');
		EditorModule::configure(self::HANDLE);
	}

	public function testCloneCreatedBeforeFooterPrintingRemainsOpen(): void
	{
		EditorModule::configure(self::HANDLE);
		$clone = clone $this->scripts;
		foreach ($this->footerFilters as $filter) {
			$filter(true);
		}

		$this->scripts = $clone;
		EditorModule::configure(self::HANDLE);
		$this->assertModuleTag($this->filterTag());
	}

	public function testAnotherPrefixObservesAlreadyPrintedFooter(): void
	{
		$prefixedClass = $this->loadPrefixedEditorModule();
		EditorModule::validateRegistration(self::HANDLE, []);
		foreach ($this->footerFilters as $filter) {
			$filter(true);
		}

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('must be registered before scripts are printed');
		$prefixedClass::configure(self::HANDLE);
	}

	public function testFooterStateRemainsSpecificToEachRegistry(): void
	{
		$original = $this->scripts;
		EditorModule::configure(self::HANDLE);
		foreach ($this->footerFilters as $filter) {
			$filter(true);
		}

		$this->scripts = new EditorModuleTestScripts();
		$this->scripts->registered = $original->registered;
		EditorModule::configure(self::HANDLE);
		$this->assertModuleTag($this->filterTag());

		$this->scripts = $original;
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('must be registered before scripts are printed');
		EditorModule::configure(self::HANDLE);
	}

	private function tag(string $handle = self::HANDLE): string
	{
		return '<script id="' . $handle . '-js" src="' . self::SRC . '"></script>';
	}

	private function filterTag(string $handle = self::HANDLE, ?string $tag = null): string
	{
		$tag ??= $this->tag($handle);
		foreach ($this->scriptFilters as $filter) {
			$tag = $filter($tag, $handle, self::SRC);
		}

		return $tag;
	}

	private function assertModuleTag(string $tag, string $handle = self::HANDLE): void
	{
		self::assertStringContainsString('type="module"', $tag);
		self::assertStringContainsString('data-eightshift-editor-ready="' . $handle . '"', $tag);
		self::assertStringContainsString('src="' . self::SRC . '#' . $handle . '"', $tag);
	}

	private function loadPrefixedEditorModule(): string
	{
		if (!\class_exists(self::PREFIXED_CLASS, false)) {
			$source = \file_get_contents(__DIR__ . '/../../../../src/Enqueue/Blocks/EditorModule.php');
			self::assertIsString($source);
			$source = \str_replace(
				'namespace EightshiftLibs\\Enqueue\\Blocks;',
				'namespace VendorB\\EightshiftLibs\\Enqueue\\Blocks;',
				$source,
				$count
			);
			self::assertSame(1, $count);
			$copy = __DIR__ . '/.prefixed-editor-module-' . \bin2hex(\random_bytes(16)) . '.php';
			$file = \fopen($copy, 'x');
			if ($file === false) {
				throw new RuntimeException('Unable to create prefixed editor module fixture.');
			}

			try {
				if (\fwrite($file, $source) !== \strlen($source)) {
					throw new RuntimeException('Unable to write prefixed editor module fixture.');
				}
				\fclose($file);
				$file = null;
				require $copy;
			} finally {
				if (\is_resource($file)) {
					\fclose($file);
				}
				\unlink($copy);
			}
		}

		return self::PREFIXED_CLASS;
	}
}
