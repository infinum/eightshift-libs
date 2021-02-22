<?php

/**
 * Custom BEM menu walker
 *
 * @package EightshiftLibs\Menu;
 */

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps, PEAR.Functions.ValidDefaultValue.NotAtEnd, Squiz.NamingConventions.ValidVariableName.NotCamelCaps

declare(strict_types=1);

namespace EightshiftLibs\Menu;

/**
 * BemExtendedMenu class.
 */
class BemMenuWalker extends \Walker_Nav_Menu
{
	/**
	 * CSS class prefix string - unique for a theme.
	 *
	 * @var string
	 */
	public $cssClassPrefix;

	/**
	 * List item custom class.
	 *
	 * @var string
	 */
	private $linkClasses;

	/**
	 * Menu item CSS suffixes.
	 *
	 * @var string[]
	 */
	private $itemCssClassSuffixes;

	/**
	 * Constructor function
	 *
	 * @param string $cssClassPrefix load menu prefix for class.
	 * @param string $linkClasses load menu prefix for class.
	 */
	public function __construct(string $cssClassPrefix, string $linkClasses)
	{
		$this->cssClassPrefix = $cssClassPrefix;
		$this->linkClasses = $linkClasses;

		// Define menu item names appropriately.
		$this->itemCssClassSuffixes = [
			'list' => '__list',
			'item' => '__item js-menu-item',
			'link' => '__link',
			'link_text' => '__link-text',
			'parent_item' => '__item--has-children',
			'active_item' => '__item--current',
			'parent_of_active_item' => '__item--current',
			'ancestor_of_active_item' => '__item--current',
			'sub_menu' => '__sub-menu',
			'sub_menu_item' => '__sub-menu-item',
			'sub_link' => '__sub-menu-link',
			'sub_link_text' => '__sub-menu-link-text',
		];
	}

	/**
	 * Display element for walker
	 *
	 * @see \Walker::display_element()
	 *
	 * @param object $element Data object.
	 * @param array  $children_elements List of elements to continue traversing (passed by reference).
	 * @param int    $max_depth Max depth to traverse.
	 * @param int    $depth Depth of current element.
	 * @param array  $args An array of arguments.
	 * @param string $output            Used to append additional content (passed by reference).
	 *
	 * @return void Parent Display element
	 */
	public function display_element(
		$element,
		&$children_elements,
		$max_depth,
		$depth = 0,
		$args,
		&$output
	) {
		$idField = $this->db_fields['id'];

		if (isset($args[0]-> has_children)) {
			$args[0]->has_children = !empty($children_elements[$element->$idField]);
		}

		parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
	}

	/**
	 * Start level
	 *
	 * @see \Walker_Nav_Menu::start_lvl()
	 *
	 * @param string         $output Used to append additional content (passed by reference).
	 * @param int            $depth Depth of menu item. Used for padding.
	 * @param \stdClass|null $args An object of wp_nav_menu() arguments.
	 *
	 * @return void
	 */
	public function start_lvl(
		&$output,
		$depth = 1,
		$args = null
	) {
		$realDepth = $depth + 1;

		$indent = str_repeat("\t", $realDepth);

		$classes = [
			$this->getPrefixedItem('sub_menu'),
			$this->getPrefixedItem('sub_menu') . '--' . $realDepth,
		];

		$classNames = implode(' ', $classes);

		// Add a ul wrapper to sub nav.
		$output .= "\n" . $indent . '<ul class="' . \esc_attr($classNames) . ' js-submenu' . '">' . "\n";
	}

	/**
	 * Add main/sub classes to li's and links.
	 *
	 * @see \Walker_Nav_Menu::start_el()
	 *
	 * @param string         $output Used to append additional content (passed by reference).
	 * @param \WP_Post       $item Menu item data object.
	 * @param int            $depth Depth of menu item. Used for padding.
	 * @param \stdClass|null $args An object of wp_nav_menu() arguments.
	 * @param int            $id Current item ID.
	 *
	 * @return void
	 */
	public function start_el(
		&$output,
		$item,
		$depth = 0,
		$args = null,
		$id = 0
	) {
		$indent = ($depth > 0 ? str_repeat('    ', $depth) : ''); // code indent.

		$prefix = $this->cssClassPrefix;

		$itemClasses = [];

		if (!empty($item->classes)) {

			/**
			 * Several types of built in classes:
			 *
			 * 1. menu-item (menu-item-type-post_type, menu-item-object-page, menu-item-has-children)
			 * 2. current (current-menu-item, current_page_item)
			 * 3. page (page_item, page-item-28)
			 *
			 * The aim here is only to leave custom classes coming from the admin,
			 * or what we passed through the arguments.
			 */
			$itemClasses = array_map(
				function ($className) use ($prefix) {
					if (strpos($className, 'menu-item-type') !== false) {
						$parts = explode('menu-item-type-', $className);
						$output = $prefix . '__item-type--' . $parts[1];
					} elseif (strpos($className, 'menu-item-object-') !== false) {
						$parts = explode('menu-item-object-', $className);
						$output = $prefix . '__item-object-type--' . $parts[1];
					} elseif (strpos($className, 'js-') !== false) {
						$output = $className;
					} elseif (strpos($className, 'menu-item') !== false) {
						$output = '';
					} elseif (strpos($className, 'current-page') !== false) {
						$output = '';
					} elseif (strpos($className, 'current-menu') !== false) {
						$output = '';
					} elseif (strpos($className, 'current_page') !== false) {
						$output = '';
					} elseif (strpos($className, 'page_item') !== false) {
						$output = '';
					} elseif (strpos($className, 'page-item') !== false) {
						$output = '';
					} else {
						$output = $className;
					}
					return $output;
				},
				$item->classes
			);

			// Remove empty class names.
			$itemClasses = array_filter($itemClasses, function ($className) {
				return !empty($className);
			});

			$itemClassesString = implode(', ', $item->classes);

			// Item classes.
			$itemClasses = [
				'item_class' => 0 === $depth ? $this->getPrefixedItem('item') : '',
				'parent_class' => isset($args->walker->has_children) && $args->walker->has_children ?
					$this->getPrefixedItem('parent_item') :
					'',
				'active_page_class' => strpos($itemClassesString, 'current-menu-item') !== false ?
					$this->getPrefixedItem('active_item') :
					'',
				'active_parent_class' => strpos($itemClassesString, 'current-menu-parent') !== false ?
					$this->getPrefixedItem('parent_of_active_item') :
					'',
				'active_ancestor_class' => strpos($itemClassesString, 'current-page-ancestor') !== false ?
					$this->getPrefixedItem('ancestor_of_active_item') :
					'',
				'depth_class' => $depth >= 1 ?
					$this->getPrefixedItem('sub_menu_item') . ' ' . $this->getPrefixedItem('sub_menu_item') . '--' . $depth :
					'',
				'item_id_class' => $prefix . '__item--' . $item->object_id,
				'user_class' => !empty($itemClasses) ? trim(implode(' ', $itemClasses)) : '',
			];
		}

		$itemClasses = \apply_filters('walker_nav_menu_item_classes', $itemClasses, $item, $depth, $args);

		// Remove duplicates.
		$itemClasses = array_keys(array_flip($itemClasses));

		// Convert array to string excluding any empty values.
		$classString = !empty($itemClasses) ? implode(' ', array_filter($itemClasses)) : '';

		// Add the classes to the wrapping <li>.
		$output .= $indent . '<li class="' . \esc_attr($classString) . '">';

		// Link classes.
		$linkClasses = [
			'item_link' => $depth === 0 ? $this->getPrefixedItem('link') : '',
			'depth_class' => $depth >= 1 ? $this->getPrefixedItem('sub_link') : '',
			'link_classes' => $this->linkClasses ?? ''
		];

		$linkClassString = implode(' ', array_filter($linkClasses));

		$linkClassOutput = 'class="' . trim(\esc_attr($linkClassString)) . '"';

		$linkTextClasses = [
			'item_link' => $depth === 0 ? $this->getPrefixedItem('link_text') : '',
			'depth_class' => $depth >= 1 ? $this->getPrefixedItem('sub_link_text') : '',
		];

		$linkTextClassString = implode(' ', array_filter($linkTextClasses));
		$linkTextClassOutput = 'class="' . trim(\esc_attr($linkTextClassString)) . '"';

		// link attributes.
		$attributes = !empty($item->attr_title) ? ' title="' . \esc_attr($item->attr_title) . '"' : '';
		$attributes .= !empty($item->target) ? ' target="' . \esc_attr($item->target) . '"' : '';
		$attributes .= !empty($item->xfn) ? ' rel="' . \esc_attr($item->xfn) . '"' : '';
		$attributes .= !empty($item->url) ? ' href="' . \esc_attr($item->url) . '"' : '';
		$attributes .= !empty($args->walker->has_children) ? ' aria-expanded="false"' : '';

		// Create link markup.
		$itemOutput = !empty($args->before) ? $args->before : '';
		$itemOutput .= '<a' . $attributes . ' ' . $linkClassOutput . '><span ' . $linkTextClassOutput . '>';
		$itemOutput .= !empty($args->link_before) ? $args->link_before : '';
		$itemOutput .= !empty($item->title) ? \apply_filters('the_title', $item->title, $item->ID) : '';
		$itemOutput .= !empty($args->link_after) ? $args->link_after : '';
		$itemOutput .= !empty($args->after) ? $args->after : '';
		$itemOutput .= '</span></a>';

		$output .= \apply_filters('walker_nav_menu_link_element', $itemOutput, $item, $depth, $args);
	}

	/**
	 * Helper to make prefixed items classes
	 *
	 * Prefix is the descriptor of the menu. You pass the type of item you want and you'll
	 * get back the prefixed item class.
	 *
	 * @param string $item Type of element to prefix. Read from $itemCssClassSuffixes member variable.
	 *
	 * @return string Prefixed class string.
	 */
	private function getPrefixedItem(string $item): string
	{
		$prefix = $this->cssClassPrefix;
		$suffix = $this->itemCssClassSuffixes;

		return $prefix . $suffix[$item];
	}
}

// phpcs:enable
