<?php

// Autoload the WP integration files.
require_once dirname(__FILE__, 2) . '/wp/tests/phpunit/includes/phpunit-adapter-testcase.php';
require_once dirname(__FILE__, 2) . '/wp/tests/phpunit/includes/abstract-testcase.php';
require_once dirname(__FILE__, 2) . '/wp/tests/phpunit/includes/testcase.php';

uses()->group('integration')->in('Integration');
uses()->group('unit')->in('Unit');

uses(\WP_UnitTestCase::class)->in('Integration');
