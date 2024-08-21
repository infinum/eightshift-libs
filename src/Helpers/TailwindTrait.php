<?php

/**
 * TailwindCSS-related helper functions.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Class TailwindTrait Helper.
 */
trait TailwindTrait
{
	use StoreBlocksTrait;

	/**
	 * Get Tailwind breakpoints.
	 *
	 * @param bool $desktopFirst Whether to use desktop-first breakpoints.
	 *
	 * @return array<string>
	 */
	public static function getTwBreakpoints($desktopFirst = false)
	{
		$breakpointData = Helpers::getSettingsGlobalVariablesBreakpoints();

		$breakpointNames = \array_keys($breakpointData);

		\usort($breakpointNames, fn($a, $b) => $breakpointData[$a] - $breakpointData[$b]);

		if ($desktopFirst) {
			return \array_map(fn($breakpoint) => "max-{$breakpoint}", $breakpointNames);
		}

		return $breakpointNames;
	}

	/**
	 * Gets Tailwind classes for the provided part.
	 *
	 * The part needs to be defined within the manifest, in the `tailwind` object.
	 *
	 * @param string $part Part name.
	 * @param array<mixed> $manifest Component/block manifest data.
	 * @param array<string> ...$custom Additional custom classes.
	 *
	 * @return string
	 */
	public static function getTwPart($part, $manifest, ...$custom)
	{
		if (!$part || !$manifest || !isset($manifest['tailwind']) || \array_keys($manifest['tailwind']) === []) {
			return '';
		}

		$partClasses = $manifest['tailwind']['parts'][$part]['twClasses'] ?? '';

		if (is_array($partClasses)) {
			$partClasses = implode(' ', $partClasses);
		}

		return Helpers::classnames([$partClasses, ...$custom]);
	}

	/**
	 * Gets Tailwind classes for the provided dynamic part.
	 *
	 * The part needs to be defined within the manifest, in the `tailwind` object.
	 *
	 * @param string $part Part name.
	 * @param array<mixed> $attributes Component/block attributes.
	 * @param array<mixed> $manifest Component/block manifest data.
	 * @param array<string> ...$custom Additional custom classes.
	 *
	 * @return string
	 */
	public static function getTwDynamicPart($part, $attributes, $manifest, ...$custom)
	{
		if (!$part || !$manifest || !isset($manifest['tailwind']) || \array_keys($manifest['tailwind']) === []) {
			return '';
		}

		$baseClasses = $manifest['tailwind']['parts'][$part]['twClasses'] ?? '';

		if (is_array($baseClasses)) {
			$baseClasses = implode(' ', $baseClasses);
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

					if (is_array($output)) {
						$output = implode(' ', $output);
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

					if (is_array($currentClasses)) {
						$currentClasses = implode(' ', $currentClasses);
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

		return Helpers::classnames([$baseClasses, ...$mainClasses, ...$custom]);
	}

	/**
	 * Get Tailwind classes for the given component/block.
	 *
	 * @param array<mixed> $attributes Component/block attributes.
	 * @param array<mixed> $manifest Component/block manifest data.
	 * @param array<string> ...$custom Additional custom classes.
	 *
	 * @return string
	 */
	public static function getTwClasses($attributes, $manifest, ...$custom)
	{
		if (!$attributes || !$manifest || !isset($manifest['tailwind']) || \array_keys($manifest['tailwind']) === []) {
			return '';
		}

		$baseClasses = $manifest['tailwind']['base']['twClasses'] ?? '';

		if (is_array($baseClasses)) {
			$baseClasses = implode(' ', $baseClasses);
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

					if (is_array($output)) {
						$output = implode(' ', $output);
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
			foreach ($manifest['tailwind']['combinations'] as $attributeName => $value) {
				$conditions = $value['attributes'];
				$twClasses = $value['twClasses'];

				if (is_array($twClasses)) {
					$twClasses = implode(' ', $twClasses);
				}

				$matches = true;

				foreach ($conditions as $key => $attrConditions) {
					$value = Helpers::checkAttr($key, $attributes, $manifest, true);

					if (\is_bool($value)) {
						$value = $value ? 'true' : 'false';
					}

					$isArrayCondition = \is_array($attrConditions);

					if (!$value) {
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

		return Helpers::classnames([$baseClasses, ...$mainClasses, ...$combinationClasses, ...$custom]);
	}
}
