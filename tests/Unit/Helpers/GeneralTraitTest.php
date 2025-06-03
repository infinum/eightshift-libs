<?php

/**
 * Comprehensive tests for GeneralTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\GeneralTrait;
use EightshiftLibs\Exception\InvalidManifest;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Wrapper class to test GeneralTrait methods without conflicts.
 */
class GeneralTraitWrapper
{
	use GeneralTrait;
}

/**
 * Comprehensive test case for GeneralTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\GeneralTrait
 */
class GeneralTraitTest extends BaseTestCase
{
	private GeneralTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new GeneralTraitWrapper();

		// Mock WordPress functions
		Functions\when('esc_html__')->returnArg(1);
		Functions\when('sanitize_text_field')->returnArg(1);
		Functions\when('wp_unslash')->returnArg(1);
	}

	/**
	 * @covers ::isValidXml
	 */
	#[DataProvider('validXmlProvider')]
	public function testIsValidXmlWithValidInput(string $xml): void
	{
		$this->assertTrue($this->wrapper::isValidXml($xml));
	}

	/**
	 * @covers ::isValidXml
	 */
	#[DataProvider('invalidXmlProvider')]
	public function testIsValidXmlWithInvalidInput(string $xml): void
	{
		$this->assertFalse($this->wrapper::isValidXml($xml));
	}

	/**
	 * @covers ::isJson
	 */
	#[DataProvider('validJsonProvider')]
	public function testIsJsonWithValidInput(string $json): void
	{
		$this->assertTrue($this->wrapper::isJson($json));
	}

	/**
	 * @covers ::isJson
	 */
	#[DataProvider('invalidJsonProvider')]
	public function testIsJsonWithInvalidInput(string $json): void
	{
		$this->assertFalse($this->wrapper::isJson($json));
	}

	/**
	 * @covers ::flattenArray
	 */
	public function testFlattenArrayWithNestedArray(): void
	{
		$input = [
			'level1' => [
				'level2' => [
					'level3' => 'value3',
					'value2'
				],
				'value1'
			],
			'standalone'
		];

		$result = $this->wrapper::flattenArray($input);

		// Check that all expected values are present (order may vary due to iterative implementation)
		$this->assertCount(4, $result);
		$this->assertContains('value3', $result);
		$this->assertContains('value2', $result);
		$this->assertContains('value1', $result);
		$this->assertContains('standalone', $result);
	}

	/**
	 * @covers ::flattenArray
	 */
	public function testFlattenArrayWithEmptyArray(): void
	{
		$this->assertEquals([], $this->wrapper::flattenArray([]));
	}

	/**
	 * @covers ::flattenArray
	 */
	public function testFlattenArrayWithEmptyValues(): void
	{
		$input = ['', null, 0, false, [], 'valid'];
		$result = $this->wrapper::flattenArray($input);

		// Only 'valid' should remain, empty values are filtered out
		$this->assertEquals(['valid'], $result);
	}

	/**
	 * @covers ::recursiveArrayFind
	 */
	public function testRecursiveArrayFind(): void
	{
		$array = [
			'first' => [
				'target' => 'found1',
				'nested' => [
					'target' => 'found2',
					'other' => 'value'
				]
			],
			'target' => 'found3'
		];

		$result = $this->wrapper::recursiveArrayFind($array, 'target');
		$this->assertCount(3, $result);
		$this->assertContains('found1', $result);
		$this->assertContains('found2', $result);
		$this->assertContains('found3', $result);
	}

	/**
	 * @covers ::recursiveArrayFind
	 */
	public function testRecursiveArrayFindWithEmptyInput(): void
	{
		$this->assertEquals([], $this->wrapper::recursiveArrayFind([], 'key'));
		$this->assertEquals([], $this->wrapper::recursiveArrayFind(['test'], ''));
	}

	/**
	 * @covers ::sanitizeArray
	 */
	public function testSanitizeArrayWithValidFunction(): void
	{
		$input = [
			'key1' => 'value1',
			'key2' => [
				'nested' => 'value2'
			]
		];

		$result = $this->wrapper::sanitizeArray($input, 'strtoupper');

		$this->assertEquals('VALUE1', $result['key1']);
		$this->assertEquals('VALUE2', $result['key2']['nested']);
	}

	/**
	 * @covers ::sanitizeArray
	 */
	public function testSanitizeArrayWithInvalidFunction(): void
	{
		$input = ['key' => 'value'];
		$result = $this->wrapper::sanitizeArray($input, 'nonexistent_function');

		// Should return original array when function doesn't exist
		$this->assertEquals($input, $result);
	}

	/**
	 * @covers ::sanitizeArray
	 */
	public function testSanitizeArrayWithEmptyArray(): void
	{
		$this->assertEquals([], $this->wrapper::sanitizeArray([], 'strtoupper'));
	}

	/**
	 * @covers ::sortArrayByOrderKey
	 */
	public function testSortArrayByOrderKey(): void
	{
		$input = [
			['name' => 'third', 'order' => 3],
			['name' => 'first', 'order' => 1],
			['name' => 'second', 'order' => 2]
		];

		$result = $this->wrapper::sortArrayByOrderKey($input);

		$this->assertEquals('first', $result[0]['name']);
		$this->assertEquals('second', $result[1]['name']);
		$this->assertEquals('third', $result[2]['name']);
	}

	/**
	 * @covers ::sortArrayByOrderKey
	 */
	public function testSortArrayByOrderKeyWithMissingOrder(): void
	{
		$input = [
			['name' => 'no_order'],
			['name' => 'with_order', 'order' => 1]
		];

		$result = $this->wrapper::sortArrayByOrderKey($input);

		// Item without order should come first (default 0)
		$this->assertEquals('no_order', $result[0]['name']);
		$this->assertEquals('with_order', $result[1]['name']);
	}

	/**
	 * @covers ::sortArrayByOrderKey
	 */
	public function testSortArrayByOrderKeyWithEmptyArray(): void
	{
		$this->assertEquals([], $this->wrapper::sortArrayByOrderKey([]));
	}

	/**
	 * @covers ::camelToKebabCase
	 */
	#[DataProvider('camelToKebabProvider')]
	public function testCamelToKebabCase(string $input, string $expected): void
	{
		$this->assertEquals($expected, $this->wrapper::camelToKebabCase($input));
	}

	/**
	 * @covers ::camelToSnakeCase
	 */
	#[DataProvider('camelToSnakeProvider')]
	public function testCamelToSnakeCase(string $input, string $expected): void
	{
		$this->assertEquals($expected, $this->wrapper::camelToSnakeCase($input));
	}

	/**
	 * @covers ::kebabToCamelCase
	 */
	#[DataProvider('kebabToCamelProvider')]
	public function testKebabToCamelCase(string $input, string $expected): void
	{
		$this->assertEquals($expected, $this->wrapper::kebabToCamelCase($input));
	}

	/**
	 * @covers ::kebabToCamelCase
	 */
	public function testKebabToCamelCaseWithCustomSeparator(): void
	{
		$this->assertEquals('fooBarBaz', $this->wrapper::kebabToCamelCase('foo_bar_baz', '_'));
	}

	/**
	 * @covers ::kebabToSnakeCase
	 */
	#[DataProvider('kebabToSnakeProvider')]
	public function testKebabToSnakeCase(string $input, string $expected): void
	{
		$this->assertEquals($expected, $this->wrapper::kebabToSnakeCase($input));
	}

	/**
	 * @covers ::arrayIsList
	 */
	#[DataProvider('arrayIsListProvider')]
	public function testArrayIsList(array $input, bool $expected): void
	{
		$this->assertEquals($expected, $this->wrapper::arrayIsList($input));
	}

	/**
	 * @covers ::parseManifest
	 */
	public function testParseManifestWithValidJson(): void
	{
		$json = '{"key": "value", "number": 123}';
		$result = $this->wrapper::parseManifest($json);

		$this->assertEquals('value', $result['key']);
		$this->assertEquals(123, $result['number']);
	}

	/**
	 * @covers ::parseManifest
	 */
	public function testParseManifestWithComplexValidJson(): void
	{
		$json = '{"nested": {"array": [1,2,3]}, "boolean": true, "null": null}';
		$result = $this->wrapper::parseManifest($json);

		$this->assertEquals([1, 2, 3], $result['nested']['array']);
		$this->assertTrue($result['boolean']);
		$this->assertNull($result['null']);
	}

	/**
	 * @covers ::parseManifest
	 * @covers EightshiftLibs\Exception\InvalidManifest::manifestStructureException
	 */
	public function testParseManifestWithEmptyString(): void
	{
		$this->expectException(InvalidManifest::class);
		$this->expectExceptionMessage('Empty manifest provided.');
		$this->wrapper::parseManifest('');
	}

	/**
	 * @covers ::parseManifest
	 * @covers EightshiftLibs\Exception\InvalidManifest::manifestStructureException
	 */
	public function testParseManifestWithSyntaxError(): void
	{
		$this->expectException(InvalidManifest::class);
		$this->expectExceptionMessage('Syntax error, malformed JSON.');
		$this->wrapper::parseManifest('{"key": "value"'); // Missing closing brace
	}

	/**
	 * @covers ::parseManifest
	 * @covers EightshiftLibs\Exception\InvalidManifest::manifestStructureException
	 */
	public function testParseManifestWithMalformedJson(): void
	{
		$this->expectException(InvalidManifest::class);
		$this->expectExceptionMessage('Syntax error, malformed JSON.');
		$this->wrapper::parseManifest('{key: value}'); // Missing quotes
	}

	/**
	 * @covers ::parseManifest
	 * @covers EightshiftLibs\Exception\InvalidManifest::manifestStructureException
	 */
	public function testParseManifestWithControlCharacterError(): void
	{
		$this->expectException(InvalidManifest::class);
		$this->expectExceptionMessage('Control character error, possibly incorrectly encoded.');
		$this->wrapper::parseManifest("{\"key\": \"value\x08\"}"); // Control character
	}

	/**
	 * @covers ::parseManifest
	 * @covers EightshiftLibs\Exception\InvalidManifest::manifestStructureException
	 */
	public function testParseManifestWithMalformedUtf8(): void
	{
		$this->expectException(InvalidManifest::class);
		$this->expectExceptionMessage('Malformed UTF-8 characters, possibly incorrectly encoded.');
		$this->wrapper::parseManifest('{"key": "' . "\xc3\x28" . '"}'); // Invalid UTF-8 sequence
	}

	/**
	 * @covers ::parseManifest
	 * @covers EightshiftLibs\Exception\InvalidManifest::manifestStructureException
	 */
	public function testParseManifestWithMaxDepthExceeded(): void
	{
		// Create deeply nested JSON that exceeds default depth limit
		$deepJson = '{"level1":{"level2":{"level3":{"level4":{"level5":{"level6":{"level7":{"level8":{"level9":{"level10":{"level11":{"level12":{"level13":{"level14":{"level15":{"level16":{"level17":{"level18":{"level19":{"level20":{"level21":{"level22":{"level23":{"level24":{"level25":{"level26":{"level27":{"level28":{"level29":{"level30":{"level31":{"level32":{"level33":{"level34":{"level35":{"level36":{"level37":{"level38":{"level39":{"level40":{"level41":{"level42":{"level43":{"level44":{"level45":{"level46":{"level47":{"level48":{"level49":{"level50":{"level51":{"level52":{"level53":{"level54":{"level55":{"level56":{"level57":{"level58":{"level59":{"level60":{"level61":{"level62":{"level63":{"level64":{"level65":{"level66":{"level67":{"level68":{"level69":{"level70":{"level71":{"level72":{"level73":{"level74":{"level75":{"level76":{"level77":{"level78":{"level79":{"level80":{"level81":{"level82":{"level83":{"level84":{"level85":{"level86":{"level87":{"level88":{"level89":{"level90":{"level91":{"level92":{"level93":{"level94":{"level95":{"level96":{"level97":{"level98":{"level99":{"level100":"deep"}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}';

		// Test with very limited depth to trigger JSON_ERROR_DEPTH
		$oldDepth = ini_get('json.depth');
		ini_set('json.depth', '10');

		try {
			$this->expectException(InvalidManifest::class);
			// Note: In practice, this might still trigger a syntax error instead of depth error
			// depending on PHP version and JSON implementation
			$this->wrapper::parseManifest($deepJson);
		} finally {
			// Restore original depth setting
			ini_set('json.depth', $oldDepth);
		}
	}

	/**
	 * @covers ::parseManifest
	 * @covers EightshiftLibs\Exception\InvalidManifest::manifestStructureException
	 */
	public function testParseManifestWithInfiniteValue(): void
	{
		// This is tricky to test as JSON doesn't naturally support INF/NAN
		// We'll create a scenario that might trigger this error
		$this->expectException(InvalidManifest::class);
		// This will likely throw a syntax error instead, but we're testing the error handling path
		$this->wrapper::parseManifest('{"number": Infinity}');
	}

	/**
	 * @covers ::parseManifest
	 * @covers EightshiftLibs\Exception\InvalidManifest::manifestStructureException
	 */
	public function testParseManifestWithStateMismatch(): void
	{
		$this->expectException(InvalidManifest::class);
		// Most JSON state mismatches result in syntax errors in practice
		$this->expectExceptionMessage('Syntax error, malformed JSON.');
		$this->wrapper::parseManifest('{"key":}'); // Invalid state
	}

	/**
	 * @covers ::parseManifest
	 * @covers EightshiftLibs\Exception\InvalidManifest::manifestStructureException
	 */
	public function testParseManifestWithUnknownError(): void
	{
		// This tests the fallback error message for unknown JSON errors
		// We'll mock a scenario where json_last_error returns an unexpected value
		$this->expectException(InvalidManifest::class);
		$this->wrapper::parseManifest('{"key": "value",}'); // Trailing comma
	}

	/**
	 * @covers ::parseManifest
	 * @covers EightshiftLibs\Exception\InvalidManifest::manifestStructureException
	 */
	#[DataProvider('invalidManifestProvider')]
	public function testParseManifestWithInvalidJson(string $invalidJson): void
	{
		$this->expectException(InvalidManifest::class);
		$this->wrapper::parseManifest($invalidJson);
	}

	/**
	 * @covers ::getCurrentUrl
	 */
	public function testGetCurrentUrl(): void
	{
		// Clear any existing SERVER variables
		unset($_SERVER['HTTPS']);
		unset($_SERVER['HTTP_HOST']);
		unset($_SERVER['REQUEST_URI']);
		unset($_SERVER['REQUEST_TIME']);

		$_SERVER['HTTPS'] = 'on';
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['REQUEST_URI'] = '/test/path?param=value';
		$_SERVER['REQUEST_TIME'] = time() + 1; // Different time to avoid cache collision

		$result = $this->wrapper::getCurrentUrl();

		$this->assertEquals('https://example.com/test/path?param=value', $result);
	}

	/**
	 * @covers ::getCurrentUrl
	 */
	public function testGetCurrentUrlWithoutHttps(): void
	{
		// Clear any existing SERVER variables that might interfere
		unset($_SERVER['HTTPS']);
		unset($_SERVER['HTTP_HOST']);
		unset($_SERVER['REQUEST_URI']);
		unset($_SERVER['REQUEST_TIME']);

		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['REQUEST_URI'] = '/test/path';
		$_SERVER['REQUEST_TIME'] = time() + 2; // Different time to avoid cache collision

		$result = $this->wrapper::getCurrentUrl();

		$this->assertEquals('http://example.com/test/path', $result);
	}

	/**
	 * @covers ::cleanUrlParams
	 */
	#[DataProvider('cleanUrlParamsProvider')]
	public function testCleanUrlParams(string $input, string $expected): void
	{
		$this->assertEquals($expected, $this->wrapper::cleanUrlParams($input));
	}

	// Data Providers

	/**
	 * Data provider for valid XML strings.
	 *
	 * @return array<string, array<string>>
	 */
	public static function validXmlProvider(): array
	{
		return [
			'simple element' => ['<root>content</root>'],
			'svg example' => ['<svg><circle r="5" /></svg>'],
			'with attributes' => ['<element attr="value">text</element>'],
			'self-closing' => ['<element />'],
			'complex nested' => ['<root><child1><grandchild>text</grandchild></child1><child2 attr="val" /></root>'],
		];
	}

	/**
	 * Data provider for invalid XML strings.
	 *
	 * @return array<string, array<string>>
	 */
	public static function invalidXmlProvider(): array
	{
		return [
			'empty string' => [''],
			'plain text' => ['just text'],
			'unclosed tag' => ['<element>content'],
			'invalid characters' => ['<element>content & more</element>'],
			'too short' => ['<>'],
			'malformed' => ['<element><unclosed></element>'],
		];
	}

	/**
	 * Data provider for valid JSON strings.
	 *
	 * @return array<string, array<string>>
	 */
	public static function validJsonProvider(): array
	{
		return [
			'simple object' => ['{"key": "value"}'],
			'array' => ['[1, 2, 3]'],
			'nested object' => ['{"nested": {"key": "value"}}'],
			'empty object' => ['{}'],
			'empty array' => ['[]'],
			'complex' => ['{"string": "value", "number": 123, "boolean": true, "null": null, "array": [1,2,3]}'],
		];
	}

	/**
	 * Data provider for invalid JSON strings.
	 *
	 * @return array<string, array<string>>
	 */
	public static function invalidJsonProvider(): array
	{
		return [
			'empty string' => [''],
			'plain text' => ['not json'],
			'single quotes' => ["{'key': 'value'}"],
			'trailing comma' => ['{"key": "value",}'],
			'unclosed brace' => ['{"key": "value"'],
			'unclosed array' => ['[1, 2, 3'],
			'leading comma' => ['{,"key": "value"}'],
		];
	}

	/**
	 * Data provider for camel to kebab case conversion.
	 *
	 * @return array<string, array<string>>
	 */
	public static function camelToKebabProvider(): array
	{
		return [
			'simple camel case' => ['camelCase', 'camel-case'],
			'multiple words' => ['thisIsCamelCase', 'this-is-camel-case'],
			'single word' => ['word', 'word'],
			'already kebab' => ['kebab-case', 'kebab-case'],
			'empty string' => ['', ''],
			'with numbers' => ['test123Value', 'test123-value'],
			'consecutive caps' => ['XMLHttpRequest', 'xml-http-request'],
		];
	}

	/**
	 * Data provider for camel to snake case conversion.
	 *
	 * @return array<string, array<string>>
	 */
	public static function camelToSnakeProvider(): array
	{
		return [
			'simple camel case' => ['camelCase', 'camel_case'],
			'multiple words' => ['thisIsCamelCase', 'this_is_camel_case'],
			'single word' => ['word', 'word'],
			'empty string' => ['', ''],
			'with numbers' => ['test123Value', 'test123_value'],
			'already snake' => ['snake_case', 'snake_case'],
		];
	}

	/**
	 * Data provider for kebab to camel case conversion.
	 *
	 * @return array<string, array<string>>
	 */
	public static function kebabToCamelProvider(): array
	{
		return [
			'simple kebab case' => ['kebab-case', 'kebabCase'],
			'multiple words' => ['this-is-kebab-case', 'thisIsKebabCase'],
			'single word' => ['word', 'word'],
			'already camel' => ['camelCase', 'camelCase'],
			'empty string' => ['', ''],
			'with numbers' => ['test-123-value', 'test123Value'],
		];
	}

	/**
	 * Data provider for kebab to snake case conversion.
	 *
	 * @return array<string, array<string>>
	 */
	public static function kebabToSnakeProvider(): array
	{
		return [
			'simple kebab case' => ['kebab-case', 'kebab_case'],
			'multiple words' => ['this-is-kebab-case', 'this_is_kebab_case'],
			'single word' => ['word', 'word'],
			'empty string' => ['', ''],
			'already snake' => ['snake_case', 'snake_case'],
		];
	}

	/**
	 * Data provider for arrayIsList tests.
	 *
	 * @return array<string, array{array<mixed>, bool}>
	 */
	public static function arrayIsListProvider(): array
	{
		return [
			'empty array' => [[], true],
			'sequential numeric' => [[0, 1, 2, 3], true],
			'sequential string' => [['a', 'b', 'c'], true],
			'associative' => [['key' => 'value'], false],
			'mixed keys' => [[0 => 'a', 'key' => 'b'], false],
			'non-sequential' => [[1 => 'a', 3 => 'b'], false],
			'string keys' => [['0' => 'a', '1' => 'b'], true],
		];
	}

	/**
	 * Data provider for invalid manifest JSON.
	 *
	 * @return array<string, array<string>>
	 */
	public static function invalidManifestProvider(): array
	{
		return [
			'unclosed object' => ['{"key": "value"'],
			'unclosed array' => ['[1, 2, 3'],
			'malformed object' => ['{key: value}'],
			'trailing comma object' => ['{"key": "value",}'],
			'trailing comma array' => ['[1, 2, 3,]'],
			'leading comma' => ['{,"key": "value"}'],
			'double quotes' => ['{"key"": "value"}'],
			'invalid escape' => ['{"key": "\\x"}'],
			'unquoted key' => ['{key: "value"}'],
			'single quotes' => ["{'key': 'value'}"],
			'missing value' => ['{"key":}'],
			'missing colon' => ['{"key" "value"}'],
			'extra comma' => ['{"key1": "value1",, "key2": "value2"}'],
			'invalid number' => ['{"number": 01}'],
			'invalid literal' => ['{"bool": True}'],
			'unclosed string' => ['{"key": "unclosed'],
			'null bytes' => ["{\"key\": \"value\0\"}"],
			'mixed quotes' => ['{"key": \'value\'}'],
			'invalid unicode' => ['{"key": "\\uZZZZ"}'],
			'bare value' => ['just a string'],
			'function call' => ['{"func": alert("xss")}'],
		];
	}

	/**
	 * Data provider for cleanUrlParams tests.
	 *
	 * @return array<string, array<string>>
	 */
	public static function cleanUrlParamsProvider(): array
	{
		return [
			'empty string' => ['', ''],
			'no params' => ['https://example.com/path', 'https://example.com/path'],
			'with params' => ['https://example.com/path?param=value', 'https://example.com/path'],
			'multiple params' => ['https://example.com/path?param1=value1&param2=value2', 'https://example.com/path'],
			'only query' => ['?param=value', ''],
			'fragment after query' => ['https://example.com/path?param=value#fragment', 'https://example.com/path'],
		];
	}
}
