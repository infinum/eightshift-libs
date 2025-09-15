<?php

/**
 * Class that registers WP-CLI command for Regenerate WebP Media.
 *
 * @package EightshiftLibs\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Media;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliRun;
use EightshiftLibs\Helpers\Helpers;
use Exception;
use WP_CLI;
use WP_Query;

/**
 * Class RegenerateWebPMediaCli
 */
class RegenerateWebPMediaCli extends AbstractCli
{
	/**
	 * Get WP-CLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliRun::COMMAND_NAME;
	}

	/**
	 * Get WP-CLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'regenerate-media';
	}

	/**
	 * Define default arguments.
	 *
	 * @return array<string, int|string|boolean>
	 */
	public function getDefaultArgs(): array
	{
		return [
			'quality' => '80',
			'ids' => '',
			'allowed_ext' => 'jpg,jpeg,png,bmp',
		];
	}

	/**
	 * Get WP-CLI command doc
	 *
	 * @return array<string, mixed>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Regenerate WebP media.',
			'synopsis' => [
				[
					'type' => 'assoc',
					'name' => 'quality',
					'description' => 'Quality of conversion 0-100. Default: 80',
					'optional' => true,
					'default' => $this->getDefaultArg('quality'),
				],
				[
					'type' => 'assoc',
					'name' => 'allowed_ext',
					'description' => 'Regenerate all supported media to WebP format.',
					'optional' => true,
					'default' => $this->getDefaultArg('allowed_ext'),
				],
				[
					'type' => 'assoc',
					'name' => 'ids',
					'description' => 'Ids of attachment separated by comma.',
					'optional' => true,
					'default' => $this->getDefaultArg('ids'),
				],
				[
					'type' => 'assoc',
					'name' => 'only_update_db',
					'description' => 'Only update the database, not the media files as assumed that the media files are already converted and locaded on S3 or other storage.',
					'optional' => true,
					'default' => $this->getDefaultArg('skip_skipped'),
				]
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used as a project command to generate WebP media from the provided data.

				## EXAMPLES
				# Regenerate all supported media to WebP format.
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()}

				# Regenerate only one attachment by ID.
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --ids='16911'

				# Regenerate multiple attachments by IDs.
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --ids='16911, 1692, 1302'

				# Regenerate media with different quality.
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --quality='90'

				# Only update the database, not the media files as assumed that the media files are already converted and locaded on S3 or other storage.
				$ wp {$this->commandParentName} {$this->getCommandParentName()} {$this->getCommandName()} --only_update_db='true'
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$assocArgs = $this->prepareArgs($assocArgs);

		$this->getIntroText($assocArgs);

		$quality = $this->getArg($assocArgs, 'quality');
		$ids = $this->getArg($assocArgs, 'ids');
		$allowedExt = $this->getArg($assocArgs, 'allowed_ext');
		$onlyUpdateDb = (bool) $this->getArg($assocArgs, 'only_update_db');

		$args = [];

		if ($ids) {
			$args['post__in'] = \explode(',', $ids);
		} else {
			$args['post_mime_type'] = \array_map(
				static function ($item) {
					return "image/{$item}";
				},
				\explode(',', \str_replace(' ', '', $allowedExt))
			);
		}

		$options = [
			'quality' => (int) $quality,
		];

		WP_CLI::log("Depending on the number of attachments, this process may take a while...");

		if ($onlyUpdateDb) {
			WP_CLI::log("Only updating the database, not the media files as assumed that the media files are already converted and locaded on S3 or other storage.");
		}

		WP_CLI::confirm("Are you sure you want to regenerate the media, this process is irreversible and will regenerate all the media files and update the database? Make sure you have a backup of your database!", $assocArgs);

		$this->generateMedia($options, $args, $onlyUpdateDb);
	}

	/**
	 * Generate media.
	 *
	 * @param array<mixed> $options Options from WP-CLI.
	 * @param array<mixed> $args Parameters from WP-CLI.
	 * @param bool $onlyUpdateDb Only update the database, not the media files as assumed that the media files are already converted and locaded on S3 or other storage.
	 *
	 * @return void
	 */
	private function generateMedia(array $options, array $args = [], bool $onlyUpdateDb = false): void
	{
		global $wpdb;

		$quality = $options['quality'];

		$defaultArgs = [
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'nopaging' => true,
			'perm' => 'readable',
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields' => 'ids',
		];

		$theQuery = new WP_Query(\array_merge($defaultArgs, $args));

		if (!$theQuery->posts) {
			WP_CLI::error("No attachments found with allowed extensions!");
		}

		$skipped = [];

		foreach ($theQuery->posts as $attachmentId) {
			$title = \get_the_title($attachmentId);

			WP_CLI::log('--------------------------------------------------');

			WP_CLI::log("Attachment: '{$attachmentId} - {$title}' is being processed...");

			try {
				$attachmentMetadata = \get_post_meta($attachmentId, '_wp_attachment_metadata', true);

				$mainAttachment = Helpers::convertMediaToWebPById($attachmentId, $quality, $onlyUpdateDb);

				if ($mainAttachment['originalUrl'] !== $mainAttachment['newUrl']) {
					// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update(
						$wpdb->posts,
						[
							'guid' => $mainAttachment['newUrl'],
							'post_mime_type' => $mainAttachment['newType'],
						],
						['ID' => $attachmentId]
					);
					// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

					WP_CLI::runcommand("search-replace '{$mainAttachment['originalUrl']}' '{$mainAttachment['newUrl']}'");

					$attachmentMetadata['file'] = $mainAttachment['dirnameRelative'] . '/' . $mainAttachment['newFileName'];
				}

				$outputSizesMeta = [];
				if ($sizes = \wp_get_attachment_metadata($attachmentId)['sizes'] ?? []) {
					foreach ($sizes as $sizeName => $sizeData) {
						$sizeFullPath = $mainAttachment['dirname'] . '/' . $sizeData['file'];
						$sizeAttachment = Helpers::convertMediaToWebPByPath($sizeFullPath, $quality, $onlyUpdateDb);

						$sizeData['file'] = $sizeAttachment['newFileName'];
						$sizeData['mime-type'] = $sizeAttachment['newType'];

						$outputSizesMeta[$sizeName] = $sizeData;
					}
				}

				if ($outputSizesMeta) {
					$attachmentMetadata['sizes'] = $outputSizesMeta;
				}

				\update_post_meta($attachmentId, '_wp_attachment_metadata', $attachmentMetadata);
				\update_post_meta($attachmentId, '_wp_attached_file', $mainAttachment['dirnameRelative'] . '/' . $mainAttachment['newFileName']);

				WP_CLI::success("Attachment: '{$attachmentId} - {$title}' converted to WebP format with new URL: {$mainAttachment['newUrl']}");
			} catch (Exception $e) {
				WP_CLI::warning("Attachment: '{$attachmentId}' failed with error: '{$e->getMessage()}'! Skipped.");
				$skipped[$attachmentId] = [
					'id' => $attachmentId,
					'title' => $title,
					'error' => $e->getMessage(),
				];
				continue;
			}

			WP_CLI::log('--------------------------------------------------');
		}

		if ($skipped) {
			WP_CLI::log('--------------------------------------------------');
			WP_CLI::log('Here is the list of skipped attachments:');
			WP_CLI::log('Skipped attachments count: ' . \count($skipped));
			$skippedCount = \wp_json_encode($skipped);
			WP_CLI::log($skippedCount);
		}

		\wp_reset_postdata();
	}
}
