# Geolocation data

We use the [GeoIP2-php library](https://github.com/maxmind/GeoIP2-php) to get the user's location (country) based on their IP address. List of the countries is compared with the [DataHub list](https://datahub.io/core/country-list).

## GeoIP2-php library

We use the PHAR file implementation because there are some issues with the imposter plugin.
Every time a library has a new release we should make an update.

All releases are listed here: https://github.com/maxmind/GeoIP2-php/releases

Files used:
* GeoLite2-Country.mmdb - Database you can get here: https://www.maxmind.com/en/accounts/722673/geoip/downloads - GeoLite2 Country.
* geoip2.phar - executable file for reading the DB.

## Country list

We use DataHub list country list for providing the rest data that is used in the Block Editor. This list is used in the dropdown option for selecting the form country usage.

All releases are listed here: https://datahub.io/core/country-list

Files used:
* manifest.json

# WP-ROCKET Plugin usage

When geolocation is used in combination with WP-Rocket plugin it will not work by default because the cookie is set after the page is loaded and at that point it is too late to provide content.

WP-Rocket plugin provides options/hooks to fix this. By using hook `rocket_advanced_cache_file` we are able to inject our custom function in the page generation process inside the `wp-content/advanced-cache.php` file. By modifying this file we can detect users geolocation from the IP and set the cookie manually before the page is loaded. This way WP-Rocket can detect that cookie and provide the necessary cache. Below is the process of setting it:

## Set dynamic cookie:

You must provide the list of cookies that will be used as dynamic cookies to generate cached versions.

**Filter:**
```php
\add_filter('rocket_cache_dynamic_cookies', [$this, 'dynamicCookiesList']);
```

**Callback:**
```php
/**
 * List all dynamic cookies that will create new cached version.
 *
 * @param array<string, mixed> $items Items from the admin.
 *
 * @return array<int|string, mixed>
 */
public function dynamicCookiesList(array $items): array
{
	$items[] = 'esForms-country';

	return $items;
}
```

## Set custom function to advanced-cached.php file.

By providing a custom function in the advanced-cached.php file, you will be able to detect geolocation and set cookies before the cached version is provided.

For `$esFormsPath` variable you must provide the absolute path to the `geolocationDetect.php` file in vendor.
The provided example works if you have Eightshift-forms in your project.

**Filter:**
```php
\add_filter('rocket_advanced_cache_file', [$this, 'addNginxAdvanceCacheRules']);
```

**Callback:**
```php
/**
 * Add geolocation function in advance-cache.php config file on plugin activation used only with Nginx.
 *
 * @param string $content Original file output.
 */
public function addNginxAdvanceCacheRules( string $content ) : string {
	$position = \strpos($content, '$rocket_config_class');

	// This part is string on purpose.
	$cors_function = '
	$esFormsPath = ABSPATH . "wp-content/plugins/eightshift-forms/src/Geolocation/geolocationDetect.php";
	if (file_exists($esFormsPath)) {
		require_once $esFormsPath;
	};';

	return \substr_replace($content, $cors_function, $position, 0);
}
```

## Disable `setLocationCookie` filter in your project.

If you have installed Geolocation.php class in your project and you have this filter set, make sure to disable it, or provide the check to be disabled when WP-Rocket cache plugin is active.

**Example:**
```php
if (!\is_plugin_active('wp-rocket/wp-rocket.php')) {
	\add_filter('init', [$this, 'setLocationCookie']);
}
```
