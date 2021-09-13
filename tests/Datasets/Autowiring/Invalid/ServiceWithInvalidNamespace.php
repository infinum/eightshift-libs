<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Invalid;

use EightshiftLibs\Services\ServiceInterface;

if ( ! class_exists( ServiceWithInvalidNamespace::class ) ) {
	class ServiceWithInvalidNamespace implements ServiceInterface
	{
		/**
		 * Registers service.
		 *
		 * @return void
		 */
		public function register(): void
		{
		}
	}
}
