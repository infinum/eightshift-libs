# Geolocation data

We use the [GeoIP2-php library](https://github.com/maxmind/GeoIP2-php) to get the user's location (country) based on their IP address. List of the countries is compared with the [DataHub list](https://datahub.io/core/country-list).

## GeoIP2-php library

We use the PHAR file implementation because there are some issues with the imposter plugin.
Every time a library has a new release we should make an update.

All releases are listed here: https://github.com/maxmind/GeoIP2-php/releases

Files used:

- GeoLite2-Country.mmdb - Database you can get here: https://www.maxmind.com/en/accounts/722673/geoip/downloads - GeoLite2 Country.
- geoip2.phar - executable file for reading the DB.

## Country list

We use DataHub list country list for providing the rest data that is used in the Block Editor. This list is used in the dropdown option for selecting the form country usage.

All releases are listed here: https://datahub.io/core/country-list

Files used:

- manifest.json
