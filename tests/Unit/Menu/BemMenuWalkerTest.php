<?php

/**
 * Tests for BemMenuWalker class
 *
 * @package EightshiftLibs\Tests\Unit\Menu
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Menu;

use Brain\Monkey\Functions;
use EightshiftLibs\Menu\BemMenuWalker;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * BemMenuWalkerTest class
 */
class BemMenuWalkerTest extends BaseTestCase
{
	/**
	 * Set up before each test
	 */
	protected function setUp(): void
	{
		parent::setUp();

		// Stub Walker_Nav_Menu if not defined.
		if (!\class_exists('Walker_Nav_Menu')) {
			// phpcs:ignore
			eval('class Walker_Nav_Menu {
				public $db_fields = ["id" => "ID", "parent" => "menu_item_parent"];
				public function __construct() {}
				public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) {}
			}');
		}

		// Stub common WP functions.
		Functions\when('esc_attr')->returnArg();
		Functions\when('apply_filters')->returnArg(2);
	}

	/**
	 * Test constructor sets cssClassPrefix
	 */
	public function testConstructorSetsCssClassPrefix(): void
	{
		$walker = new BemMenuWalker('main-menu');

		$this->assertSame('main-menu', $walker->cssClassPrefix);
	}

	/**
	 * Test constructor sets itemCssClassSuffixes
	 */
	public function testConstructorSetsItemCssClassSuffixes(): void
	{
		$walker = new BemMenuWalker('main-menu');

		$this->assertIsArray($walker->itemCssClassSuffixes);
		$this->assertArrayHasKey('item', $walker->itemCssClassSuffixes);
		$this->assertArrayHasKey('link', $walker->itemCssClassSuffixes);
		$this->assertArrayHasKey('sub_menu', $walker->itemCssClassSuffixes);
		$this->assertArrayHasKey('parent_item', $walker->itemCssClassSuffixes);
		$this->assertArrayHasKey('active_item', $walker->itemCssClassSuffixes);
		$this->assertArrayHasKey('parent_of_active_item', $walker->itemCssClassSuffixes);
		$this->assertArrayHasKey('ancestor_of_active_item', $walker->itemCssClassSuffixes);
		$this->assertArrayHasKey('sub_menu_item', $walker->itemCssClassSuffixes);
	}

	/**
	 * Test display_element sets has_children true when element has children
	 */
	public function testDisplayElementSetsHasChildrenTrue(): void
	{
		$walker = new BemMenuWalker('nav');

		$element = new \stdClass();
		$element->ID = 1;

		$childElement = new \stdClass();
		$childElement->ID = 2;

		$children_elements = [1 => [$childElement]];
		$args = [(object)['has_children' => false]];
		$output = '';

		$walker->display_element($element, $children_elements, 0, 0, $args, $output);

		$this->assertTrue($args[0]->has_children);
	}

	/**
	 * Test display_element sets has_children false when element has no children
	 */
	public function testDisplayElementSetsHasChildrenFalse(): void
	{
		$walker = new BemMenuWalker('nav');

		$element = new \stdClass();
		$element->ID = 1;

		$children_elements = [];
		$args = [(object)['has_children' => true]];
		$output = '';

		$walker->display_element($element, $children_elements, 0, 0, $args, $output);

		$this->assertFalse($args[0]->has_children);
	}

	/**
	 * Test display_element does not crash when has_children is not set on args
	 */
	public function testDisplayElementHandlesMissingHasChildren(): void
	{
		$walker = new BemMenuWalker('nav');

		$element = new \stdClass();
		$element->ID = 1;

		$children_elements = [];
		$args = [(object)[]];
		$output = '';

		$walker->display_element($element, $children_elements, 0, 0, $args, $output);

		$this->assertSame('', $output);
	}

	/**
	 * Test start_lvl generates sub-menu wrapper with correct classes
	 */
	public function testStartLvlGeneratesSubMenuMarkup(): void
	{
		$walker = new BemMenuWalker('main-menu');
		$output = '';

		$walker->start_lvl($output, 0);

		$this->assertStringContainsString('<ul class="', $output);
		$this->assertStringContainsString('main-menu__sub-menu', $output);
	}

	/**
	 * Test start_lvl includes correct depth class
	 */
	public function testStartLvlIncludesDepthClass(): void
	{
		$walker = new BemMenuWalker('nav');
		$output = '';

		$walker->start_lvl($output, 1);

		// depth 1 → real_depth 2
		$this->assertStringContainsString('nav__sub-menu--2', $output);
	}

	/**
	 * Test start_lvl appends to existing output
	 */
	public function testStartLvlAppendsToOutput(): void
	{
		$walker = new BemMenuWalker('nav');
		$output = '<existing>';

		$walker->start_lvl($output, 0);

		$this->assertStringStartsWith('<existing>', $output);
		$this->assertStringContainsString('<ul class="', $output);
	}

	/**
	 * Test start_lvl at depth 0 has real_depth 1
	 */
	public function testStartLvlAtDepthZero(): void
	{
		$walker = new BemMenuWalker('menu');
		$output = '';

		$walker->start_lvl($output, 0);

		$this->assertStringContainsString('menu__sub-menu--1', $output);
	}

	/**
	 * Test start_el generates basic item markup at depth 0
	 */
	public function testStartElGeneratesBasicItemAtDepthZero(): void
	{
		$walker = new BemMenuWalker('main-menu');

		$item = $this->createMenuItem(1, 'Home', 'http://example.com', ['menu-item']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('<li class="', $output);
		$this->assertStringContainsString('<a', $output);
		$this->assertStringContainsString('href="http://example.com"', $output);
		$this->assertStringContainsString('main-menu__item', $output);
		$this->assertStringContainsString('main-menu__link', $output);
	}

	/**
	 * Test start_el generates sub-menu item at depth >= 1
	 */
	public function testStartElGeneratesSubMenuItemAtDepth(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(2, 'Sub Page', 'http://example.com/sub', ['menu-item']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 1, $args);

		$this->assertStringContainsString('nav__sub-menu__item', $output);
		$this->assertStringContainsString('nav__sub-menu--1__item', $output);
	}

	/**
	 * Test start_el adds active class for current-menu-item
	 */
	public function testStartElAddsActiveClass(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Home', '/', ['current-menu-item']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('nav__item--active', $output);
	}

	/**
	 * Test start_el adds parent active class for current-menu-parent
	 */
	public function testStartElAddsParentActiveClass(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Parent', '/', ['current-menu-parent']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('nav__item--parent--active', $output);
	}

	/**
	 * Test start_el adds ancestor active class for current-page-ancestor
	 */
	public function testStartElAddsAncestorActiveClass(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Ancestor', '/', ['current-page-ancestor']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('nav__item--ancestor--active', $output);
	}

	/**
	 * Test start_el adds parent class when has_children
	 */
	public function testStartElAddsParentClassWhenHasChildren(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Parent', '/', ['menu-item']);
		$args = $this->createMenuArgs(true);
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('nav__item--parent', $output);
	}

	/**
	 * Test start_el handles js- prefixed classes
	 */
	public function testStartElHandlesJsPrefixedClasses(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Home', '/', ['js-custom-handler']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		// js- classes should be kept as-is without prefix
		$this->assertStringContainsString('js-custom-handler', $output);
	}

	/**
	 * Test start_el adds user classes with prefix
	 */
	public function testStartElAddsUserClassesWithPrefix(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Home', '/', ['custom-class']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('nav__item--custom-class', $output);
	}

	/**
	 * Test start_el adds object_id class
	 */
	public function testStartElAddsObjectIdClass(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(42, 'Page', '/', ['menu-item']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('nav__item--42', $output);
	}

	/**
	 * Test start_el includes link attributes
	 */
	public function testStartElIncludesLinkAttributes(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'External', 'http://example.com', ['menu-item']);
		$item->attr_title = 'Link Title';
		$item->target = '_blank';
		$item->xfn = 'nofollow';

		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('title="Link Title"', $output);
		$this->assertStringContainsString('target="_blank"', $output);
		$this->assertStringContainsString('rel="nofollow"', $output);
	}

	/**
	 * Test start_el with before/after args
	 */
	public function testStartElWithBeforeAfterArgs(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Home', '/', ['menu-item']);
		$args = $this->createMenuArgs();
		$args->before = '<span class="before">';
		$args->after = '</span>';

		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('<span class="before">', $output);
		$this->assertStringContainsString('</span>', $output);
	}

	/**
	 * Test start_el with link_before/link_after args
	 */
	public function testStartElWithLinkBeforeAfterArgs(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Home', '/', ['menu-item']);
		$args = $this->createMenuArgs();
		$args->link_before = '<icon>';
		$args->link_after = '</icon>';

		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('<icon>', $output);
		$this->assertStringContainsString('</icon>', $output);
	}

	/**
	 * Test start_el with empty title
	 */
	public function testStartElWithEmptyTitle(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, '', '/', ['menu-item']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		// Should still have the link element
		$this->assertStringContainsString('<a', $output);
		$this->assertStringContainsString('<li class="', $output);
	}

	/**
	 * Test start_el with empty classes array
	 */
	public function testStartElWithEmptyClassesArray(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Home', '/');
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('<li class="', $output);
		$this->assertStringContainsString('<a', $output);
	}

	/**
	 * Test start_el with multiple combined classes
	 */
	public function testStartElWithMultipleCombinedClasses(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Home', '/', [
			'current-menu-item',
			'current-menu-parent',
			'current-page-ancestor',
			'custom-class',
		]);
		$args = $this->createMenuArgs(true);
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('nav__item--active', $output);
		$this->assertStringContainsString('nav__item--parent--active', $output);
		$this->assertStringContainsString('nav__item--ancestor--active', $output);
		$this->assertStringContainsString('nav__item--parent', $output);
		$this->assertStringContainsString('nav__item--custom-class', $output);
	}

	/**
	 * Test start_el appends to existing output
	 */
	public function testStartElAppendsToExistingOutput(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Home', '/', ['menu-item']);
		$args = $this->createMenuArgs();
		$output = '<previous>';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringStartsWith('<previous>', $output);
	}

	/**
	 * Test start_el link text classes at depth 0
	 */
	public function testStartElLinkTextClassesAtDepthZero(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Home', '/', ['menu-item']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringContainsString('nav__link-text', $output);
	}

	/**
	 * Test start_el link classes at sub-menu depth
	 */
	public function testStartElLinkClassesAtSubMenuDepth(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Sub', '/', ['menu-item']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 2, $args);

		$this->assertStringContainsString('nav__sub-menu__link', $output);
		$this->assertStringContainsString('nav__sub-menu--2__link', $output);
	}

	/**
	 * Test start_el at depth 0 doesn't add sub-menu classes
	 */
	public function testStartElAtDepthZeroNoSubMenuClasses(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'Home', '/', ['menu-item']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringNotContainsString('nav__sub-menu__item', $output);
	}

	/**
	 * Test start_el item without url omits href
	 */
	public function testStartElItemWithoutUrl(): void
	{
		$walker = new BemMenuWalker('nav');

		$item = $this->createMenuItem(1, 'No Link', '', ['menu-item']);
		$args = $this->createMenuArgs();
		$output = '';

		$walker->start_el($output, $item, 0, $args);

		$this->assertStringNotContainsString('href=', $output);
	}

	/**
	 * Create a menu item object for testing.
	 *
	 * @param int $id Item ID.
	 * @param string $title Item title.
	 * @param string $url Item URL.
	 * @param array<string> $classes CSS classes.
	 * @return \stdClass
	 */
	private function createMenuItem(int $id, string $title, string $url, array $classes = []): \stdClass
	{
		$item = new \stdClass();
		$item->ID = $id;
		$item->object_id = $id;
		$item->title = $title;
		$item->url = $url;
		$item->classes = $classes;
		$item->attr_title = '';
		$item->target = '';
		$item->xfn = '';

		return $item;
	}

	/**
	 * Create menu args object for testing.
	 *
	 * @param bool $hasChildren Whether menu has children.
	 * @return \stdClass
	 */
	private function createMenuArgs(bool $hasChildren = false): \stdClass
	{
		$args = new \stdClass();
		$args->has_children = $hasChildren;
		$args->before = '';
		$args->after = '';
		$args->link_before = '';
		$args->link_after = '';

		return $args;
	}
}
