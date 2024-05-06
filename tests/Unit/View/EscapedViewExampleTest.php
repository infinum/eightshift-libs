<?php

namespace Tests\Unit\View;

use EightshiftLibs\View\EscapedViewCli;
use Infinum\View\EscapedView;

use function Tests\getMockArgs;
use function Tests\reqOutputFiles;

beforeEach(function () {
	$escapedViewCliMock = new EscapedViewCli('boilerplate');
	$escapedViewCliMock([], getMockArgs($escapedViewCliMock->getDefaultArgs()));

	reqOutputFiles(
		'View/EscapedView.php',
	);
});

test('Escaped view class has register method', function () {
	$mock = (new EscapedView());

	$this->assertTrue(\method_exists($mock, 'register'));
	$this->assertEmpty($mock->register());
});
