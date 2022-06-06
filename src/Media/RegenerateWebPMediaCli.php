<?php

/**
 * Class that registers WPCLI command for Regenerate WebP Media.
 *
 * @package EightshiftLibs\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Media;

use EightshiftLibs\Cli\AbstractCli;
use EightshiftLibs\Cli\ParentGroups\CliWebp;
use WP_CLI;
use WP_Query;

/**
 * Class RegenerateWebPMediaCli
 */
class RegenerateWebPMediaCli extends AbstractCli
{
	/**
	 * Media WebP Trait.
	 */
	use MediaWebPTrait;

	/**
	 * Get WPCLI command parent name
	 *
	 * @return string
	 */
	public function getCommandParentName(): string
	{
		return CliWebp::COMMAND_NAME;
	}

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'regenerate_media';
	}

	/**
	 * Get WPCLI command doc
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
					'name' => 'action',
					'description' => 'Action to use "generate" or "delete". Default: generate',
					'optional' => true,
					'options' => [
						'generate',
						'delete',
					],
				],
				[
					'type' => 'assoc',
					'name' => 'quality',
					'description' => 'Quality of conversion 0-100. Default: 80',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'ids',
					'description' => 'Ids of attachment separated by comma.',
					'optional' => true,
				],
				[
					'type' => 'assoc',
					'name' => 'force',
					'description' => 'Force generation no matter if the file exists. Default: false',
					'optional' => true,
					'options' => [
						'true',
						'false',
					],
				],
			],
			'longdesc' => $this->prepareLongDesc("
				## USAGE

				Used as a project command to generate WebP meda from the provided data.

				## EXAMPLES
				# Regenerate all supported media to WebP format.
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()}

				# Regenerate only one attachment by ID.
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --ids='16911'

				# Regenerate multiple attachments by IDs.
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --ids='16911, 1692, 1302'

				# Force regenerate attachments no matter if they all-ready exist.
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --force='true'

				# Regenerate media with diffferent quality.
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --quality='90'

				# Delete all WebP media formats.
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --action='delete'

				# Delete only one WebP attachment by ID.
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --ids='16911' --action='delete'

				# Delete multiple WebP attachments by ID.
				$ wp boilerplate {$this->getCommandParentName()} {$this->getCommandName()} --ids='16911, 1692, 1302' --action='delete'
			"),
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		$quality = $assocArgs['quality'] ?? '80';
		$action = $assocArgs['action'] ?? 'generate';
		$ids = $assocArgs['ids'] ?? '';
		$force = isset($assocArgs['force']) ?: 'false'; // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found

		$args = [];

		if ($ids) {
			$args['post__in'] = \explode(',', $ids);
		}

		$options = [
			'quality' => (int) $quality,
			'force' => (bool) $force,
		];

		switch ($action) {
			case 'delete':
				$this->deleteMedia($args);
				break;
			default:
				$this->generateMedia($options, $args);
				break;
		}
	}

	/**
	 * Get Query args
	 *
	 * @param array<string> $args Args to merge on the original array.
	 *
	 * @return array<string>
	 */
	private function getQueryArgs(array $args): array
	{
		$defaultArgs = [
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'nopaging' => true,
			'perm' => 'readable',
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields' => 'ids',
			'post_mime_type' => \array_map(
				static function ($item) {
					return "image/{$item}";
				},
				AbstractMedia::WEBP_ALLOWED_EXT
			),
		];

		return \array_merge($defaultArgs, $args);
	}

	/**
	 * Generate media.
	 *
	 * @param array<string, mixed> $options Options from WP-CLI.
	 * @param array<string> $args Parameters from WP-CLI.
	 *
	 * @return void
	 */
	private function generateMedia(array $options, array $args = []): void
	{
		$quality = $options['quality'];
		$force = $options['force'];

		$theQuery = new WP_Query($this->getQueryArgs($args));

		if (!$theQuery->posts) {
			WP_CLI::error("No attachments found!");
		}

		foreach ($theQuery->posts as $id) {
			$title = \get_the_title($id);

			$original = $this->generateWebPMediaOriginal($id, $quality, $force);

			WP_CLI::log("Attachment '{$title}' conversion to WebP status: {$id}");

			if ($original) {
				WP_CLI::success("Attachment original converted!");
				WP_CLI::log($original);
			} else {
				WP_CLI::warning("Attachment original not converted - allready exists!");
			}

			$sizes = $this->generateWebPMediaAllSizes($id, $quality, $force);

			if ($sizes) {
				foreach ($sizes as $size => $sizeValue) {
					WP_CLI::success("Attachment size {$size} converted!");
					WP_CLI::log($sizeValue);
				}
			} else {
				WP_CLI::warning("Attachment sizes not converted - allready exists or media is to small for additional sizes!");
			}

			WP_CLI::log('--------------------------------------------------');
		}

		\wp_reset_postdata();
	}

	/**
	 * Delete media.
	 *
	 * @param array<string> $args Parameters from WP-CLI.
	 *
	 * @return void
	 */
	private function deleteMedia(array $args = []): void
	{
		$theQuery = new WP_Query($this->getQueryArgs($args));

		if (!$theQuery->posts) {
			WP_CLI::error("No attachments found!");
		}

		foreach ($theQuery->posts as $id) {
			$title = \get_the_title($id);

			$original = $this->deleteWebPMediaOriginal($id);

			WP_CLI::log("Attachment '{$title}' deleting WebP status: {$id}");

			if ($original) {
				WP_CLI::success("Attachment original deleted!");
				WP_CLI::log($original);
			} else {
				WP_CLI::warning("Attachment original not deleted - allready deleted or missing!");
			}

			$sizes = $this->deleteWebPMediaAllSizes($id);

			if ($sizes) {
				foreach ($sizes as $size => $sizeValue) {
					WP_CLI::success("Attachment size {$size} deleted!");
					WP_CLI::log($sizeValue);
				}
			} else {
				WP_CLI::warning("Attachment sizes not deleted - allready deleted or missing!");
			}

			WP_CLI::log('--------------------------------------------------');
		}

		\wp_reset_postdata();
	}
}
