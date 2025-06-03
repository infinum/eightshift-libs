# EightshiftLibs Testing

This directory contains unit tests for the EightshiftLibs project.

## Overview

The testing setup uses:

- **PHPUnit 11**: Main testing framework
- **Brain\Monkey**: WordPress function mocking
- **Mockery**: Advanced mocking capabilities
- **PHP 8.3+**: Minimum required version

## Running Tests

### Install Dependencies

First, make sure you have installed the dev dependencies:

```bash
composer install
```

### Run All Tests

```bash
# Run the full test suite (standards, types, and unit tests)
composer test

# Run only unit tests
composer test:unit

# Run unit tests with coverage report
composer test:unit-coverage
```

### Run Specific Tests

```bash
# Run a specific test file
./vendor/bin/phpunit tests/Unit/Helpers/GeneralTraitTest.php

# Run tests with a specific filter
./vendor/bin/phpunit --filter testIsValidXml

# Run tests in a specific directory
./vendor/bin/phpunit tests/Unit/Helpers/
```

## Test Structure

```
tests/
├── README.md                          # This file
├── bootstrap.php                      # Test environment setup
├── BaseTestCase.php                   # Base class for all tests
└── Unit/                              # Unit tests directory
    └── Helpers/                       # Tests for Helper classes
        └── GeneralTraitTest.php       # Example test file
```

## Writing Tests

### Basic Test Example

```php
<?php

namespace EightshiftLibs\Tests\Unit\YourNamespace;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\YourNamespace\YourClass;

class YourClassTest extends BaseTestCase
{
    public function testSomething(): void
    {
        $instance = new YourClass();
        $result = $instance->someMethod();

        $this->assertEquals('expected', $result);
    }
}
```

### Testing WordPress Functions

The setup includes Brain\Monkey for mocking WordPress functions:

```php
use Brain\Monkey\Functions;

public function testWordPressFunctionCall(): void
{
    // Mock WordPress function
    Functions\expect('get_option')
        ->once()
        ->with('some_option')
        ->andReturn('mocked_value');

    // Your test code here
    $result = $this->someMethodThatCallsGetOption();

    $this->assertEquals('expected_result', $result);
}
```

### Data Providers

Use data providers for testing multiple scenarios:

```php
/**
 * @dataProvider validInputProvider
 */
public function testWithValidInput(string $input, string $expected): void
{
    $result = SomeClass::process($input);
    $this->assertEquals($expected, $result);
}

public static function validInputProvider(): array
{
    return [
        'case 1' => ['input1', 'expected1'],
        'case 2' => ['input2', 'expected2'],
    ];
}
```

## Coverage Reports

Coverage reports are generated in the `coverage/` directory when running:

```bash
composer test:unit-coverage
```

Open `coverage/index.html` in your browser to view the detailed coverage report.

## Configuration

- **phpunit.xml.dist**: Main PHPUnit configuration
- **tests/bootstrap.php**: Test environment initialization
- **composer.json**: Test dependencies and scripts

## Best Practices

1. **One test method per scenario**: Keep test methods focused and small
2. **Descriptive test names**: Use names that explain what is being tested
3. **Data providers**: Use for testing multiple inputs/outputs
4. **Mocking**: Mock external dependencies and WordPress functions
5. **Assertions**: Use specific assertions (`assertEquals` vs `assertTrue`)
6. **Coverage**: Aim for high test coverage of critical code paths

## Troubleshooting

### Common Issues

1. **Class not found**: Make sure autoloading is set up correctly in `composer.json`
2. **WordPress functions undefined**: Use Brain\Monkey to mock WordPress functions
3. **Tests slow**: Consider mocking heavy operations instead of running them

### Debug Tests

```bash
# Run tests with verbose output
./vendor/bin/phpunit --verbose

# Run tests with debug information
./vendor/bin/phpunit --debug
```
