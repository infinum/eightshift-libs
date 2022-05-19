<?php

/**
 * Class that registers WPCLI command for Use WebP Media.
 *
 * @package EightshiftLibs\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Media;

use EightshiftLibs\Cli\AbstractCli;
use WP_CLI;

/**
 * Class UseWebPMediaCli
 */
class UseWebPMediaCli extends AbstractCli
{
	/**
	 * Media WebP Trait.
	 */
	use MediaWebPTrait;

	/**
	 * Option name constant.
	 *
	 * @var string
	 */
	public const USE_WEBP_MEDIA_OPTION_NAME = 'es-use-webp-media';

	/**
	 * Get WPCLI command name
	 *
	 * @return string
	 */
	public function getCommandName(): string
	{
		return 'use_webp_media';
	}

	/**
	 * Get WPCLI command doc
	 *
	 * @return array<string, array<int, array<string, bool|string>>|string>
	 */
	public function getDoc(): array
	{
		return [
			'shortdesc' => 'Use WebP media.',
			'longdesc' => "## EXAMPLES \n
				# Toggle frontend media to use WebP format.
				$ wp boilerplate use_webp_media
			",
		];
	}

	/* @phpstan-ignore-next-line */
	public function __invoke(array $args, array $assocArgs)
	{
		if ((bool) \get_option(self::USE_WEBP_MEDIA_OPTION_NAME)) {
			\update_option(self::USE_WEBP_MEDIA_OPTION_NAME, false, true);

			WP_CLI::success('WebP Media use disabled.');
		} else {
			\update_option(self::USE_WEBP_MEDIA_OPTION_NAME, true, true);

			WP_CLI::success('WebP Media use enabled.');
		}
	}
}
