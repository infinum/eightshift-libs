<?php

/**
 * File that holds class for AcfMetaExample custom meta registration.
 *
 * @package %g_namespace%\CustomMeta
 */

declare(strict_types=1);

namespace %g_namespace%\CustomMeta;

use %g_use_libs%\CustomMeta\AbstractAcfMeta;

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
		if (\function_exists('acf_add_local_field_group')) {
			\acf_add_local_field_group([]);
		}
	}
}
