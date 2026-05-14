<?php

/**
 * TailwindCSS-related helper functions.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use Exception;
use JsonException;

/**
 * Class TailwindTrait Helper.
 */
trait TailwindTrait
{
	/**
	 * Per-title slug cache for the debug prefix emitted by `tailwindClasses` under `WP_DEBUG`.
	 *
	 * @var array<string, string>
	 */
	private static array $tailwindDebugSlugCache = [];

	/**
	 * Get Tailwind breakpoints.
	 *
	 * @param bool $desktopFirst Whether to use desktop-first breakpoints.
	 *
	 * @return array<string>
	 */
	public static function getTwBreakpoints(bool $desktopFirst = false): array
	{
		static $cache = [];

		$key = $desktopFirst ? 'desktop' : 'mobile';
		if (isset($cache[$key])) {
			return $cache[$key];
		}

		$breakpointData = Helpers::getSettingsGlobalVariablesBreakpoints();

		$breakpointNames = \array_keys($breakpointData);

		\usort($breakpointNames, fn($a, $b) => $breakpointData[$a] - $breakpointData[$b]);

		if ($desktopFirst) {
			$cache[$key] = \array_map(fn($breakpoint) => "max-{$breakpoint}", $breakpointNames);
			return $cache[$key];
		}

		$cache[$key] = $breakpointNames;
		return $cache[$key];
	}

	/**
	 * Gets Tailwind classes for the provided part.
	 *
	 * The part needs to be defined within the manifest, in the `tailwind` object.
	 *
	 * @param string $part Part name.
	 * @param array<string, mixed> $manifest Component/block manifest data.
	 * @param array<string> ...$custom Additional custom classes.
	 *
	 * @deprecated 9.2.0 Use `tailwindClasses` instead.
	 *
	 * @return string
	 */
	public static function getTwPart($part, $manifest, ...$custom)
	{
		if (!$part || !$manifest || !isset($manifest['tailwind']) || \array_keys($manifest['tailwind']) === []) {
			return $custom ? Helpers::clsx($custom) : ''; // @phpstan-ignore-line
		}

		$partClasses = $manifest['tailwind']['parts'][$part]['twClasses'] ?? '';

		if (\is_array($partClasses)) {
			$partClasses = \implode(' ', $partClasses);
		}

		return Helpers::clsx([$partClasses, ...$custom]);
	}

	/**
	 * Gets Tailwind classes for the provided dynamic part.
	 *
	 * The part needs to be defined within the manifest, in the `tailwind` object.
	 *
	 * @param string $part Part name.
	 * @param array<string, mixed> $attributes Component/block attributes.
	 * @param array<string, mixed> $manifest Component/block manifest data.
	 * @param array<string> ...$custom Additional custom classes.
	 *
	 * @deprecated 9.2.0 Use `tailwindClasses` instead.
	 *
	 * @return string
	 */
	public static function getTwDynamicPart($part, $attributes, $manifest, ...$custom)
	{
		if (!$part || !$manifest || !isset($manifest['tailwind']) || \array_keys($manifest['tailwind']) === []) {
			return $custom ? Helpers::clsx($custom) : ''; // @phpstan-ignore-line
		}

		$baseClasses = $manifest['tailwind']['parts'][$part]['twClasses'] ?? '';

		if (\is_array($baseClasses)) {
			$baseClasses = \implode(' ', $baseClasses);
		}

		$mainClasses = [];

		if (isset($manifest['tailwind']['options'])) {
			foreach ($manifest['tailwind']['options'] as $attributeName => $value) {
				if (!isset($value['part']) || $value['part'] !== $part) {
					continue;
				}

				$responsive = $value['responsive'] ?? false;
				$twClasses = $value['twClasses'] ?? null;

				if (!$twClasses) {
					continue;
				}

				$value = Helpers::checkAttr($attributeName, $attributes, $manifest, true);

				if (\is_bool($value)) {
					$value = $value ? 'true' : 'false';
				}

				if ($responsive ? empty($value['_default'] ?? '') : !$value) {
					continue;
				}

				if ($responsive ? empty($twClasses[$value['_default']] ?? '') : empty($twClasses[$value] ?? '')) {
					continue;
				}

				if (!$responsive) {
					$output = $twClasses[$value] ?? '';

					if (\is_array($output)) {
						$output = \implode(' ', $output);
					}

					$mainClasses = [...$mainClasses, $output];
					continue;
				}

				$valueKeys = \array_keys($value);

				$responsiveClasses = \array_reduce($valueKeys, function ($curr, $breakpoint) use ($twClasses, $value) {
					if ($breakpoint === '_desktopFirst') {
						return $curr;
					}

					$currentClasses = $twClasses[$value[$breakpoint]] ?? '';

					if (\is_array($currentClasses)) {
						$currentClasses = \implode(' ', $currentClasses);
					}

					if (!$currentClasses) {
						return $curr;
					}

					if ($breakpoint === '_default') {
						return [...$curr, $currentClasses];
					}

					$currentClasses = \explode(' ', $currentClasses);
					$currentClasses = \array_map(fn($currentClass) => "{$breakpoint}:{$currentClass}", $currentClasses);

					return [...$curr, ...$currentClasses];
				}, []);

				$mainClasses = [...$mainClasses, ...$responsiveClasses, ...$custom];
			}
		}

		return Helpers::clsx([$baseClasses, ...$mainClasses, ...$custom]);
	}

	/**
	 * Get Tailwind classes for the given component/block.
	 *
	 * @param array<string, mixed> $attributes Component/block attributes.
	 * @param array<string, mixed> $manifest Component/block manifest data.
	 * @param array<string> ...$custom Additional custom classes.
	 *
	 * @deprecated 9.2.0 Use `tailwindClasses` instead.
	 *
	 * @return string
	 */
	public static function getTwClasses($attributes, $manifest, ...$custom)
	{
		if (!$attributes || !$manifest || !isset($manifest['tailwind']) || \array_keys($manifest['tailwind']) === []) {
			return $custom ? Helpers::clsx($custom) : ''; // @phpstan-ignore-line
		}

		$baseClasses = $manifest['tailwind']['base']['twClasses'] ?? '';

		if (\is_array($baseClasses)) {
			$baseClasses = \implode(' ', $baseClasses);
		}

		$mainClasses = [];

		if (isset($manifest['tailwind']['options'])) {
			foreach ($manifest['tailwind']['options'] as $attributeName => $value) {
				if (isset($value['part'])) {
					continue;
				}

				$responsive = $value['responsive'] ?? false;
				$twClasses = $value['twClasses'] ?? null;

				if (!$twClasses) {
					continue;
				}

				$value = Helpers::checkAttr($attributeName, $attributes, $manifest, true);

				if (\is_bool($value)) {
					$value = $value ? 'true' : 'false';
				}

				if ($responsive ? empty($value['_default'] ?? '') : !$value) {
					continue;
				}

				if ($responsive ? empty($twClasses[$value['_default']] ?? '') : empty($twClasses[$value] ?? '')) {
					continue;
				}

				if (!$responsive) {
					$output = $twClasses[$value] ?? '';

					if (\is_array($output)) {
						$output = \implode(' ', $output);
					}

					$mainClasses = [...$mainClasses, $output];
					continue;
				}

				$valueKeys = \array_keys($value);

				$responsiveClasses = \array_reduce($valueKeys, function ($curr, $breakpoint) use ($twClasses, $value) {
					if ($breakpoint === '_desktopFirst') {
						return $curr;
					}

					$currentClasses = $twClasses[$value[$breakpoint]] ?? '';

					if (!$currentClasses) {
						return $curr;
					}

					if ($breakpoint === '_default') {
						return [...$curr, $currentClasses];
					}

					$currentClasses = \explode(' ', $currentClasses);
					$currentClasses = \array_map(fn($currentClass) => "{$breakpoint}:{$currentClass}", $currentClasses);

					return [...$curr, ...$currentClasses];
				}, []);

				$mainClasses = [...$mainClasses, ...$responsiveClasses, ...$custom];
			}
		}

		$combinationClasses = [];

		if (isset($manifest['tailwind']['combinations'])) {
			foreach ($manifest['tailwind']['combinations'] as $value) {
				$conditions = $value['attributes'];
				$twClasses = $value['twClasses'];

				if (\is_array($twClasses)) {
					$twClasses = \implode(' ', $twClasses);
				}

				$matches = true;

				foreach ($conditions as $key => $attrConditions) {
					$value = Helpers::checkAttr($key, $attributes, $manifest, true);

					$isArrayCondition = \is_array($attrConditions);

					if ($value === '' || $value === null) {
						$matches = false;
						break;
					} elseif ($isArrayCondition && !\in_array($value, $attrConditions, true)) {
						$matches = false;
						break;
					} elseif (!$isArrayCondition && $value !== $attrConditions) {
						$matches = false;
						break;
					}
				}

				if ($matches) {
					$combinationClasses = [...$combinationClasses, $twClasses];
				}
			}
		}

		return Helpers::clsx([$baseClasses, ...$mainClasses, ...$combinationClasses, ...$custom]);
	}

	/**
	 * Unifies the given input classes into a single string.
	 *
	 * Takes an array or string of CSS classes and unifies them into a single string,
	 * ensuring that there are no duplicate classes and that the classes are properly formatted.
	 *
	 * @param string|array<string> $input The input classes to be unified. This can be a string or an array of strings.
	 *
	 * @return string The unified string of CSS classes.
	 */
	private static function unifyClasses(string|array $input): string
	{
		if (\is_array($input)) {
			return Helpers::clsx($input);
		}

		return \trim($input);
	}

	/**
	 * Processes the given option for a specific part name.
	 *
	 * This method processes the option value for a given part name based on the provided definitions.
	 * It ensures that the option value is correctly handled according to the definitions.
	 *
	 * @param string $partName The name of the part for which the option is being processed.
	 * @param string|array<string, string> $optionValue The value of the option to be processed.
	 * @param array<string, mixed> $defs The definitions that dictate how the option should be processed.
	 *
	 * @return string The processed option value.
	 */
	private static function processOption(string $partName, string|array $optionValue, array $defs): string
	{
		$optionClasses = [];

		$isResponsive = $defs['responsive'] ?? false;
		$itemPartName = $defs['part'] ?? 'base';
		$isSingleValue = isset($defs['twClasses']) || isset($defs['twClassesEditor']);

		if (!$isSingleValue && !isset($defs[$partName])) {
			return '';
		}

		if ($isSingleValue && !\str_contains($itemPartName, $partName)) {
			return '';
		}

		if (!$isResponsive) {
			$rawValue = $defs['twClasses'][$optionValue] ?? $defs[$partName]['twClasses'][$optionValue] ?? '';

			return self::unifyClasses($rawValue);
		}

		foreach ($optionValue as $breakpoint => $breakpointValue) {
			if ($breakpoint === '_desktopFirst' || !$breakpointValue) {
				continue;
			}

			$rawValue = $defs['twClasses'][$breakpointValue] ?? $defs[$partName]['twClasses'][$breakpointValue] ?? '';
			$rawClasses = self::unifyClasses($rawValue);

			if ($breakpoint === '_default') {
				$optionClasses[] = $rawClasses;
				continue;
			}

			foreach (\explode(' ', $rawClasses) as $cn) {
				if ($cn === '') {
					continue;
				}
				$optionClasses[] = "{$breakpoint}:{$cn}";
			}
		}

		return self::unifyClasses($optionClasses);
	}

	/**
	 * Processes the given combination for a specific part name.
	 *
	 * This method processes the combination value for a given part name based on the provided attributes and manifest.
	 * It ensures that the combination is correctly handled according to the attributes and manifest.
	 *
	 * @param string $partName The name of the part for which the combination is being processed.
	 * @param array<string, mixed> $combo The combination value to be processed.
	 * @param array<string, mixed> $attributes The attributes that dictate how the combination should be processed.
	 * @param array<string, mixed> $manifest The manifest that provides additional context for processing the combination.
	 *
	 * @throws JsonException If the combination was not defined correctly.
	 *
	 * @return string The processed combination value.
	 */
	private static function processCombination(string $partName, array $combo, array $attributes, array $manifest): string
	{
		$matches = true;

		foreach ($combo['attributes'] as $attributeName => $allowedValue) {
			$optionValue = Helpers::checkAttr($attributeName, $attributes, $manifest, true);

			$isArrayCondition = \is_array($allowedValue);

			if ($isArrayCondition && !\in_array($optionValue, $allowedValue, true)) {
				$matches = false;
				break;
			} elseif (!$isArrayCondition && $optionValue !== $allowedValue) {
				$matches = false;
				break;
			}
		}

		if (!$matches) {
			return '';
		}

		$itemPartName = $combo['part'] ?? 'base';
		$isSingleValue = isset($combo['twClasses']) || isset($combo['twClassesEditor']);

		if ($isSingleValue && !\str_contains($itemPartName, $partName)) {
			return '';
		}

		$rawValue = $combo['output'][$partName]['twClasses'] ?? $combo['twClasses'] ?? '';

		if (\is_array($rawValue) && !\array_is_list($rawValue)) {
			throw new JsonException('Combination was not defined correctly. Please check the combination definition in the manifest.');
		}

		return self::unifyClasses($rawValue);
	}

	/**
	 * Get Tailwind classes for the given component/block.
	 *
	 * @param string $part Part to get classes for.
	 * @param array<string, mixed> $attributes Component/block attributes.
	 * @param array<string, mixed> $manifest Component/block manifest data.
	 * @param array<string> ...$custom Additional custom classes.
	 *
	 * @throws Exception If the part is not defined in the manifest.
	 *
	 * @return string
	 */
	public static function tailwindClasses(string $part, array $attributes, array $manifest, ...$custom): string
	{
		// If nothing is set, return custom classes as a fallback.
		if (!$part || !$manifest || !isset($manifest['tailwind']) || $manifest['tailwind'] === []) {
			return $custom ? Helpers::clsx($custom) : ''; // @phpstan-ignore-line
		}

		$partName = 'base';

		if (isset($manifest['tailwind']['parts'][$part])) {
			$partName = $part;
		} elseif ($part !== 'base') {
			throw new Exception("Part '{$part}' is not defined in the manifest.");
		}

		$baseClasses = self::unifyClasses($manifest['tailwind']['parts'][$partName]['twClasses'] ?? $manifest['tailwind']['base']['twClasses'] ?? ['']);

		$options = $manifest['tailwind']['options'] ?? [];
		$optionClasses = [];

		foreach ($options as $attributeName => $defs) {
			$optionValue = Helpers::checkAttr($attributeName, $attributes, $manifest, true);

			if (\is_bool($optionValue)) {
				$optionValue = $optionValue ? 'true' : 'false';
			}

			$optionClasses[] = self::processOption($partName, $optionValue, $defs);
		}

		$combinations = $manifest['tailwind']['combinations'] ?? [];
		$combinationClasses = [];

		foreach ($combinations as $combo) {
			$combinationClasses[] = self::processCombination($partName, $combo, $attributes, $manifest);
		}

		$debugPrefix = '';
		if (\defined('WP_DEBUG') && \WP_DEBUG) {
			$title = (string) ($manifest['title'] ?? '');
			if (!isset(self::$tailwindDebugSlugCache[$title])) {
				self::$tailwindDebugSlugCache[$title] = \strtolower(\preg_replace('/[^a-zA-Z]+/', '-', $title));
			}
			$debugPrefix = "_es__" . self::$tailwindDebugSlugCache[$title] . "/{$part}";
		}

		return Helpers::clsx([$debugPrefix, $baseClasses, ...$optionClasses, ...$combinationClasses, ...$custom]);
	}
}
