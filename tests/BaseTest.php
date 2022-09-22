<?php

declare(strict_types=1);

namespace Tests;

use Brain\Monkey\Functions;
use Exception;
use Mockery\MockInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Yoast\WPTestUtils\BrainMonkey\TestCase;

class BaseTest extends TestCase
{
	protected MockInterface $wpCliMock;

	protected function set_up()
	{
		parent::set_up();

		$this->stubTranslationFunctions();
		$this->stubEscapeFunctions();

		// Mock the template dir location.
		Functions\when('get_template_directory')->justReturn(\dirname(__FILE__) . \DIRECTORY_SEPARATOR . 'data');

		// Mock the template dir location.
		Functions\when('get_stylesheet_directory')->justReturn(\dirname(__FILE__) . \DIRECTORY_SEPARATOR);

		// Mock escaping function.
		Functions\when('wp_kses_post')->returnArg();

		// Mock escaping function.
		Functions\when('esc_html__')->returnArg();

		// Mock rand function.
		Functions\when('wp_rand')->justReturn(1154790670);

		// Mock json success and error handlers.
		Functions\when('wp_send_json_success')->alias(function ($data = null, $statusCode = null, $options = 0) {
			$response = ['success' => true];

			if (isset($data)) {
				$response['data'] = $data;
			}

			echo json_encode($response, $options);
		});

		Functions\when('wp_send_json_error')->alias(function ($data = null, $statusCode = null, $options = 0) {
			$response = ['success' => false];

			if (isset($data)) {
				$response['data'] = $data;
			}

			echo json_encode($response, $options);
		});

		// Mock rest response handler.
		Functions\when('rest_ensure_response')->returnArg();

		// Mock site_url function.
		Functions\when('site_url')->justReturn('https://example.com');

		// Mock wp_get_attachment_metadata function.
		Functions\when('wp_get_attachment_metadata')->justReturn(attachemntMetaDataMock());

		// Mock get_post_meta function.
		Functions\when('get_post_meta')->justReturn('');

		// Mock wp_delete_file function.
		Functions\when('wp_delete_file')->justReturn('');

		// Mock ACF add options page function
		Functions\when('acf_add_options_page')->justReturn(true);

		// Mock ACF add options subpage function
		Functions\when('acf_add_options_sub_page')->justReturn(true);

		// Mock ACF add local field group function
		Functions\when('acf_add_local_field_group')->justReturn(true);

		// Mock current_user_can function.
		Functions\when('current_user_can')->returnArg();

		// Mock get_field function.
		Functions\when('get_field')->returnArg();

		// Mock wp_parse_url function.
		Functions\when('wp_parse_url')->justReturn([
			'scheme' => 'https',
			'host' => 'developer.wordpress.org',
			'path' => '/reference/functions/wp_parse_url/',
		]);

		$this->wpCliMock = mock('alias:WP_CLI');

		$this->wpCliMock
			->shouldReceive('success')
			->andReturnUsing(function ($message) {
				putenv("ES_CLI_SUCCESS_HAPPENED={$message}");
			});

		$this->wpCliMock
			->shouldReceive('error')
			->andReturnUsing(function ($errorMessage) {
				putenv("ES_CLI_ERROR_HAPPENED={$errorMessage}");
				throw new Exception($errorMessage);
			});

		$this->wpCliMock
			->shouldReceive('log')
			->andReturnUsing(function ($message) {
				putenv("ES_CLI_LOG_HAPPENED={$message}");
			});

		$this->wpCliMock
			->shouldReceive('add_command')
			->andReturnUsing(function ($message) {
				putenv("ES_CLI_ADD_COMMAND_HAPPENED={$message}");
			});

		$this->wpCliMock
			->shouldReceive('colorize')
			->andReturnUsing(function ($message) {
				return $message;
			});

		// Mock attachment function.
		Functions\when('get_attached_file')->justReturn('test.jpg');

		// Mock attachment function.
		Functions\when('wp_check_filetype')->justReturn([
			'ext' => 'jpg',
			'type' => 'image/jpeg',
		]);

		if (!defined('DAY_IN_SECONDS')) {
			define('DAY_IN_SECONDS', 3600);
		}

		Functions\when('is_admin')->justReturn(false);

		Functions\when('trailingslashit')->alias(function (string $string) {
			return rtrim($string, '/\\');
		});

		Functions\when('is_wp_version_compatible')->justReturn(true);

		Functions\when('wp_nonce_field')->justReturn('nonce');
	}

	protected function tear_down()
	{
		parent::tear_down();

		$this->deleteCliOutput();

		for ($i = 1; $i <= 10; $i++) {
			putenv("ES_SIDEAFFECT_{$i}");
		}

		putenv('ES_CLI_SUCCESS_HAPPENED');
		putenv('ES_CLI_ERROR_HAPPENED');
		putenv('ES_CLI_LOG_HAPPENED');
		putenv('ES_CLI_RUNCOMMAND_HAPPENED');

		global $esBlocks;
		$esBlocks = null;

		unset($this->wpCliMock);
	}

	/**
	 * Used for cleaning out the cliOutput created after every CLI test
	 *
	 * @param string $dir Directory to remove.
	 *
	 * @return void
	 */
	private function deleteCliOutput(string $dir = ''): void
	{
		$sep = \DIRECTORY_SEPARATOR;
		if (!$dir) {
			$dir = \dirname(__FILE__, 2) . "{$sep}cliOutput";
		}

		if (!\is_dir($dir)) {
			return;
		}

		$iterator = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

		foreach ($files as $file) {
			if ($file->isDir()) {
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}

		rmdir($dir);
	}
}
