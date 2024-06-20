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
	 * @param bool $mobileFirst Whether to use mobile-first breakpoints.
	 *
	 * @return array<string>
	 */
	public static function getTwBreakpoints($mobileFirst = false)
	{
		$breakpointData = Helpers::getSettingsGlobalVariablesBreakpoints();

		$breakpointNames = \array_keys($breakpointData);

		\usort($breakpointNames, fn($a, $b) => $breakpointData[$a] - $breakpointData[$b]);

		if ($mobileFirst) {
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

		return Helpers::classnames([$partClasses, ...$custom]);
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

		$mainClasses = [];

		if (isset($manifest['tailwind']['options'])) {
			foreach ($manifest['tailwind']['options'] as $attributeName => $value) {
				$responsive = $value['responsive'] ?? false;
				$twClasses = $value['twClasses'] ?? null;

				if (!$twClasses) {
					continue;
				}

				$value = Helpers::checkAttr($attributeName, $attributes, $manifest, true);

				if ($responsive ? empty($value['_default'] ?? '') : !$value) {
					continue;
				}

				if ($responsive ? empty($twClasses[$value['_default']] ?? '') : empty($twClasses[$value] ?? '')) {
					continue;
				}

				if (!$responsive) {
					$mainClasses = [...$mainClasses, $twClasses[$value]];
					continue;
				}

				$valueKeys = \array_keys($value);

				$responsiveClasses = \array_reduce($valueKeys, function ($curr, $breakpoint) use ($twClasses, $value) {
					if ($breakpoint === '_mobileFirst') {
						return $curr;
					}

					$currentClasses = $twClasses[$value[$breakpoint]];

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

				$matches = true;

				foreach ($conditions as $key => $attrConditions) {
					$value = Helpers::checkAttr($key, $attributes, $manifest, true);

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
