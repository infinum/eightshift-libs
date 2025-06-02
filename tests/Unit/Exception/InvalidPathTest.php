<?php

/**
 * Tests for InvalidPath exception class.
 *
 * @package EightshiftLibs\Tests\Unit\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Exception;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Exception\InvalidPath;
use EightshiftLibs\Exception\GeneralExceptionInterface;
use InvalidArgumentException;
use Brain\Monkey\Functions;

/**
 * Test case for InvalidPath exception.
 *
 * @coversDefaultClass EightshiftLibs\Exception\InvalidPath
 */
class InvalidPathTest extends BaseTestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		// Mock WordPress functions
		Functions\when('esc_html__')->returnArg(1);
	}

	/**
	 * @covers ::missingDirectoryException
	 */
	public function testMissingDirectoryExceptionMessage(): void
	{
		$path = '/some/missing/directory';
		$exception = InvalidPath::missingDirectoryException($path);

		$this->assertInstanceOf(InvalidPath::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Failed to read the directory at "/some/missing/directory". Please check the implementation and try again.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingDirectoryException
	 */
	public function testMissingDirectoryExceptionWithEmptyPath(): void
	{
		$path = '';
		$exception = InvalidPath::missingDirectoryException($path);

		$expectedMessage = 'Failed to read the directory at "". Please check the implementation and try again.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingDirectoryException
	 */
	public function testMissingDirectoryExceptionWithSpecialCharacters(): void
	{
		$path = '/path/with/special-chars_123/äöü';
		$exception = InvalidPath::missingDirectoryException($path);

		$expectedMessage = 'Failed to read the directory at "/path/with/special-chars_123/äöü". Please check the implementation and try again.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingFileException
	 */
	public function testMissingFileExceptionMessage(): void
	{
		$path = '/some/missing/file.php';
		$exception = InvalidPath::missingFileException($path);

		$this->assertInstanceOf(InvalidPath::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Failed to open the file at "/some/missing/file.php". Please check the implementation and try again.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingFileException
	 */
	public function testMissingFileExceptionWithRelativePath(): void
	{
		$path = './config/settings.json';
		$exception = InvalidPath::missingFileException($path);

		$expectedMessage = 'Failed to open the file at "./config/settings.json". Please check the implementation and try again.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingFileWithExampleException
	 */
	public function testMissingFileWithExampleExceptionMessage(): void
	{
		$path = '/components/block';
		$example = 'manifest.json';
		$exception = InvalidPath::missingFileWithExampleException($path, $example);

		$this->assertInstanceOf(InvalidPath::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Failed to open the file at "/components/block". Expected file: "manifest.json".';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingFileWithExampleException
	 */
	public function testMissingFileWithExampleExceptionWithComplexPaths(): void
	{
		$path = '/var/www/html/wp-content/themes/mytheme/src/Blocks/CustomBlock';
		$example = 'block.json';
		$exception = InvalidPath::missingFileWithExampleException($path, $example);

		$expectedMessage = 'Failed to open the file at "/var/www/html/wp-content/themes/mytheme/src/Blocks/CustomBlock". Expected file: "block.json".';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingFileWithExampleException
	 */
	public function testMissingFileWithExampleExceptionWithEmptyValues(): void
	{
		$path = '';
		$example = '';
		$exception = InvalidPath::missingFileWithExampleException($path, $example);

		$expectedMessage = 'Failed to open the file at "". Expected file: "".';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::wrongOrNotAllowedParentPathException
	 */
	public function testWrongOrNotAllowedParentPathExceptionMessage(): void
	{
		$pathName = 'unauthorized-path';
		$allowed = 'components,blocks,variations';
		$exception = InvalidPath::wrongOrNotAllowedParentPathException($pathName, $allowed);

		$this->assertInstanceOf(InvalidPath::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Parent path is incorrect or not allowed. Path used: "unauthorized-path". Allowed path: "components,blocks,variations". Please check the implementation.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::wrongOrNotAllowedParentPathException
	 */
	public function testWrongOrNotAllowedParentPathExceptionWithComplexPaths(): void
	{
		$pathName = '/admin/sensitive/data';
		$allowed = '/public/assets,/public/uploads';
		$exception = InvalidPath::wrongOrNotAllowedParentPathException($pathName, $allowed);

		$expectedMessage = 'Parent path is incorrect or not allowed. Path used: "/admin/sensitive/data". Allowed path: "/public/assets,/public/uploads". Please check the implementation.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::wrongOrNotAllowedParentPathException
	 */
	public function testWrongOrNotAllowedParentPathExceptionWithEmptyValues(): void
	{
		$pathName = '';
		$allowed = '';
		$exception = InvalidPath::wrongOrNotAllowedParentPathException($pathName, $allowed);

		$expectedMessage = 'Parent path is incorrect or not allowed. Path used: "". Allowed path: "". Please check the implementation.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * Test that exception inherits from proper parent classes.
	 * @covers ::missingFileException
	 */
	public function testExceptionInheritance(): void
	{
		$exception = InvalidPath::missingFileException('/test/path');

		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		// Verify it's throwable
		$this->expectException(InvalidPath::class);
		throw $exception;
	}

	/**
	 * Test static factory methods return proper instance.
	 * @covers ::missingDirectoryException
	 * @covers ::missingFileException
	 * @covers ::missingFileWithExampleException
	 * @covers ::wrongOrNotAllowedParentPathException
	 */
	public function testStaticFactoryMethodsReturnProperInstance(): void
	{
		$missingDir = InvalidPath::missingDirectoryException('/test');
		$missingFile = InvalidPath::missingFileException('/test.php');
		$missingFileWithExample = InvalidPath::missingFileWithExampleException('/test', 'example.json');
		$wrongPath = InvalidPath::wrongOrNotAllowedParentPathException('wrong', 'correct');

		$this->assertInstanceOf(InvalidPath::class, $missingDir);
		$this->assertInstanceOf(InvalidPath::class, $missingFile);
		$this->assertInstanceOf(InvalidPath::class, $missingFileWithExample);
		$this->assertInstanceOf(InvalidPath::class, $wrongPath);

		// Each should be a different instance
		$this->assertNotSame($missingDir, $missingFile);
		$this->assertNotSame($missingFile, $missingFileWithExample);
		$this->assertNotSame($missingFileWithExample, $wrongPath);
	}

	/**
	 * Test that messages are properly formatted and don't contain raw placeholders.
	 * @covers ::missingFileException
	 */
	public function testMessageFormattingIsSafe(): void
	{
		$pathWithPercents = '/path/%s/with/%d/placeholders';
		$exception = InvalidPath::missingFileException($pathWithPercents);

		// Message should contain the path as-is, sprintf handles the placeholders properly
		$this->assertStringContainsString('/path/%s/with/%d/placeholders', $exception->getMessage());
		// The %s and %d should still be in the message since they are part of the path, not sprintf placeholders
		$this->assertStringContainsString('%s', $exception->getMessage());
		$this->assertStringContainsString('%d', $exception->getMessage());
	}
}
