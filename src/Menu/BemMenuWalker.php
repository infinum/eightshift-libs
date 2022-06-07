<?php
// phpcs:ignoreFile

/**
 * Custom Menu Walker specific functionality.
 * It provides BEM classes to menus.
 *
 * @package EightshiftLibs\Menu
 */

declare(strict_types=1);

namespace EightshiftLibs\Menu;

/**
 * Bem Menu Walker
 * Inserts some BEM naming sensibility into WordPress menus
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
	 * Menu item CSS suffixes.
	 *
	 * @var string[]
	 */
	public $itemCssClassSuffixes;

	/**
	 * Constructor function
	 *
	 * @param string $cssClassPrefix load menu prefix for class.
	 */
	public function __construct(string $cssClassPrefix)
	{
		$this->cssClassPrefix = $cssClassPrefix;

		// Define menu item names appropriately.
		$this->itemCssClassSuffixes = [
			'item' => '__item',
			'parent_item' => '__item--parent',
			'active_item' => '__item--active',
			'parent_of_active_item' => '__item--parent--active',
			'ancestor_of_active_item' => '__item--ancestor--active',
			'sub_menu' => '__sub-menu',
			'sub_menu_item' => '__sub-menu__item',
			'link' => '__link',
		];
	}

	/**
	 * Display element for walker
	 *
	 * @param object $element Data object.
	 * @param array<int, array<int, object>> $children_elements List of elements to continue traversing (passed by reference).
	 * @param int $max_depth Max depth to traverse.
	 * @param int $depth Depth of current element.
	 * @param Object[] $args An array of arguments.
	 * @param string $output Used to append additional content (passed by reference).
	 *
	 * @return void Parent Display element
	 *@see \Walker::display_element()
	 *
	 */
	public function display_element( // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps, PEAR.Functions.ValidDefaultValue.NotAtEnd
		$element,
		&$children_elements,
		$max_depth, // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps, PEAR.Functions.ValidDefaultValue.NotAtEnd
		$depth = 0,
		$args, // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps, PEAR.Functions.ValidDefaultValue.NotAtEnd
		&$output
	) {
		$id_field = $this->db_fields['id'];

		if (isset($args[0]-> has_children)) {
			$args[0]->has_children = !empty($children_elements[$element->$id_field]);
		}

		parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
	}

	/**
	 * Start level
	 *
	 * @see \Walker_Nav_Menu::start_lvl()
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param \stdClass|null $args An object of wp_nav_menu() arguments.
	 *
	 * @return void
	 */
	public function start_lvl( // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps, PEAR.Functions.ValidDefaultValue.NotAtEnd
		&$output,
		$depth = 1,
		$args = null
	) {
		$real_depth = $depth + 1;

		$indent = str_repeat("\t", $real_depth);

		$prefix = $this->cssClassPrefix;
		$suffix = $this->itemCssClassSuffixes;

		$classes = [
			$prefix . $suffix['sub_menu'],
			$prefix . $suffix['sub_menu'] . '--' . $real_depth,
		];

		$classNames = \implode(' ', $classes);

		// Add a ul wrapper to sub nav.
		$output .= "\n" . $indent . '<ul class="' . $classNames . '">' . "\n";
	}

	/**
	 * Add main/sub classes to li's and links.
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param \WP_Post $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param \stdClass|null $args An object of wp_nav_menu() arguments.
	 * @param int $id Current item ID.
	 *
	 * @return void
	 * @see \Walker_Nav_Menu::start_el()
	 *
     */
	public function start_el( // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps, PEAR.Functions.ValidDefaultValue.NotAtEnd
		&$output,
		$item,
		$depth = 0,
		$args = null,
		$id = 0
	) {
		$indent = ($depth > 0 ? str_repeat('    ', $depth) : ''); // code indent.

		$prefix = $this->cssClassPrefix;
		$suffix = $this->itemCssClassSuffixes;

		$parent_class = $prefix . $suffix['parent_item'];

		$itemClasses = [];

		if (!empty($item->classes)) {
			$userClasses = \array_map(
				function ($className) use ($prefix) {
					if (\strpos($className, 'js-') !== false) {
						$output = $className;
					} else {
						$output = $prefix . '__item--' . $className;
					}
					return $output;
				},
				$item->classes
			);

			// Item classes.
			$itemClasses = [
				'item_class' => 0 === $depth ? $prefix . $suffix['item'] : '',
				'parent_class' => isset($args->has_children) && $args->has_children ? $parent_class : '',
				'active_page_class' => \in_array(
					'current-menu-item',
					$item->classes,
					true
				) ? $prefix . $suffix['active_item'] : '',
				'active_parent_class' => \in_array(
					'current-menu-parent',
					$item->classes,
					true
				) ? $prefix . $suffix['parent_of_active_item'] : '',
				'active_ancestor_class' => \in_array(
					'current-page-ancestor',
					$item->classes,
					true
				) ? $prefix . $suffix['ancestor_of_active_item'] : '',
				'depth_class' => $depth >= 1 ? $prefix . $suffix['sub_menu_item'] . ' ' . $prefix . $suffix['sub_menu'] . '--' . $depth . '__item' : '',
				'item_id_class' => property_exists($item, 'object_id') ? $prefix . '__item--' . $item->object_id : '',
				'user_class' => !empty($userClasses) ? \implode(' ', $userClasses) : '',
			];
		}

		// Convert array to string excluding any empty values.
		$itemClasses = \apply_filters('walker_nav_menu_item_classes', $itemClasses, $item, $depth, $args);
		$class_string = !empty($itemClasses) ? \implode('  ', \array_filter($itemClasses)) : '';

		// Add the classes to the wrapping <li>.
		$output .= $indent . '<li class="' . $class_string . '">';

		// Link classes.
		$link_classes = [
			'item_link' => 0 === $depth ? $prefix . $suffix['link'] : '',
			'depth_class' => $depth >= 1 ? $prefix . $suffix['sub_menu'] . $suffix['link'] . '  ' . $prefix . $suffix['sub_menu'] . '--' . $depth . $suffix['link'] : '',
		];

		$link_class_string = \implode('  ', \array_filter($link_classes));

		$link_class_output = 'class="' . $link_class_string . ' "';

		$link_text_classes = [
			'item_link' => 0 === $depth ? $prefix . $suffix['link'] . '-text' : '',
			'depth_class' => $depth >= 1 ? $prefix . $suffix['sub_menu'] . $suffix['link'] . '-text ' . $prefix . $suffix['sub_menu'] . '--' . $depth . $suffix['link'] . '-text' : '',
		];

		$link_text_class_string = \implode('  ', \array_filter($link_text_classes));
		$link_text_class_output = 'class="' . $link_text_class_string . '"';

		// link attributes.
		$attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
		$attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
		$attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
		$attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

		// Create link markup.
		$item_output = !empty($args->before) ? $args->before : '';
		$item_output .= '<a' . $attributes . ' ' . $link_class_output . '><span ' . $link_text_class_output . '>';
		$item_output .= !empty($args->link_before) ? $args->link_before : '';
		$item_output .= !empty($item->title) ? apply_filters('the_title', $item->title, $item->ID) : '';
		$item_output .= !empty($args->link_after) ? $args->link_after : '';
		$item_output .= !empty($args->after) ? $args->after : '';
		$item_output .= '</span></a>';

		$output .= apply_filters('walker_nav_menu_link_element', $item_output, $item, $depth, $args);
	}
}
