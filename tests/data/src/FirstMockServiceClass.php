<?php

/**
 * File holding FirstMockServiceClass class
 *
 * @package Tests\data\src
 */

declare(strict_types=1);

namespace Tests\data\src;

use EightshiftLibs\Services\ServiceInterface;

/**
 * FirstMockServiceClass class
 *
 * @package Tests\data\src
 */
class FirstMockServiceClass implements ServiceInterface
{

	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
	}
}
