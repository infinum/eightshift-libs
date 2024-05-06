<?php

namespace Tests\Unit\Login;

use EightshiftLibs\Main\MainCli;
use Infinum\Main\Main;

use function Tests\getMockArgs;
use function Tests\reqOutputFiles;

beforeEach(function() {
	$mainCliMock = new MainCli('boilerplate');
	$mainCliMock([], getMockArgs($mainCliMock->getDefaultArgs()));

	reqOutputFiles(
		'Main/Main.php',
	);
});

test('Register method will call init hook', function () {
	(new Main([], ''))->register();

	$this->assertSame(10, has_action('after_setup_theme', 'Infinum\Main\Main->registerServices()'));
});
