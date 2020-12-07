<?php

namespace Tests\Unit\Menu;

use Brain\Monkey\Functions;
use EightshiftLibs\Menu\MenuCli;


/**
 * Components::ensureString tests
 */
test('Menu CLI command will correctly copy the Menu class', function () {
	Functions\when('EightshiftLibs\Menu\MenuCli\outputWrite')->justecho();

	$menu = new MenuCli('boilerplate');

	$result = $menu([], []);

	var_dump($result);
});

