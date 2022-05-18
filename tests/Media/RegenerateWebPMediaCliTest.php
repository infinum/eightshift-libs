<?php

namespace Tests\Unit\Media;

use EightshiftLibs\Media\RegenerateWebPMediaCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

/**
 * Mock before tests.
 */
beforeEach(function () {
	setBeforeEach();

	$this->regenerateWebPMediaCli = new RegenerateWebPMediaCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	setAfterEach();
});

test('Check if CLI command documentation is correct.', function () {
	$mock = $this->regenerateWebPMediaCli;

	$mock = $mock->getDoc();

	expect($mock)
		->toBeArray($mock)
		->toMatchArray([
			'shortdesc' => 'Regenerate WebP media.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'action',
					'description' => 'Action to use "generate" or "delete". Default: generate',
					'optional' => true
				],
				[
					'type' => 'assoc',
					'name' => 'quality',
					'description' => 'Quality of conversion 0-100. Default: 80',
					'optional' => true
				],
				[
					'type' => 'assoc',
					'name' => 'ids',
					'description' => 'Ids of attachment separated by comma.',
					'optional' => true
				],
				[
					'type' => 'assoc',
					'name' => 'force',
					'description' => 'Force generation no matter if the file exists. Default: false',
					'optional' => true
				],
			],
			'longdesc' => "## EXAMPLES \n
				# Regenerate all supported media to WebP format.
				$ wp boilerplate regenerate_webp_media

				# Regenerate only one attachment by ID.
				$ wp boilerplate regenerate_webp_media --ids='16911'

				# Regenerate multiple attachments by IDs.
				$ wp boilerplate regenerate_webp_media --ids='16911, 1692, 1302'

				# Force regenerate attachments no matter if they all-ready exist.
				$ wp boilerplate regenerate_webp_media --force='true'

				# Regenerate media with diffferent quality.
				$ wp boilerplate regenerate_webp_media --quality='90'

				# Delete all WebP media formats.
				$ wp boilerplate regenerate_webp_media --action='delete'

				# Delete only one WebP attachment by ID.
				$ wp boilerplate regenerate_webp_media --ids='16911' --action='delete'

				# Delete multiple WebP attachments by ID.
				$ wp boilerplate regenerate_webp_media --ids='16911, 1692, 1302' --action='delete'
			",
		]);
});
