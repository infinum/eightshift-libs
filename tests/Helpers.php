<?php

namespace Tests;

use Brain\Monkey\Functions;

// Mock WP functions
Functions\stubTranslationFunctions();
Functions\stubEscapeFunctions();

// Mock the template dir location.
Functions\when('get_template_directory')->justReturn(dirname(__FILE__) . '/data');

// Mock escaping function.
Functions\when('wp_kses_post')->returnArg();

