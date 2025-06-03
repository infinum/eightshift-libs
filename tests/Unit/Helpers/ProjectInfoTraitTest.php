<?php

/**
 * Comprehensive tests for ProjectInfoTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\ProjectInfoTrait;
use Brain\Monkey\Functions;

/**
 * Wrapper class to test ProjectInfoTrait methods without conflicts.
 */
class ProjectInfoTraitWrapper
{
	use ProjectInfoTrait;

	/**
	 * Override getPluginDetails to provide test data directly.
	 */
	protected static function getPluginDetails(): array
	{
		// Return test data directly instead of calling WordPress functions
		return self::$testPluginData ?? [];
	}

	/**
	 * Static test data for plugin details.
	 */
	private static array $testPluginData = [];

	/**
	 * Set test data for plugin details.
	 */
	public static function setTestPluginData(array $data): void
	{
		self::$testPluginData = $data;
	}

	/**
	 * Reset test data.
	 */
	public static function resetTestData(): void
	{
		self::$testPluginData = [];
	}
}

/**
 * Comprehensive test case for ProjectInfoTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\ProjectInfoTrait
 */
class ProjectInfoTraitTest extends BaseTestCase
{
	private ProjectInfoTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new ProjectInfoTraitWrapper();

		// Reset test data
		ProjectInfoTraitWrapper::resetTestData();

		// Mock WordPress functions with defaults
		Functions\when('esc_html')->returnArg(1);
		Functions\when('wp_get_theme')->justReturn($this->getMockTheme());
	}

	/**
	 * Create a mock theme object for testing.
	 */
	private function getMockTheme(): object
	{
		return new class() {
			private array $data = [
				'Version' => '2.1.0',
				'Name' => 'Test Theme',
				'TextDomain' => 'test-theme'
			];

			public function get(string $key): string
			{
				return $this->data[$key] ?? '';
			}

			public function setData(array $data): void
			{
				$this->data = $data;
			}
		};
	}

	/**
	 * @covers ::getPluginVersion
	 */
	public function testGetPluginVersionWithValidData(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '1.5.2',
			'Name' => 'Test Plugin',
			'TextDomain' => 'test-plugin'
		]);

		$result = $this->wrapper::getPluginVersion();
		$this->assertEquals('1.5.2', $result);
	}

	/**
	 * @covers ::getPluginVersion
	 */
	public function testGetPluginVersionWithMissingVersion(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Name' => 'Test Plugin',
			'TextDomain' => 'test-plugin'
			// Version key missing
		]);

		$result = $this->wrapper::getPluginVersion();
		$this->assertEquals('1.0.0', $result);
	}

	/**
	 * @covers ::getPluginVersion
	 */
	public function testGetPluginVersionWithEmptyVersion(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '',
			'Name' => 'Test Plugin',
			'TextDomain' => 'test-plugin'
		]);

		$result = $this->wrapper::getPluginVersion();
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getPluginVersion
	 */
	public function testGetPluginVersionWithEmptyPluginData(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([]);

		$result = $this->wrapper::getPluginVersion();
		$this->assertEquals('1.0.0', $result);
	}

	/**
	 * @covers ::getPluginName
	 */
	public function testGetPluginNameWithValidData(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '1.5.2',
			'Name' => 'Awesome Test Plugin',
			'TextDomain' => 'test-plugin'
		]);

		$result = $this->wrapper::getPluginName();
		$this->assertEquals('Awesome Test Plugin', $result);
	}

	/**
	 * @covers ::getPluginName
	 */
	public function testGetPluginNameWithMissingName(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '1.5.2',
			'TextDomain' => 'test-plugin'
			// Name key missing
		]);

		$result = $this->wrapper::getPluginName();
		$this->assertEquals('Plugin', $result);
	}

	/**
	 * @covers ::getPluginName
	 */
	public function testGetPluginNameWithEmptyName(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '1.5.2',
			'Name' => '',
			'TextDomain' => 'test-plugin'
		]);

		$result = $this->wrapper::getPluginName();
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getPluginTextDomain
	 */
	public function testGetPluginTextDomainWithValidData(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '1.5.2',
			'Name' => 'Test Plugin',
			'TextDomain' => 'awesome-plugin-domain'
		]);

		$result = $this->wrapper::getPluginTextDomain();
		$this->assertEquals('awesome-plugin-domain', $result);
	}

	/**
	 * @covers ::getPluginTextDomain
	 */
	public function testGetPluginTextDomainWithMissingTextDomain(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '1.5.2',
			'Name' => 'Test Plugin'
			// TextDomain key missing
		]);

		$result = $this->wrapper::getPluginTextDomain();
		$this->assertEquals('PluginTextDomain', $result);
	}

	/**
	 * @covers ::getPluginTextDomain
	 */
	public function testGetPluginTextDomainWithEmptyTextDomain(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '1.5.2',
			'Name' => 'Test Plugin',
			'TextDomain' => ''
		]);

		$result = $this->wrapper::getPluginTextDomain();
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getThemeVersion
	 */
	public function testGetThemeVersionWithValidTheme(): void
	{
		$mockTheme = $this->getMockTheme();
		$mockTheme->setData([
			'Version' => '3.2.1',
			'Name' => 'Test Theme',
			'TextDomain' => 'test-theme'
		]);

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		$result = $this->wrapper::getThemeVersion();
		$this->assertEquals('3.2.1', $result);
	}

	/**
	 * @covers ::getThemeVersion
	 */
	public function testGetThemeVersionWithMissingVersion(): void
	{
		$mockTheme = $this->getMockTheme();
		$mockTheme->setData([
			'Name' => 'Test Theme',
			'TextDomain' => 'test-theme'
			// Version missing
		]);

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		$result = $this->wrapper::getThemeVersion();
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getThemeVersion
	 */
	public function testGetThemeVersionWithEmptyVersion(): void
	{
		$mockTheme = $this->getMockTheme();
		$mockTheme->setData([
			'Version' => '',
			'Name' => 'Test Theme',
			'TextDomain' => 'test-theme'
		]);

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		$result = $this->wrapper::getThemeVersion();
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getThemeName
	 */
	public function testGetThemeNameWithValidTheme(): void
	{
		$mockTheme = $this->getMockTheme();
		$mockTheme->setData([
			'Version' => '2.1.0',
			'Name' => 'Beautiful Theme',
			'TextDomain' => 'beautiful-theme'
		]);

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		$result = $this->wrapper::getThemeName();
		$this->assertEquals('Beautiful Theme', $result);
	}

	/**
	 * @covers ::getThemeName
	 */
	public function testGetThemeNameWithMissingName(): void
	{
		$mockTheme = $this->getMockTheme();
		$mockTheme->setData([
			'Version' => '2.1.0',
			'TextDomain' => 'test-theme'
			// Name missing
		]);

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		$result = $this->wrapper::getThemeName();
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getThemeName
	 */
	public function testGetThemeNameWithEmptyName(): void
	{
		$mockTheme = $this->getMockTheme();
		$mockTheme->setData([
			'Version' => '2.1.0',
			'Name' => '',
			'TextDomain' => 'test-theme'
		]);

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		$result = $this->wrapper::getThemeName();
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getThemeTextDomain
	 */
	public function testGetThemeTextDomainWithValidTheme(): void
	{
		$mockTheme = $this->getMockTheme();
		$mockTheme->setData([
			'Version' => '2.1.0',
			'Name' => 'Test Theme',
			'TextDomain' => 'custom-theme-domain'
		]);

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		$result = $this->wrapper::getThemeTextDomain();
		$this->assertEquals('custom-theme-domain', $result);
	}

	/**
	 * @covers ::getThemeTextDomain
	 */
	public function testGetThemeTextDomainWithMissingTextDomain(): void
	{
		$mockTheme = $this->getMockTheme();
		$mockTheme->setData([
			'Version' => '2.1.0',
			'Name' => 'Test Theme'
			// TextDomain missing
		]);

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		$result = $this->wrapper::getThemeTextDomain();
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getThemeTextDomain
	 */
	public function testGetThemeTextDomainWithEmptyTextDomain(): void
	{
		$mockTheme = $this->getMockTheme();
		$mockTheme->setData([
			'Version' => '2.1.0',
			'Name' => 'Test Theme',
			'TextDomain' => ''
		]);

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		$result = $this->wrapper::getThemeTextDomain();
		$this->assertEquals('', $result);
	}

	/**
	 * Integration test - all plugin methods together
	 */
	public function testAllPluginMethodsIntegration(): void
	{
		$pluginData = [
			'Version' => '3.1.4',
			'Name' => 'Integration Test Plugin',
			'TextDomain' => 'integration-test-plugin',
			'Description' => 'A plugin for testing integration'
		];

		ProjectInfoTraitWrapper::setTestPluginData($pluginData);

		$version = $this->wrapper::getPluginVersion();
		$name = $this->wrapper::getPluginName();
		$textDomain = $this->wrapper::getPluginTextDomain();

		$this->assertEquals('3.1.4', $version);
		$this->assertEquals('Integration Test Plugin', $name);
		$this->assertEquals('integration-test-plugin', $textDomain);
	}

	/**
	 * Integration test - all theme methods together
	 */
	public function testAllThemeMethodsIntegration(): void
	{
		$mockTheme = $this->getMockTheme();
		$mockTheme->setData([
			'Version' => '4.2.0',
			'Name' => 'Integration Test Theme',
			'TextDomain' => 'integration-test-theme'
		]);

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		$version = $this->wrapper::getThemeVersion();
		$name = $this->wrapper::getThemeName();
		$textDomain = $this->wrapper::getThemeTextDomain();

		$this->assertEquals('4.2.0', $version);
		$this->assertEquals('Integration Test Theme', $name);
		$this->assertEquals('integration-test-theme', $textDomain);
	}

	/**
	 * Test esc_html escaping behavior
	 */
	public function testEscHtmlEscaping(): void
	{
		Functions\when('esc_html')->alias(function ($value) {
			return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		});

		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '<script>alert("xss")</script>',
			'Name' => '<b>Bold Plugin</b>',
			'TextDomain' => 'test&plugin'
		]);

		$version = $this->wrapper::getPluginVersion();
		$name = $this->wrapper::getPluginName();
		$textDomain = $this->wrapper::getPluginTextDomain();

		$this->assertEquals('<script>alert("xss")</script>', $version);
		$this->assertEquals('<b>Bold Plugin</b>', $name);
		$this->assertEquals('test&plugin', $textDomain);
	}

	/**
	 * Test esc_html is called only on fallback values
	 */
	public function testEscHtmlOnFallbackValues(): void
	{
		Functions\when('esc_html')->alias(function ($value) {
			return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
		});

		// Test when keys are missing (fallback values should be escaped)
		ProjectInfoTraitWrapper::setTestPluginData([]);

		$version = $this->wrapper::getPluginVersion();
		$name = $this->wrapper::getPluginName();
		$textDomain = $this->wrapper::getPluginTextDomain();

		// Fallback values should be escaped by esc_html
		$this->assertEquals('1.0.0', $version);
		$this->assertEquals('Plugin', $name);
		$this->assertEquals('PluginTextDomain', $textDomain);
	}

	/**
	 * Test fallback values when everything is empty
	 */
	public function testAllFallbackValues(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([]);

		$mockTheme = $this->getMockTheme();
		$mockTheme->setData([]);
		Functions\when('wp_get_theme')->justReturn($mockTheme);

		// Plugin fallbacks
		$this->assertEquals('1.0.0', $this->wrapper::getPluginVersion());
		$this->assertEquals('Plugin', $this->wrapper::getPluginName());
		$this->assertEquals('PluginTextDomain', $this->wrapper::getPluginTextDomain());

		// Theme fallbacks (empty strings)
		$this->assertEquals('', $this->wrapper::getThemeVersion());
		$this->assertEquals('', $this->wrapper::getThemeName());
		$this->assertEquals('', $this->wrapper::getThemeTextDomain());
	}

	/**
	 * Test plugin version with null coalescing operator behavior
	 */
	public function testGetPluginVersionNullCoalescing(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => null,
			'Name' => 'Test Plugin',
			'TextDomain' => 'test-plugin'
		]);

		$result = $this->wrapper::getPluginVersion();
		$this->assertEquals('1.0.0', $result);
	}

	/**
	 * Test plugin name with null coalescing operator behavior
	 */
	public function testGetPluginNameNullCoalescing(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '1.0.0',
			'Name' => null,
			'TextDomain' => 'test-plugin'
		]);

		$result = $this->wrapper::getPluginName();
		$this->assertEquals('Plugin', $result);
	}

	/**
	 * Test plugin text domain with null coalescing operator behavior
	 */
	public function testGetPluginTextDomainNullCoalescing(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '1.0.0',
			'Name' => 'Test Plugin',
			'TextDomain' => null
		]);

		$result = $this->wrapper::getPluginTextDomain();
		$this->assertEquals('PluginTextDomain', $result);
	}

	/**
	 * Test theme methods with null values
	 */
	public function testThemeMethodsWithNullValues(): void
	{
		$mockTheme = new class() {
			public function get(string $key): ?string
			{
				return null; // Always return null
			}
		};

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		// Theme methods should return empty strings when values are null
		$this->assertEquals('', $this->wrapper::getThemeVersion());
		$this->assertEquals('', $this->wrapper::getThemeName());
		$this->assertEquals('', $this->wrapper::getThemeTextDomain());
	}

	/**
	 * Test theme methods with short ternary operator
	 */
	public function testThemeMethodsShortTernaryBehavior(): void
	{
		// Test with false values (should return empty string)
		$mockTheme = new class() {
			public function get(string $key): bool
			{
				return false;
			}
		};

		Functions\when('wp_get_theme')->justReturn($mockTheme);

		$this->assertEquals('', $this->wrapper::getThemeVersion());
		$this->assertEquals('', $this->wrapper::getThemeName());
		$this->assertEquals('', $this->wrapper::getThemeTextDomain());
	}

	/**
	 * Test with valid string values that contain special characters
	 */
	public function testPluginMethodsWithSpecialCharacters(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '1.0.0-beta+build.1',
			'Name' => 'Test Plugin (Beta)',
			'TextDomain' => 'test-plugin_beta'
		]);

		$this->assertEquals('1.0.0-beta+build.1', $this->wrapper::getPluginVersion());
		$this->assertEquals('Test Plugin (Beta)', $this->wrapper::getPluginName());
		$this->assertEquals('test-plugin_beta', $this->wrapper::getPluginTextDomain());
	}

	/**
	 * Test plugin methods with whitespace values
	 */
	public function testPluginMethodsWithWhitespace(): void
	{
		ProjectInfoTraitWrapper::setTestPluginData([
			'Version' => '  1.0.0  ',
			'Name' => '  Test Plugin  ',
			'TextDomain' => '  test-plugin  '
		]);

		// Whitespace should be preserved (no trimming in the trait)
		$this->assertEquals('  1.0.0  ', $this->wrapper::getPluginVersion());
		$this->assertEquals('  Test Plugin  ', $this->wrapper::getPluginName());
		$this->assertEquals('  test-plugin  ', $this->wrapper::getPluginTextDomain());
	}
}
