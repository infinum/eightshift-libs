<?php

/**
 * Tests for AttributesTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\AttributesTrait;
use EightshiftLibs\Helpers\GeneralTrait;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Wrapper class to test AttributesTrait methods.
 */
class AttributesTraitWrapper
{
	use AttributesTrait;
	use GeneralTrait; // Needed for kebabToCamelCase
}

/**
 * Test case for AttributesTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\AttributesTrait
 */
class AttributesTraitTest extends BaseTestCase
{
	private AttributesTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new AttributesTraitWrapper();
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrReturnsExistingAttribute(): void
	{
		$attributes = ['testKey' => 'testValue'];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'testKey' => ['type' => 'string']
			]
		];

		$result = $this->wrapper::checkAttr('testKey', $attributes, $manifest);

		$this->assertEquals('testValue', $result);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrReturnsDefaultValueWhenKeyNotInAttributes(): void
	{
		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'testKey' => [
					'type' => 'string',
					'default' => 'defaultValue'
				]
			]
		];

		$result = $this->wrapper::checkAttr('testKey', $attributes, $manifest);

		$this->assertEquals('defaultValue', $result);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrReturnsFalseForBooleanWithoutDefault(): void
	{
		$attributes = [];
		$manifest = [
			'componentName' => 'test-component',
			'attributes' => [
				'isEnabled' => ['type' => 'boolean']
			]
		];

		$result = $this->wrapper::checkAttr('isEnabled', $attributes, $manifest);

		$this->assertFalse($result);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrReturnsEmptyArrayForArrayType(): void
	{
		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'items' => ['type' => 'array']
			]
		];

		$result = $this->wrapper::checkAttr('items', $attributes, $manifest);

		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrReturnsEmptyStringForStringType(): void
	{
		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'title' => ['type' => 'string']
			]
		];

		$result = $this->wrapper::checkAttr('title', $attributes, $manifest);

		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrThrowsExceptionWhenManifestMissingAttributes(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('testKey key does not exist - missing attributes in test-block block manifest');

		$attributes = [];
		$manifest = ['blockName' => 'test-block'];

		$this->wrapper::checkAttr('testKey', $attributes, $manifest);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrThrowsExceptionWhenKeyNotInManifest(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('missingKey key does not exist in the test-block block manifest');

		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'existingKey' => ['type' => 'string']
			]
		];

		$this->wrapper::checkAttr('missingKey', $attributes, $manifest);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrWithComponentContext(): void
	{
		$attributes = ['testValue' => 'value'];
		$manifest = [
			'componentName' => 'test-component',
			'attributes' => [
				'testKey' => ['type' => 'string']
			]
		];

		$result = $this->wrapper::checkAttr('testKey', $attributes, $manifest);

		// Should return default empty string since key not in attributes
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrReturnsNullWhenUndefinedAllowed(): void
	{
		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'optionalKey' => ['type' => 'string']
			]
		];

		$result = $this->wrapper::checkAttr('optionalKey', $attributes, $manifest, true);

		$this->assertNull($result);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrReturnsDefaultEvenWhenUndefinedAllowed(): void
	{
		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'keyWithDefault' => [
					'type' => 'string',
					'default' => 'defaultValue'
				]
			]
		];

		$result = $this->wrapper::checkAttr('keyWithDefault', $attributes, $manifest, true);

		$this->assertEquals('defaultValue', $result);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrWithPrefixTransformation(): void
	{
		$attributes = [
			'prefix' => 'custom',
			'customKey' => 'transformedValue'
		];
		$manifest = [
			'componentName' => 'test-component',
			'attributes' => [
				'testComponentKey' => ['type' => 'string']
			]
		];

		$result = $this->wrapper::checkAttr('testComponentKey', $attributes, $manifest);

		// Should find customKey after transformation
		$this->assertEquals('transformedValue', $result);
	}

	/**
	 * @covers ::checkAttr
	 */
	#[DataProvider('typeDefaultsProvider')]
	public function testCheckAttrReturnsCorrectDefaultByType(string $type, $expected): void
	{
		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'testAttr' => ['type' => $type]
			]
		];

		$result = $this->wrapper::checkAttr('testAttr', $attributes, $manifest);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrExceptionIncludesTipForComponentsKey(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessageMatches('/If you are using additional components/');

		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'components' => ['some-component'],
			'attributes' => [
				'validKey' => ['type' => 'string']
			]
		];

		$this->wrapper::checkAttr('invalidKey', $attributes, $manifest);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrWithObjectType(): void
	{
		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'settings' => ['type' => 'object']
			]
		];

		$result = $this->wrapper::checkAttr('settings', $attributes, $manifest);

		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrWithNumericDefault(): void
	{
		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'count' => [
					'type' => 'number',
					'default' => 42
				]
			]
		];

		$result = $this->wrapper::checkAttr('count', $attributes, $manifest);

		$this->assertEquals(42, $result);
	}

	/**
	 * @covers ::checkAttr
	 */
	public function testCheckAttrWithBooleanTrue(): void
	{
		$attributes = ['isActive' => true];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'isActive' => ['type' => 'boolean']
			]
		];

		$result = $this->wrapper::checkAttr('isActive', $attributes, $manifest);

		$this->assertTrue($result);
	}

	/**
	 * @covers ::getAttrKey
	 */
	public function testGetAttrKeyWithBlockName(): void
	{
		$attributes = [];
		$manifest = ['blockName' => 'test-block'];

		$result = $this->wrapper::getAttrKey('testKey', $attributes, $manifest);

		// Should return key unchanged for blocks
		$this->assertEquals('testKey', $result);
	}

	/**
	 * @covers ::getAttrKey
	 */
	public function testGetAttrKeyWithWrapperKey(): void
	{
		$attributes = ['prefix' => 'custom'];
		$manifest = ['componentName' => 'test-component'];

		$result = $this->wrapper::getAttrKey('wrapperClass', $attributes, $manifest);

		// Should return key unchanged for wrapper keys
		$this->assertEquals('wrapperClass', $result);
	}

	/**
	 * @covers ::getAttrKey
	 */
	public function testGetAttrKeyWithoutPrefix(): void
	{
		$attributes = [];
		$manifest = ['componentName' => 'test-component'];

		$result = $this->wrapper::getAttrKey('testKey', $attributes, $manifest);

		// Should return key unchanged when no prefix
		$this->assertEquals('testKey', $result);
	}

	/**
	 * @covers ::getAttrKey
	 */
	public function testGetAttrKeyWithPrefix(): void
	{
		$attributes = ['prefix' => 'custom'];
		$manifest = ['componentName' => 'test-component'];

		$result = $this->wrapper::getAttrKey('testComponentValue', $attributes, $manifest);

		// Should transform the key with prefix
		$this->assertEquals('customValue', $result);
	}

	/**
	 * @covers ::getAttrKey
	 */
	public function testGetAttrKeyWithEmptyComponentName(): void
	{
		$attributes = ['prefix' => 'custom'];
		$manifest = ['componentName' => ''];

		$result = $this->wrapper::getAttrKey('testKey', $attributes, $manifest);

		// Should return key unchanged when component name is empty
		$this->assertEquals('testKey', $result);
	}

	/**
	 * @covers ::props
	 */
	public function testPropsWithBasicAttributes(): void
	{
		$attributes = [
			'blockName' => 'test-block',
			'blockClass' => 'test-class',
			'customValue' => 'value'
		];

		$result = $this->wrapper::props('button', $attributes);

		$this->assertArrayHasKey('prefix', $result);
		$this->assertArrayHasKey('blockName', $result);
		$this->assertArrayHasKey('blockClass', $result);
		$this->assertEquals('test-block', $result['blockName']);
	}

	/**
	 * @covers ::props
	 */
	public function testPropsWithPrefix(): void
	{
		$attributes = [
			'prefix' => 'custom',
			'customButtonValue' => 'test'
		];

		$result = $this->wrapper::props('button', $attributes);

		$this->assertEquals('customButton', $result['prefix']);
		$this->assertArrayHasKey('customButtonValue', $result);
	}

	/**
	 * @covers ::props
	 */
	public function testPropsIncludesCommonAttributes(): void
	{
		$attributes = [
			'blockClientId' => 'abc123',
			'blockFullName' => 'test/block',
			'blockSsr' => true,
			'selectorClass' => 'selector',
			'additionalClass' => 'extra'
		];

		$result = $this->wrapper::props('component', $attributes);

		$this->assertArrayHasKey('blockClientId', $result);
		$this->assertArrayHasKey('blockFullName', $result);
		$this->assertArrayHasKey('blockSsr', $result);
		$this->assertArrayHasKey('selectorClass', $result);
		$this->assertArrayHasKey('additionalClass', $result);
	}

	/**
	 * @covers ::props
	 */
	public function testPropsWithManualAttributes(): void
	{
		$attributes = [
			'blockName' => 'test-block',
		];
		$manual = [
			'buttonLabel' => 'Click me',
			'blockClass' => 'override-class'
		];

		$result = $this->wrapper::props('button', $attributes, $manual);

		$this->assertArrayHasKey('blockClass', $result);
		$this->assertEquals('override-class', $result['blockClass']);
		$this->assertArrayHasKey('testBlockButtonLabel', $result);
	}

	/**
	 * @covers ::props
	 */
	public function testPropsFiltersAttributesByPrefix(): void
	{
		$attributes = [
			'prefix' => 'btn',
			'btnButtonSize' => 'large',
			'otherValue' => 'ignored',
			'blockName' => 'included'
		];

		$result = $this->wrapper::props('button', $attributes);

		$this->assertArrayHasKey('btnButtonSize', $result);
		$this->assertArrayNotHasKey('otherValue', $result);
		$this->assertArrayHasKey('blockName', $result);
	}

	/**
	 * @covers ::getAttrsOutput
	 */
	public function testGetAttrsOutputWithBasicAttributes(): void
	{
		$attrs = [
			'class' => 'test-class',
			'id' => 'test-id'
		];

		\Brain\Monkey\Functions\when('esc_attr')->returnArg(1);

		$result = $this->wrapper::getAttrsOutput($attrs);

		$this->assertStringContainsString("class='test-class'", $result);
		$this->assertStringContainsString("id='test-id'", $result);
	}

	/**
	 * @covers ::getAttrsOutput
	 */
	public function testGetAttrsOutputWithoutEscape(): void
	{
		$attrs = [
			'data-value' => '<script>alert("xss")</script>'
		];

		$result = $this->wrapper::getAttrsOutput($attrs, false);

		$this->assertStringContainsString('<script>', $result);
	}

	/**
	 * @covers ::getAttrsOutput
	 */
	public function testGetAttrsOutputWithEmptyValue(): void
	{
		$attrs = [
			'disabled' => '',
			'readonly' => false
		];

		\Brain\Monkey\Functions\when('esc_attr')->returnArg(1);

		$result = $this->wrapper::getAttrsOutput($attrs);

		// Empty string and false should output attribute name only
		$this->assertStringContainsString('disabled', $result);
		$this->assertStringContainsString('readonly', $result);
	}

	/**
	 * @covers ::getAttrsOutput
	 */
	public function testGetAttrsOutputWithZeroValue(): void
	{
		$attrs = [
			'tabindex' => 0,
			'count' => '0'
		];

		\Brain\Monkey\Functions\when('esc_attr')->returnArg(1);

		$result = $this->wrapper::getAttrsOutput($attrs);

		// Zero values should include the value
		$this->assertStringContainsString("tabindex='0'", $result);
		$this->assertStringContainsString("count='0'", $result);
	}

	/**
	 * @covers ::getAttrsOutput
	 */
	public function testGetAttrsOutputEscapesKeys(): void
	{
		$attrs = [
			'data-test' => 'value'
		];

		$escapeCalled = false;
		\Brain\Monkey\Functions\when('esc_attr')->alias(function ($value) use (&$escapeCalled) {
			$escapeCalled = true;
			return $value;
		});

		$this->wrapper::getAttrsOutput($attrs);

		$this->assertTrue($escapeCalled);
	}

	/**
	 * @covers ::getAttrsOutput
	 */
	public function testGetAttrsOutputFormatsCorrectly(): void
	{
		$attrs = [
			'href' => 'https://example.com',
			'target' => '_blank'
		];

		\Brain\Monkey\Functions\when('esc_attr')->returnArg(1);

		$result = $this->wrapper::getAttrsOutput($attrs);

		$this->assertStringStartsWith(' ', $result);
		$this->assertStringContainsString("='", $result);
	}

	/**
	 * @covers ::checkAttrResponsive
	 */
	public function testCheckAttrResponsiveThrowsExceptionWhenResponsiveAttributesMissing(): void
	{
		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => []
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('missing responsiveAttributes key');

		$this->wrapper::checkAttrResponsive('spacing', $attributes, $manifest);
	}

	/**
	 * @covers ::checkAttrResponsive
	 */
	public function testCheckAttrResponsiveThrowsExceptionWhenKeyMissing(): void
	{
		$attributes = [];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [],
			'responsiveAttributes' => [
				'width' => ['large' => 'widthLarge', 'desktop' => 'widthDesktop']
			]
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage('missing the spacing key');

		$this->wrapper::checkAttrResponsive('spacing', $attributes, $manifest);
	}

	/**
	 * @covers ::checkAttrResponsive
	 */
	public function testCheckAttrResponsiveReturnsResponsiveValues(): void
	{
		$attributes = [
			'spacingLarge' => '20px',
			'spacingDesktop' => '15px',
			'spacingTablet' => '10px'
		];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'spacingLarge' => ['type' => 'string', 'default' => '0'],
				'spacingDesktop' => ['type' => 'string', 'default' => '0'],
				'spacingTablet' => ['type' => 'string', 'default' => '0']
			],
			'responsiveAttributes' => [
				'spacing' => [
					'large' => 'spacingLarge',
					'desktop' => 'spacingDesktop',
					'tablet' => 'spacingTablet'
				]
			]
		];

		$result = $this->wrapper::checkAttrResponsive('spacing', $attributes, $manifest);

		$this->assertIsArray($result);
		$this->assertArrayHasKey('large', $result);
		$this->assertArrayHasKey('desktop', $result);
		$this->assertArrayHasKey('tablet', $result);
		$this->assertEquals('20px', $result['large']);
		$this->assertEquals('15px', $result['desktop']);
		$this->assertEquals('10px', $result['tablet']);
	}

	/**
	 * @covers ::checkAttrResponsive
	 */
	public function testCheckAttrResponsiveWithDefaults(): void
	{
		$attributes = [
			'spacingLarge' => '20px'
		];
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'spacingLarge' => ['type' => 'string', 'default' => '0'],
				'spacingDesktop' => ['type' => 'string', 'default' => '10px'],
				'spacingTablet' => ['type' => 'string', 'default' => '5px']
			],
			'responsiveAttributes' => [
				'spacing' => [
					'large' => 'spacingLarge',
					'desktop' => 'spacingDesktop',
					'tablet' => 'spacingTablet'
				]
			]
		];

		$result = $this->wrapper::checkAttrResponsive('spacing', $attributes, $manifest);

		$this->assertEquals('20px', $result['large']);
		$this->assertEquals('10px', $result['desktop']);
		$this->assertEquals('5px', $result['tablet']);
	}

	/**
	 * @covers ::getDefaultRenderAttributes
	 */
	public function testGetDefaultRenderAttributesWithEmptyManifest(): void
	{
		$manifest = [];
		$attributes = ['customAttr' => 'value'];

		$result = $this->wrapper::getDefaultRenderAttributes($manifest, $attributes);

		$this->assertEquals($attributes, $result);
	}

	/**
	 * @covers ::getDefaultRenderAttributes
	 */
	public function testGetDefaultRenderAttributesWithNoDefaults(): void
	{
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'title' => ['type' => 'string'],
				'content' => ['type' => 'string']
			]
		];
		$attributes = ['customAttr' => 'value'];

		$result = $this->wrapper::getDefaultRenderAttributes($manifest, $attributes);

		$this->assertEquals($attributes, $result);
	}

	/**
	 * @covers ::getDefaultRenderAttributes
	 */
	public function testGetDefaultRenderAttributesMergesDefaults(): void
	{
		$manifest = [
			'blockName' => 'test-block',
			'attributes' => [
				'title' => ['type' => 'string', 'default' => 'Default Title'],
				'showIcon' => ['type' => 'boolean', 'default' => true],
				'content' => ['type' => 'string', 'default' => 'Default Content']
			]
		];
		$attributes = [
			'title' => 'Custom Title'
		];

		$result = $this->wrapper::getDefaultRenderAttributes($manifest, $attributes);

		$this->assertEquals('Custom Title', $result['title']);
		$this->assertTrue($result['showIcon']);
		$this->assertEquals('Default Content', $result['content']);
	}

	/**
	 * @covers ::getDefaultRenderAttributes
	 */
	public function testGetDefaultRenderAttributesWithComponentPrefix(): void
	{
		$manifest = [
			'componentName' => 'button',
			'attributes' => [
				'buttonLabel' => ['type' => 'string', 'default' => 'Click me'],
				'buttonSize' => ['type' => 'string', 'default' => 'medium']
			]
		];
		$attributes = [
			'prefix' => 'customBtn'
		];

		$result = $this->wrapper::getDefaultRenderAttributes($manifest, $attributes);

		$this->assertArrayHasKey('customBtnLabel', $result);
		$this->assertArrayHasKey('customBtnSize', $result);
		$this->assertEquals('Click me', $result['customBtnLabel']);
		$this->assertEquals('medium', $result['customBtnSize']);
	}

	/**
	 * @covers ::getDefaultRenderAttributes
	 */
	public function testGetDefaultRenderAttributesSkipsWrapperAttributes(): void
	{
		$manifest = [
			'componentName' => 'button',
			'attributes' => [
				'buttonLabel' => ['type' => 'string', 'default' => 'Click'],
				'wrapperClass' => ['type' => 'string', 'default' => 'wrapper-class']
			]
		];
		$attributes = [
			'prefix' => 'customBtn'
		];

		$result = $this->wrapper::getDefaultRenderAttributes($manifest, $attributes);

		// wrapperClass should not be transformed
		$this->assertArrayHasKey('wrapperClass', $result);
		$this->assertArrayNotHasKey('customBtnClass', $result);
		$this->assertEquals('wrapper-class', $result['wrapperClass']);
	}

	/**
	 * Data providers
	 */
	public static function typeDefaultsProvider(): array
	{
		return [
			'boolean type' => ['boolean', false],
			'array type' => ['array', []],
			'object type' => ['object', []],
			'string type' => ['string', ''],
			'number type' => ['number', ''],
			'unknown type' => ['customType', ''],
		];
	}
}
