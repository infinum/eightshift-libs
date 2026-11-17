<?php

/**
 * Parser-inserted editor module integration.
 *
 * @package EightshiftLibs\Enqueue\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Blocks;

use RuntimeException;
use Throwable;
use WeakMap;
use WP_Scripts; // phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse -- Used in WeakMap PHPDoc.
use WP_HTML_Tag_Processor;

/**
 * Keep the classic WordPress dependency registry while explicitly signalling module readiness.
 */
final class EditorModule
{
	/**
	 * Registry state is copied on clone, including configured registrations.
	 */
	private const string REGISTRY_STATE_PROPERTY = 'eightshiftEditorModuleRegistryState';

	/**
	 * Handles with an installed script tag filter.
	 *
	 * @var array<string, bool>|null
	 */
	private static ?array $registrations = null;

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
		if (self::$footerPrinted instanceof WeakMap) {
			return;
		}

		self::$footerPrinted = new WeakMap();
		\add_filter('print_footer_scripts', static function (bool $print): bool {
			$scripts = \wp_scripts();
			self::$footerPrinted[$scripts] = true;
			$state = self::getRegistryState($scripts);
			$state['footerPrinted'] = true;
			$scripts->{self::REGISTRY_STATE_PROPERTY} = $state;
			return $print;
		}, \PHP_INT_MIN);
	}

	/**
	 * Read the state attached to a script registry.
	 *
	 * @param object $scripts WordPress script registry.
	 * @return array<string, mixed> Copy-on-write registry state.
	 */
	private static function getRegistryState(object $scripts): array
	{
		$state = $scripts->{self::REGISTRY_STATE_PROPERTY} ?? [];
		return \is_array($state) ? $state : [];
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
		$scripts = \wp_scripts();
		$state = self::getRegistryState($scripts);
		if (\wp_script_is($handle, 'done') || isset(self::$footerPrinted[$scripts]) || !empty($state['footerPrinted'])) {
			throw new RuntimeException('Eightshift editor modules must be registered before scripts are printed.');
		}
	}

	/**
	 * Configure only the declared editor handle. Inline scripts remain classic scripts.
	 *
	 * @param string $handle Registered script handle.
	 * @throws RuntimeException If readiness setup or script output is invalid.
	 * @throws Throwable If inline registration throws unexpectedly.
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

		self::$registrations ??= [];
		$state = self::getRegistryState($scripts);
		$configured = $state['registrations'][self::class][$handle] ?? [];
		if (\is_array($configured) && \in_array($registration, $configured, true)) {
			return;
		}

		$encodedHandle = \wp_json_encode($handle, \JSON_HEX_TAG | \JSON_HEX_AMP | \JSON_HEX_APOS | \JSON_HEX_QUOT);
		if ($encodedHandle === false) {
			throw new RuntimeException('Unable to encode Eightshift editor module handle: ' . $handle);
		}

		$bootstrap = self::getBootstrap() . "\nglobalThis.eightshiftEditorModules.register({$encodedHandle});";
		// Registrations can be shared by shallow-cloned or copied script registries.
		if (!\in_array($bootstrap, $scripts->get_data($handle, 'before') ?: [], true)) {
			$previousRegistration = $registration;
			$registration = clone $registration;
			$scripts->registered[$handle] = $registration;

			try {
				if (!\wp_add_inline_script($handle, $bootstrap, 'before')) {
					throw new RuntimeException('Unable to register Eightshift editor module readiness: ' . $handle);
				}
			} catch (Throwable $exception) {
				$scripts->registered[$handle] = $previousRegistration;
				throw $exception;
			}
		}

		$state = self::getRegistryState($scripts);
		$registrations = \is_array($state['registrations'] ?? null) ? $state['registrations'] : [];
		$classRegistrations = \is_array($registrations[self::class] ?? null) ? $registrations[self::class] : [];
		$configured = \is_array($classRegistrations[$handle] ?? null) ? $classRegistrations[$handle] : [];
		$configured[] = $registration;
		$classRegistrations[$handle] = $configured;
		$registrations[self::class] = $classRegistrations;
		$state['registrations'] = $registrations;
		$scripts->{self::REGISTRY_STATE_PROPERTY} = $state;

		if (isset(self::$registrations[$handle])) {
			return;
		}

		\add_filter('script_loader_tag', static function (string $tag, string $scriptHandle, string $src) use ($handle): string {
			$scripts = \wp_scripts();
			$state = self::getRegistryState($scripts);
			$registration = $scripts->registered[$handle] ?? null;
			$configured = $state['registrations'][self::class][$handle] ?? [];
			if ($scriptHandle !== $handle || $registration === null || !\is_array($configured) || !\in_array($registration, $configured, true)) {
				return $tag;
			}

			try {
				self::validateDependencyStrategies($handle);
			} catch (RuntimeException $exception) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Report a suppressed unsafe module without interrupting page output.
				\error_log($exception->getMessage());
				return '';
			}

			$processor = new WP_HTML_Tag_Processor($tag);
			$matches = 0;
			while ($processor->next_tag(['tag_name' => 'SCRIPT'])) {
				if ($processor->get_attribute('id') !== $handle . '-js') {
					continue;
				}

				if ($processor->get_attribute('src') !== \html_entity_decode($src, \ENT_QUOTES | \ENT_HTML5, 'UTF-8')) {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Report a suppressed unsafe module without interrupting page output.
					\error_log('Eightshift editor module source was changed: ' . $handle);
					return '';
				}

				if ($processor->get_attribute('async') !== null || $processor->get_attribute('defer') !== null) {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Report a suppressed unsafe module without interrupting page output.
					\error_log('Eightshift editor modules cannot have async or defer attributes: ' . $handle);
					return '';
				}

				// URL fragments survive redirects unless the redirect explicitly replaces them.
				$source = \explode('#', \html_entity_decode($src, \ENT_QUOTES | \ENT_HTML5, 'UTF-8'), 2)[0];
				$processor->set_attribute('src', $source . '#' . \rawurlencode($handle));
				$processor->set_attribute('type', 'module');
				$processor->set_attribute('data-eightshift-editor-ready', $handle);
				$matches++;
			}

			if ($matches !== 1) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Report a suppressed unsafe module without interrupting page output.
				\error_log('Expected exactly one Eightshift editor module script tag: ' . $handle);
				return '';
			}

			return $processor->get_updated_html();
		}, \PHP_INT_MAX, 3);

		self::$registrations[$handle] = true;
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

			if (!isset($scripts->registered[$dependency])) {
				continue;
			}

			foreach ($scripts->registered[$dependency]->deps as $nested) {
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
			const handle = decodeURIComponent(new URL(url).hash.slice(1));
			const scripts = Array.from(document.scripts).filter((script) => script.id === `${handle}-js` && script.getAttribute(attribute) === handle);
			if (scripts.length !== 1) {
				throw new Error('Eightshift editor module completion must identify exactly one script.');
			}
			const script = scripts[0];
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
