<?php

/**
 * File that holds class for AcfMetaExample custom meta registration.
 *
 * @package EightshiftLibs\CustomMeta
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomMeta;

use EightshiftLibs\CustomMeta\AbstractAcfMeta;

/**
 * Class AcfMetaExample.
 */
class AcfMetaExample extends AbstractAcfMeta
{

	/**
	 * Render acf fields.
	 *
	 * @return void
	 */
	public function fields(): void
	{
		if (function_exists('acf_add_local_field_group')) {
			\acf_add_local_field_group(
				[
				]
			);
		}
	}
}
