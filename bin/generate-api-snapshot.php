#!/usr/bin/env php
<?php

/**
 * Generate an API surface snapshot of all public/protected methods in src/.
 *
 * Usage: php bin/generate-api-snapshot.php
 * Output: JSON to stdout
 *
 * The snapshot is deterministic and diff-friendly. Commit api-surface.json
 * and regenerate on every PR to detect contract-breaking changes before they
 * reach downstream repos.
 *
 * Each entry captures:
 *   - PHP native type hints (method signatures)
 *   - @param / @phpstan-param phpdoc annotations (the precise shapes)
 *   - @return / @phpstan-return phpdoc annotations
 *   - Class-level @phpstan-type aliases (shape definitions)
 *
 * If any of these change, the diff will show it — even for changes that keep
 * the method signature identical (e.g. adding a required key to an array shape).
 *
 * @package EightshiftLibs
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
// WordPress stubs so WP-dependent classes (e.g. Walker_Nav_Menu) can be reflected.
require_once __DIR__ . '/../vendor/php-stubs/wordpress-stubs/wordpress-stubs.php';

/**
 * Extract @phpstan-type aliases from a class/trait docblock.
 *
 * Handles multi-line definitions (lines that continue after the type name).
 *
 * @return array<string, string>
 */
function extractTypeAliases(string $docComment): array
{
	$aliases = [];

	// Each @phpstan-type block runs until the next @annotation or end of docblock.
	if (!preg_match_all('/@phpstan-type\s+(\w+)\s+((?:[^@*]|\*(?!\/))*)/s', $docComment, $matches, PREG_SET_ORDER)) {
		return $aliases;
	}

	foreach ($matches as $match) {
		$name = $match[1];
		// Strip the leading " * " from continuation doc lines, then normalise whitespace.
		$raw = preg_replace('/\n\s*\*\s?/', ' ', $match[2]) ?? '';
		$aliases[$name] = trim((string) preg_replace('/\s+/', ' ', $raw));
	}

	return $aliases;
}

/**
 * Return the most specific phpdoc type for a parameter.
 *
 * Prefers @phpstan-param over @param so that PHPStan-only shapes take priority.
 */
function extractParamPhpdoc(string $docComment, string $paramName): string
{
	$escaped = preg_quote($paramName, '/');

	if (preg_match('/@phpstan-param\s+(\S+)\s+\$' . $escaped . '/m', $docComment, $m)) {
		return $m[1];
	}

	if (preg_match('/@param\s+(\S+)\s+\$' . $escaped . '/m', $docComment, $m)) {
		return $m[1];
	}

	return '';
}

/**
 * Return the most specific phpdoc return type.
 *
 * Prefers @phpstan-return over @return.
 */
function extractReturnPhpdoc(string $docComment): string
{
	if (preg_match('/@phpstan-return\s+(\S+)/m', $docComment, $m)) {
		return $m[1];
	}

	if (preg_match('/@return\s+(\S+)/m', $docComment, $m)) {
		return $m[1];
	}

	return '';
}

/**
 * Render a ReflectionType as a string, including nullability prefix.
 */
function reflectionTypeToString(?ReflectionType $type): string
{
	if ($type === null) {
		return '';
	}

	if ($type instanceof ReflectionNamedType) {
		$prefix = ($type->allowsNull() && $type->getName() !== 'null' && $type->getName() !== 'mixed') ? '?' : '';
		return $prefix . $type->getName();
	}

	// Union / intersection types (PHP 8+) — return the string representation.
	return (string) $type;
}

// ---------------------------------------------------------------------------
// Discover all FQNs by parsing source files
// ---------------------------------------------------------------------------

$srcDir = __DIR__ . '/../src';
/** @var array<int, array{fqn: string, kind: string}> $fqns */
$fqns = [];

$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator($srcDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
	/** @var SplFileInfo $file */
	if ($file->getExtension() !== 'php') {
		continue;
	}

	$content = file_get_contents($file->getPathname());
	if (!$content) {
		continue;
	}

	if (!preg_match('/^namespace\s+([^;{]+)[;{]/m', $content, $nsMatch)) {
		continue;
	}

	if (!preg_match('/^(?:abstract\s+)?(?:final\s+)?(?:readonly\s+)?(class|trait|interface)\s+(\w+)/m', $content, $kindMatch)) {
		continue;
	}

	$fqns[] = [
		'fqn'  => trim($nsMatch[1]) . '\\' . $kindMatch[2],
		'kind' => $kindMatch[1],
	];
}

// ---------------------------------------------------------------------------
// Reflect and build snapshot
// ---------------------------------------------------------------------------

/** @var array<string, mixed> $snapshot */
$snapshot = [];

foreach ($fqns as ['fqn' => $fqn, 'kind' => $kind]) {
	try {
		$reflection = new ReflectionClass($fqn);
	} catch (ReflectionException $e) {
		continue;
	}

	// Class/trait-level type aliases.
	$classDoc = $reflection->getDocComment() ?: '';
	$typeAliases = extractTypeAliases($classDoc);

	// Methods declared directly in this class/trait (not inherited).
	$methods = [];
	$filter  = ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED;

	foreach ($reflection->getMethods($filter) as $method) {
		if ($method->getDeclaringClass()->getName() !== $fqn) {
			continue;
		}

		$docComment = $method->getDocComment() ?: '';

		$params = [];
		foreach ($method->getParameters() as $param) {
			$params[] = [
				'name'       => $param->getName(),
				'type'       => reflectionTypeToString($param->getType()),
				'required'   => !$param->isOptional(),
				'hasDefault' => $param->isDefaultValueAvailable(),
				'phpdoc'     => extractParamPhpdoc($docComment, $param->getName()),
			];
		}

		$methods[$method->getName()] = [
			'visibility'   => $method->isPublic() ? 'public' : 'protected',
			'static'       => $method->isStatic(),
			'abstract'     => $method->isAbstract(),
			'params'       => $params,
			'returnType'   => reflectionTypeToString($method->getReturnType()),
			'phpdocReturn' => extractReturnPhpdoc($docComment),
		];
	}

	ksort($methods);

	$snapshot[$fqn] = [
		'kind'        => $kind,
		'typeAliases' => $typeAliases,
		'methods'     => $methods,
	];
}

ksort($snapshot);

echo json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
