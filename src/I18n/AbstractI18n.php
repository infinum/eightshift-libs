<?php
/**
 * The Language specific functionality.
 *
 * @package EightshiftLibs\I18n
 */

declare( strict_types=1 );

namespace EightshiftLibs\I18n;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Class i18n
 *
 * This class handles theme or admin languages.
 */
abstract class AbstractI18n implements ServiceInterface {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function loadThemeTextdomain() {
		\load_theme_textdomain(
			$this->getTextdomainName(),
			$this->getTranslationFilePath()
		);
	}

	/**
	 * Text domain. Unique identifier for retrieving translated strings.
	 *
	 * @return string
	 */
	abstract public function getTextdomainName() : string;

	/**
	 * Path to the directory containing the .mo file.
	 *
	 * @return string
	 */
	abstract public function getTranslationFilePath() : string;
}
