<?php

/**
 * File holding FirstMockServiceClass class
 *
 * @package Tests\Unit\data\src
 */

declare(strict_types=1);

namespace Tests\Unit\data\src;

use EightshiftLibs\Services\ServiceInterface;

/**
 * FirstMockServiceClass class
 *
 * @package Tests\Unit\data\src
 */
class SecondMockServiceClass implements ServiceInterface
{

	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
	}
}
