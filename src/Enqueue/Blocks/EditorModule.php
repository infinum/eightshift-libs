<?php

/**
 * Opt-in parser-inserted editor module integration.
 *
 * @package EightshiftLibs\Enqueue\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Blocks;

use _WP_Dependency; // phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse -- Used in WeakMap PHPDoc.
use RuntimeException;
use WeakMap;
use WP_Scripts; // phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse -- Used in WeakMap PHPDoc.
use WP_HTML_Tag_Processor;

/**
 * Keep the classic WordPress dependency registry while explicitly signalling module readiness.
 */
final class EditorModule
{
	/**
	 * Registrations configured separately for each WordPress script registry.
	 *
	 * @var WeakMap<WP_Scripts, array<string, _WP_Dependency>>|null
	 */
	private static ?WeakMap $registrations = null;

	/**
	 * Registries whose footer processing has finished since tracking started.
	 *
	 * @var WeakMap<WP_Scripts, bool>|null
	 */
	private static ?WeakMap $footerPrinted = null;

	/**
	 * Observe actual registry processing, not request-global footer action counts.
	 */
	private static function trackFooterPrinting(): void
	{
		if (self::$footerPrinted !== null) {
			return;
		}

		self::$footerPrinted = new WeakMap();
		\add_filter('print_footer_scripts', static function (bool $print): bool {
			self::$footerPrinted[\wp_scripts()] = true;
			return $print;
		}, \PHP_INT_MIN);
	}

	/**
	 * Reject configurations that cannot satisfy the parser-inserted module contract.
	 *
	 * @param string $handle Registered script handle.
	 * @param array<string, string|bool> $args Script registration arguments.
	 * @throws RuntimeException If module registration cannot safely proceed.
	 */
	public static function validateRegistration(string $handle, array $args): void
	{
		if (!\class_exists(WP_HTML_Tag_Processor::class)) {
			throw new RuntimeException('Eightshift editor modules require WP_HTML_Tag_Processor.');
		}

		if (!empty($args['strategy']) || !empty($args['async']) || !empty($args['defer'])) {
			throw new RuntimeException('Eightshift editor modules do not support async or defer script strategies.');
		}

		self::trackFooterPrinting();
		if (\wp_script_is($handle, 'done') || isset(self::$footerPrinted[\wp_scripts()])) {
			throw new RuntimeException('Eightshift editor modules must be registered before scripts are printed.');
		}
	}

	/**
	 * Configure only the declared editor handle. Inline scripts remain classic scripts.
	 *
	 * @param string $handle Registered script handle.
	 * @throws RuntimeException If readiness setup or script output is invalid.
	 */
	public static function configure(string $handle): void
	{
		self::validateRegistration($handle, []);
		self::validateDependencyStrategies($handle);

		$scripts = \wp_scripts();
		$registration = $scripts->registered[$handle] ?? null;
		if ($registration === null) {
			throw new RuntimeException('Eightshift editor module handle is not registered: ' . $handle);
		}

		self::$registrations ??= new WeakMap();
		$registrations = self::$registrations[$scripts] ?? [];
		if (($registrations[$handle] ?? null) === $registration) {
			return;
		}

		$encodedHandle = \wp_json_encode($handle, \JSON_HEX_TAG | \JSON_HEX_AMP | \JSON_HEX_APOS | \JSON_HEX_QUOT);
		if ($encodedHandle === false) {
			throw new RuntimeException('Unable to encode Eightshift editor module handle: ' . $handle);
		}

		$bootstrap = self::getBootstrap() . "\nglobalThis.eightshiftEditorModules.register({$encodedHandle});";
		// Core copies registration objects into its temporary iframe registry.
		if (!\in_array($bootstrap, $scripts->get_data($handle, 'before') ?: [], true) && !\wp_add_inline_script($handle, $bootstrap, 'before')) {
			throw new RuntimeException('Unable to register Eightshift editor module readiness: ' . $handle);
		}

		\add_filter('script_loader_tag', static function (string $tag, string $scriptHandle, string $src) use ($handle, $scripts, $registration): string {
			if ($scriptHandle !== $handle || \wp_scripts() !== $scripts || ($scripts->registered[$handle] ?? null) !== $registration) {
				return $tag;
			}

			self::validateDependencyStrategies($handle);

			$processor = new WP_HTML_Tag_Processor($tag);
			$matches = 0;
			while ($processor->next_tag('SCRIPT')) {
				if ($processor->get_attribute('id') !== $handle . '-js') {
					continue;
				}

				if ($processor->get_attribute('src') !== \html_entity_decode($src, \ENT_QUOTES | \ENT_HTML5, 'UTF-8')) {
					throw new RuntimeException('Eightshift editor module source was changed: ' . $handle);
				}

				if ($processor->get_attribute('async') !== null || $processor->get_attribute('defer') !== null) {
					throw new RuntimeException('Eightshift editor modules cannot have async or defer attributes: ' . $handle);
				}

				$processor->set_attribute('type', 'module');
				$processor->set_attribute('data-eightshift-editor-ready', $handle);
				$matches++;
			}

			if ($matches !== 1) {
				throw new RuntimeException('Expected exactly one Eightshift editor module script tag: ' . $handle);
			}

			return $processor->get_updated_html();
		}, \PHP_INT_MAX, 3);

		$registrations[$handle] = $registration;
		self::$registrations[$scripts] = $registrations;
	}

	/**
	 * Classic global dependencies must execute synchronously before the module.
	 * Recheck at printing because other services may register dependencies later.
	 *
	 * @param string $handle Editor script handle and dependency graph root.
	 * @throws RuntimeException If a dependency has a nonblocking strategy.
	 */
	private static function validateDependencyStrategies(string $handle): void
	{
		$scripts = \wp_scripts();
		$pending = [$handle];
		$visited = [];

		while ($pending !== []) {
			$dependency = \array_pop($pending);
			if (isset($visited[$dependency])) {
				continue;
			}

			$visited[$dependency] = true;
			foreach (['strategy', 'async', 'defer'] as $key) {
				if ($scripts->get_data($dependency, $key)) {
					throw new RuntimeException('Eightshift editor modules require blocking classic dependencies; unsupported strategy on: ' . $dependency);
				}
			}

			foreach ($scripts->registered[$dependency]->deps ?? [] as $nested) {
				$pending[] = $nested;
			}
		}
	}

	/**
	 * A shared API with private per-handle state, installed before the external module.
	 *
	 * @return string Inline classic JavaScript.
	 */
	private static function getBootstrap(): string
	{
		return <<<'JS'
(() => {
	const existing = globalThis.eightshiftEditorModules;
	if (existing !== undefined) {
		if (existing?.version !== 1 || !(existing.ready instanceof Map) || typeof existing.register !== 'function' || typeof existing.complete !== 'function') {
			throw new Error('Incompatible Eightshift editor module readiness API.');
		}
		return;
	}

	const ready = new Map();
	const states = new Map();
	const attribute = 'data-eightshift-editor-ready';
	const fail = (state, message) => {
		if (state.status !== 'pending') {
			return;
		}
		state.status = 'failed';
		state.error = new Error(`Eightshift editor module "${state.handle}": ${message}`);
		state.cleanup();
		state.reject(state.error);
	};

	globalThis.eightshiftEditorModules = Object.freeze({
		version: 1,
		ready,
		register(handle) {
			if (typeof handle !== 'string' || handle.length === 0) {
				throw new Error('Eightshift editor module requires a nonempty handle.');
			}
			if (states.has(handle)) {
				return;
			}
			const state = { handle, status: 'pending', cleanup() {} };
			const promise = new Promise((resolve, reject) => {
				state.resolve = resolve;
				state.reject = reject;
			});
			states.set(handle, state);
			ready.set(handle, promise);
			promise.catch((error) => console.error(error));

			if (document.readyState !== 'loading') {
				fail(state, 'readiness must be registered while the document is loading.');
				throw state.error;
			}

			const onScriptEvent = (event) => {
				const script = event.target;
				if (script?.tagName !== 'SCRIPT' || (script.id !== `${handle}-js` && script.getAttribute(attribute) !== handle)) {
					return;
				}
				fail(state, event.type === 'error' ? 'module loading or evaluation failed.' : 'module loaded without its completion signal.');
			};
			const onDeadline = () => fail(state, 'module did not complete before DOMContentLoaded.');
			state.cleanup = () => {
				document.removeEventListener('load', onScriptEvent, true);
				document.removeEventListener('error', onScriptEvent, true);
				document.removeEventListener('DOMContentLoaded', onDeadline, true);
			};
			document.addEventListener('load', onScriptEvent, true);
			document.addEventListener('error', onScriptEvent, true);
			document.addEventListener('DOMContentLoaded', onDeadline, true);
		},
		complete(url) {
			const scripts = Array.from(document.scripts).filter((script) => script.src === url && script.hasAttribute(attribute));
			if (scripts.length !== 1) {
				throw new Error('Eightshift editor module completion must identify exactly one script.');
			}
			const script = scripts[0];
			const handle = script.getAttribute(attribute);
			const state = states.get(handle);
			if (!state) {
				throw new Error(`Eightshift editor module "${handle}" was not registered.`);
			}
			if (state.status === 'failed') {
				throw state.error;
			}
			if (state.status === 'ready') {
				return;
			}
			if (script.type !== 'module' || script.async || script.hasAttribute('async') || script.hasAttribute('defer')) {
				fail(state, 'completion requires a parser-inserted module without async/defer.');
				throw state.error;
			}
			if (document.readyState === 'complete') {
				fail(state, 'completion arrived after document loading.');
				throw state.error;
			}
			state.status = 'ready';
			state.cleanup();
			state.resolve();
		},
	});
})();
JS;
	}
}
