<?php

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Enqueue;

use Brain\Monkey\Functions;
use EightshiftLibs\Enqueue\Theme\AbstractEnqueueTheme;
use EightshiftLibs\Tests\BaseTestCase;

final class ThemeDiagnosticsProbe extends AbstractEnqueueTheme
{
	public function register(): void {}
	public function getAssetsPrefix(): string { return 'test'; }
	public function getAssetsVersion(): string { return '1'; }
}

final class ThemeDiagnosticsTest extends BaseTestCase
{
	public function testMissingTransitiveDependencyAndLateRegistration(): void
	{
		$scripts = (object) [
			'queue' => ['test-theme-scripts'],
			'done' => [],
			'registered' => [
				'test-theme-scripts' => (object) ['deps' => ['parent']],
				'parent' => (object) ['deps' => ['missing?ver=1']],
			],
		];
		Functions\when('wp_scripts')->justReturn($scripts);
		Functions\when('esc_html')->alias(fn (string $value): string => $value);
		Functions\expect('_doing_it_wrong')->once()->withArgs(fn ($method, $message) => str_contains($message, 'missing'));
		$service = new ThemeDiagnosticsProbe();
		$service->diagnoseMissingThemeScriptDependencies();
		$scripts->registered['missing'] = (object) ['deps' => []];
		$service->diagnoseMissingThemeScriptDependencies();
		$scripts->done[] = 'test-theme-scripts';
		unset($scripts->registered['missing']);
		$service->diagnoseMissingThemeScriptDependencies();
	}
}
