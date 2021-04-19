<?php

declare(strict_types=1);

namespace Tests\Datasets\Autowiring\Services;

use EightshiftLibs\Services\ServiceInterface;

class ServiceWithPrimitiveDepHasDefault implements ServiceInterface
{
	public function __construct(string $something = 'default') {
		$this->something = $something;
	}

	/**
	 * Registers service.
   *
	 * @return void
	 */
	public function register(): void
	{
	}
}
