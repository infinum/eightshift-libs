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
					'name' => 'ids',
					'description' => 'Ids of attachment separated by comma.',
					'optional' => true,
					'default' => $this->getDefaultArg('ids'),
				],
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

		$args = [];

		if ($ids) {
			$args['post__in'] = \explode(',', $ids);
		}

		$options = [
			'quality' => (int) $quality,
		];

		$this->generateMedia($options, $args);
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
			// 'post_mime_type' => \array_map(
			// 	static function ($item) {
			// 		return "image/{$item}";
			// 	},
			// 	// AbstractMedia::WEBP_ALLOWED_EXT
			// ),
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

		$theQuery = new WP_Query($this->getQueryArgs($args));

		if (!$theQuery->posts) {
			WP_CLI::error("No attachments found!");
		}

		foreach ($theQuery->posts as $id) {
			$title = \get_the_title($id);

			$original = Helpers::convertMediaToWebPById($id, $quality);

			WP_CLI::log("Attachment '{$title}' conversion to WebP status: {$id}");

			if ($original) {
				WP_CLI::success("Attachment original converted!");
				WP_CLI::log($original);
			} else {
				WP_CLI::warning("Attachment original not converted - already exists!");
			}

			WP_CLI::log('--------------------------------------------------');
		}

		\wp_reset_postdata();
	}
}
