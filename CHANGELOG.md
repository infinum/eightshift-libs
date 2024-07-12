# Change Log for the Eightshift Libs
All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](https://semver.org/) and [Keep a CHANGELOG](https://keepachangelog.com/).

## [9.0.0]

### Changed
- WP-CLI command `reusable-header-footer` is renamed to `patterns-header-footer`.
- `Reusable blocks` admin menu is now named `Patterns` when moved to project.
- WP-CLI command `admin-reusable-blocks-menu` is renamed to `admin-patterns-menu`.
- Plugin now has WP-CLI prefix as `boilerplate-plugin`.
- WP-CLI command `theme-options` is renamed to `theme-options-acf`.

### Added
- WP-CLI command `admin-menu` now supports `view_component` prop.
- WP-CLI command `admin-sub-menu` now supports `view_component` prop.
- WP-CLI command `admin-theme-options-menu`
- WP-CLI commands can now detect if they are used in the standard or Tailwind setup.
- WP-CLI global param `g_frontend_libs_type`.
- WP-CLI parent command name `ìnit-setup`.
- WP-CLI command `init-setup theme`.
- WP-CLI command `init-setup theme-clean`.
- WP-CLI command `init-setup plugin`.
- WP-CLI command `init-setup plugin-clean`.
- Enqueue function to deregister all WP default styles that should not be there.
- Tailwind trait for all Tailwind helpers used in the new setup.
- New setups for Tailwind setup for plugin and theme.
- WP-CLI command `theme-options`.

### Fixed
- Admin assets dependency is needed to support the Tailwind setup.

## [8.0.7]

### Fixed
- Countries get list pulled the data from the wrong manifest cache file.

## [8.0.6]

### Fixed
- default attributes render will not throw error if attribute key is missing in component manifest.

## [8.0.5]

### Fixed
- optimization wrong service class use constant.

## [8.0.4]

### Fixed
- broken media upload after adding svg filters.
- wrong optimization service class namespace.

## [8.0.3]

### Added
- Media support for svg and json.
- Optimization service class for better performance.

### Fixed
- Phpstan config fix for theme and plugin setup
- Project name variable will now be ucfirst on setup.
- Removed .git folder after the setup.

## [8.0.2]

### Fixed
- Installation of theme and plugin eightshift-frontend-libs repo.

## [8.0.1]

### Changed
- Package and composer versions.

## [8.0.0]

### Changed
- Minimum PHP version is now 8.2.
- Complete refactor of the block registration process for faster and more efficient block registration.
- Custom post type and taxonomy registration labels usage is now more consistent and simplified.
- All enqueue methods now supports new scripts args introduced in WP 6.3.
- All enqueue methods now implements `manifestCache`interface for fetching manifest data.
- All AbstractGeolocation method now implements `manifestCache`interface for fetching manifest data.

### Added
- Internal caching in WordPress transients for faster manifest parsing and block registration.
- Blocks render method now uses the Helpers::render method for better performance and code readability.
- New ManifestCache CLI command with example.
- New CLI commands for config theme and plugin.
- - `Exception/InvalidBlock.php` method: `missingItemException`.
- `src/Exception/InvalidManifest.php` methods: `missingManifestKeyException`, `emptyOrErrorManifestException`, `notAllowedManifestPathException`, `notAllowedManifestPathItemException`, `missingCacheTopItemException`, `missingCacheSubItemException`.
- `src/Exception/InvalidPath.php` method: `missingDirectoryException`, `missingFileException`, `missingFileWithExampleException`, `wrongOrNotAllowedParentPathException`.
- New `src/Helpers/ApiTrait.php` helper for better API handling.
- New `recursiveArrayFind` helper for finding items in multidimensional arrays recursively.
- New casing helpers `camelToSnakeCase`, `kebabToSnakeCase`.
- New `getCurrentUrl` helper for getting the current URL with query parameters.
- New `cleanUrlParams` helper for cleaning URL parameters.
- New project info helpers: `getPluginVersion`, `getPluginName`, `getPluginTextDomain`, `getThemeVersion`, `getThemeName`, `getThemeTextDomain`, `getPluginDetails`.
- New constants for API handing in the `src/Rest/Routes/AbstractRoute.php`.

### Fixed
- Typo in block enqueue method for `getBlockFrontentScriptHandle` to `getBlockFrontendScriptHandle` method.

### Removed
- `src/Blocks/AbstractBlocks.php` method `renderWrapperView` is removed.
- `Config/AbstractConfigData.php` is removed as it is no longer needed. Use helpers instead.
- `Config/ConfigDataInterface.php` is removed as it is no longer needed.
- `LabelGeneratorTrait` is removed as it is no longer needed.
- All Enqueue methods no longer use `manifest` interface.
- `Exception/ComponentException.php` is removed as it is no longer needed.
- `Exception/FailedToLoadView.php` is removed as it is no longer needed.
- `Exception/FileMissing.php` is removed as it is no longer needed.
- `src/Exception/InvalidCallback.php` is removed as it is no longer needed.
- `src/Exception/InvalidNouns.php` is removed as it is no longer needed.
- `src/Exception/InvalidService.php` is removed as it is no longer needed.
- `src/Exception/PluginActivationFailure.php` is removed as it is no longer needed.
- `Exception/InvalidBlock.php` methods: `missingBlocksException`, `missingComponentsException`, `missingNameException`, `missingComponentNameException`, `missingVariationNameException`, `missingViewException`, `missingRenderViewException`, `missingSettingsManifestException`, `missingWrapperManifestException`, `missingComponentManifestException`, `missingWrapperViewException`, `missingNamespaceException`, `missingSettingsKeyException`, `wrongFunctionUsedException`, `missingFileException`, is removed as it is no longer needed.
- `src/Exception/InvalidManifest.php` method: `missingManifestItemException`.
- `src/Exception/InvalidPath.php` method: `fromUri`.
- `outputCssVariablesGlobal` method no longer requires parameters.
- `outputCssVariables` method is not longer supporting `globalManifest` parameter.
- all methods from `src/Helpers/ErrorLoggerTrait.php` trait are removed.
- All manifest classes are removed, `src/Manifest/AbstractManifest.php`, `src/Manifest/ManifestExample.php`, `src/Manifest/ManifestInterface.php`.

### Deprecated
- `Helpers>Components` class is deprecated and will be removed in the next major release. Use `Helpers>Blocks` class instead.
- `Helpers>Components>renderPartial` method is deprecated and will be removed in the next major release. Use `Helpers>Helpers>render` method instead.
- `Helpers>Components>getManifest` method is deprecated and will be removed in the next major release. Use `Helpers>Helpers>getManifestByDir` method instead.

## [7.1.2] - 2024-01-15

### Changed
- Reverting the previous release.

## [7.1.1] - 2024-01-15

### Removed
- Removed enqueuing dependency for block editor only styles.

## [7.1.0] - 2023-11-22

### Removed
- Previously implemented private folder restrictions on render methods.

## [7.0.1] - 2023-11-10

### Fixed
- Re-enqueues lodash for WP 6.4 version.

## [7.0.0] - 2023-11-06

This is a major release that includes PHP8+ support. We tested it on the PHP 8.2.12 version.

### Changed
- Config definition of version and name.

### Updated
- Composer packages.
- Composer command names.

### Fixed
- Stan and Lint issues.
- Broken `db import` command with the wrong setup.json path.

### Added
- Missing type hinting.
- New command to change version number.
- Better copy for `checkAttr`` helper if key is missing.
- Every enqueue script now has `is<>Used` method that is by default true. 

### Removed
- Blocks "old" version functions.

## [6.5.7] - 2023-10-24

### Changed
- `render` and `renderPartial` methods are now no longer able to call the php files outside of themes/plugin folders. This is a security measure to prevent the execution of arbitrary code.
- `render` method can now call `wrapper` as a component name.

## [6.5.6] - 2023-07-17

### Fixed
- Assets dependency function now returns array<int, string> instead of array<string, mixed>.

## [6.5.5] - 2023-07-07

### Fixed
- if you're using the WPML plugin, webP images won't be deleted unless all the attachments are removed from the admin interface.

## [6.5.4] - 2023-07-04

### Fixed
- you no longer need to add the "blockSsr" attribute when loading a block via server-side rendering. It will automatically utilize global CSS variables and provide the necessary styles.

## [6.5.3] - 2023-06-29

### Fixed
- column media wrong filter name.
- deprecation notice in the CLI.

## [6.5.2] - 2023-06-20

### Changed
- updating random Id generator to output 8 rather than 32 characters.

## [6.5.1] - 2023-06-19

### Changed
- wrong command name was changed from `plugin_manage` to `plugin-manage`.
- webp helper now has option to add allowed item so you can limit image types you don't want to use webP.

## [6.5.0] - 2023-06-13

### Changed
- added new CLI command for initializing reusable header/footer (settings page for picking blocks, and default blocks)
- added new helper to render nice alert "windows"
- revamped some CLI command outputs and error outputs

## [6.4.0] - 2023-02-21

### Changed
- Changed the default args for init blocks and use block variation CLI commands, as the button-block variation will not exist anymore in FE libs v8.0.

## [6.3.2] - 2023-01-27

### Added
- Forms block to allowed blocks list.

## [6.3.1] - 2022-12-23

### Fixed
- WP-CLI command name for pattern creating.

### Removed
- unnecessary escape in the geolocation detect.

## [6.3.0] - 2022-10-04

### Added
- new WP-CLI command for plugins (install/update/delete) from global `setup.json`.
- new function for providing cookie expiration time.
- new file providing geolocation with WP-Rocket cache.
- WP-CLI will now look for an additional key when moving block/components. This key is used to list all inner block dependencies.
- storybook install WP-CLI description details.

### Fixed
- WP-CLI log output.
- config example wrong version output.
- config CLI wrong command version number.
- broken tests for geolocation.
- const public/private position in components.
- docs link.

### Removed
- old WP-CLI command for updating plugins from global `setup.json`.

## [6.2.0] - 2022-09-06

### Updated
- Geolocation method `getGeolocation` is now public.
- Geolocation method `getGeolocation` better error handling while files are missing.

## [6.1.0] - 2022-08-01

### Updated
- Manifest fetching in tests bug.
- All methods to be PHP8 safe in case of arguments naming.
- Allow all blocks that was broken from the last deploy.

### Added
- New tests.

## [6.0.0] - 2022-07-11

### Removed
- Bin cli command.
- Ei-exclude cli command.
- Build cli command.

### Changed
- Updated packages.
- Updated tests.
- Refactored all cli commands for better usability and output.
- Most of the cli command names and prefixes.
- Internal way of moving blocks and files to the project to be faster.

### Added
- Descriptions for all cli commands with examples and links to repos.
- New service class for Geolocation.
- New service class for WebP and helpers.

## [5.1.0] - 2022-05-09

### Added:
- new WP-CLI command for reusable blocks

### Fixed:
- Css variables combine output small fix if no blocks are added to dom.
- Windows compatibility fixes.

## [5.0.2] - 2022-04-21

### Fixed
- Abstract blocks correct order of store registration.
## [5.0.1] - 2022-04-20

### Fixed
- WP-CLI command for only necessary blocks and components in the init phase.

## [5.0.0] - 2022-04-19

- Major braking changes do to updates on css variables, and helpers and updating min PHP version to 7.4.
- Full change log can be checked on Github [releases](https://github.com/infinum/eightshift-libs/releases/tag/5.0.0).

## [4.1.0] - 2022-02-03

### Added
- Eightshift-forms as default allow block types.
- New WP-CLI command to create custom WP-CLI command. Example: `wp boilerplate create_cli_command`.
- New attribute to `outputCssVariables` called `$customSelector`.
- DI container caching on production or staging.
- Option to remove default paragraph block placeholder from the frontend if the content is empty.
- Custom post type revisions as default.
- Option to override default block class prefix in the blocks global settings manifest.

### Fixed
- Changed directory separator from `/` to `\DIRECTORY_SEPARATOR` constant to be able to work cross-platform.
- Various fixes and improvements.

### Changed
- Limit enqueue admin only to admin area but not to the block editor.

## [4.0.0] - 2021-08-16

### Changed
- Major braking changes do to updates on css variables, and helpers.

## [3.1.0] - 2022-30-01

### Added
- Two methods that will make libs work with WP 5.8+ and older versions.

## [3.0.8] - 2021-10-14

### Fixed
- Make CLI commands cross-os compatible

## [3.0.7] - 2021-07-14

### Changes
- Minor improvements

## [3.0.6] - 2021-03-17

### Fixed
- Fixes the autowiring issue (exclude the non-service classes).

## [3.0.5] - 2021-02-04

### Changed
- Updates on readme.

### Added
- New filter in Block.php to show custom post type blocks in sidebar.
- New WP-CLI command for project readme.

## [3.0.4] - 2021-01-15

### Changed
- ModifyAdminAppearance class fixing develop to development and local.
- wp-config-project fixing global variable.
- Fixing project config cli.

## [3.0.3] - 2021-01-15

### Removed
- Removed package-lock.json
- Removed composer.lock
- Removed version number from composer.json

## [3.0.2] - 2021-01-13

### Removed
- Removed env from the config CLI.

## [3.0.1] - 2021-01-13

### Fixed
- Issues with -env parameter on `wp boilerplate setup_project` command.
- Wrong Global variable name in Shortcodes.
- Small improvements.

### Added
- I18n - added hook priority to fix bug on loading languages.

### Removed
- Config - removed `getProjectEnv` method.
- Config - removed `getConfig` method.

### Changed
- Switched from custom env variable to native WP_ENVIRONMENT_TYPE.
- ModifyAdminAppearance - implementing new env variable.

## [3.0.0] - 2021-01-05
MAYOR BREAKING CHANGES

- You should not try to update from version 2 to 3 because they are not compatible.

## [2.5.0] - 2020-08-10

### Changed
- This will enable old packages who are on libs < 3 to update to WP 5.8 due to the breaking block hooks change.

## [2.4.1] - 2020-07-10

### Changed
- `class-bem-menu-walker.php` - removed declaration of db_fields.

### Added
- Setup script for CI deployment for core and plugins

## [2.4.0] - 2020-07-08

### Removed
- `class-blocks.php` - removed normal blocks view and made wrapper view as default.
- `class-blocks.php` - removed hasWrapper key.

### Changed
- `class-components.php` - changes on `responsive_selectors` method because of a bug in rendering bool.

## [2.3.0] - 2020-05-25

### Added
- Github actions.
- Phpstan.

### Changed
- `class-blocks.php` returns all colors from settings.
- corrections from Phpstan.
- Made `get_service_classes_prepared_array()` more flat.
- `composer.json` updated packages, fixing scripts names.

## [2.2.2] - 2020-05-15

### Added 
- New helper method for making responsive selectors.

## [2.2.1] - 2020-05-13

### Removed
- PHP version 7.0 from Travis build.

### Added 
- Check for block manifest validation.

## [2.2.0] - 2020-05-06

### Changed
* Removed config dependency from the Asset classes and exposed config through Manifest. 
* `editor-color-palette` - Add theme support for editor color palette built from global manifest.json.

## [2.1.1] - 2020-03-05

### Fixed
* Missing enqueue method to load scripts in footer.
* Wrong namespace in components helpers.

## [2.1.0] - 2020-03-04

### Added
* build_di_container() method to class Main.
* class-components.php helper class for easier component rendering
* ability to wrap components with parent class on render
* class-main.php - Added build_di_container() method.
* class-blocks.php - Added custom filter `block-attributes-override` to be able to override attributes depending on the post type.

### Moved
* class-shortcode.php - moved from general namespace to helpers.

### Changed
* class-base-post-columns.php was renamed to class-base-post-type-columns.php.

### Fixed
* class-invalid-block.php - Fixed error msg.

## [2.0.7] - 2020-01-29

### Added
- `add_theme_support( 'align-wide' )` in class-blocks.php

## [2.0.6] - 2020-01-27

### Removed
- Limitations the usage of only custom project blocks.
- Removing docs to new repository

## [2.0.5]

### Added

- class-base-post-columns.php - New abstract class for adding articles listing columns.
- class-base-taxonomy-columns.php - New abstract class for adding taxonomy listing columns.
- class-base-user-columns.php - New abstract class for adding users listing columns.
- class-plugin-activation-failure.php - New plugin activation exception.
- Added docpress documentation.

## [2.0.4]

### Added

- has-activation-interface.php - New interface used in plugin activation.
- has-deactivation-interface.php - New interface used in plugin deactivation.

## [2.0.3]

### Updated

- class-block.php - optimization on loading blocks data. Removed caching blocks in transient.
- class-manifest.php - optimization on loading manifest data. Removed caching manifest in transient.

## [2.0.3]

### Added

- Add enqueue abstract class that can be extended in the project.

## [2.0.2]

### Fixed

- Renderable_Block Interface - Fixing wrong type hinting for $inner_block_content.

## [2.0.1]

### Changed

- Updating readme

## [2.0.0]

- Complete refactor on project organization.
- Moving, Babel, Webpack, linters config from boilerplate to eightshift-frontend-libs.
- Rewritten Gutenberg blocks setup.

## [0.9.0]

### Changed

- Renaming assets to manifest folder.

## [0.8.0]

### Removed

- Removing type hinting void for php 7.0.
- Removing Blocks folder and adding eightshift-blocks lib.

## [0.7.0]

### Added

- Added DI instead of SL inside the class-main.php.
- Changed methods used for fetching the manifest items inside Manifest.php to non static methods.


## [0.6.0]

### Changed

- Updating Manifest.php with new methods

## [0.5.0]

### Changed

- Changed $content to $inner_block_content for better naming.
- Unsetting variables in block render.

## [0.4.0]

### Changed

- Changing wrapper block view path.

## [0.3.0]

### Added

- Separating Wrapper_Block and General_Block.

## [0.2.0]

### Added

- Interface for registrable field and route
- Changelog

### Changed

- Interface name for rest route

## [0.1.0] - 2018-04-24

Init setup

### Added

- Main theme/plugin entrypoint.
- Post Type Registration.
- Taxonomy Registration.
- Gutenberg Blocks Registration.
- Assets Manifest data.

[Unreleased]: https://github.com/infinum/eightshift-libs/compare/main...HEAD

[9.0.0]: https://github.com/infinum/eightshift-libs/compare/8.0.7...9.0.0
[8.0.7]: https://github.com/infinum/eightshift-libs/compare/8.0.6...8.0.7
[8.0.6]: https://github.com/infinum/eightshift-libs/compare/8.0.5...8.0.6
[8.0.5]: https://github.com/infinum/eightshift-libs/compare/8.0.4...8.0.5
[8.0.4]: https://github.com/infinum/eightshift-libs/compare/8.0.3...8.0.4
[8.0.3]: https://github.com/infinum/eightshift-libs/compare/8.0.2...8.0.3
[8.0.2]: https://github.com/infinum/eightshift-libs/compare/8.0.1...8.0.2
[8.0.1]: https://github.com/infinum/eightshift-libs/compare/8.0.0...8.0.1
[8.0.0]: https://github.com/infinum/eightshift-libs/compare/7.1.2...8.0.0
[7.1.2]: https://github.com/infinum/eightshift-libs/compare/7.1.1...7.1.2
[7.1.1]: https://github.com/infinum/eightshift-libs/compare/7.1.0...7.1.1
[7.1.0]: https://github.com/infinum/eightshift-libs/compare/7.0.1...7.1.0
[7.0.1]: https://github.com/infinum/eightshift-libs/compare/7.0.0...7.0.1
[7.0.0]: https://github.com/infinum/eightshift-libs/compare/6.5.7...7.0.0
[6.5.7]: https://github.com/infinum/eightshift-libs/compare/6.5.6...6.5.7
[6.5.6]: https://github.com/infinum/eightshift-libs/compare/6.5.5...6.5.6
[6.5.5]: https://github.com/infinum/eightshift-libs/compare/6.5.4...6.5.5
[6.5.4]: https://github.com/infinum/eightshift-libs/compare/6.5.3...6.5.4
[6.5.3]: https://github.com/infinum/eightshift-libs/compare/6.5.2...6.5.3
[6.5.2]: https://github.com/infinum/eightshift-libs/compare/6.5.1...6.5.2
[6.5.1]: https://github.com/infinum/eightshift-libs/compare/6.5.0...6.5.1
[6.5.0]: https://github.com/infinum/eightshift-libs/compare/6.4.0...6.5.0
[6.4.0]: https://github.com/infinum/eightshift-libs/compare/6.3.2...6.4.0
[6.3.2]: https://github.com/infinum/eightshift-libs/compare/6.3.1...6.3.2
[6.3.1]: https://github.com/infinum/eightshift-libs/compare/6.3.0...6.3.1
[6.3.0]: https://github.com/infinum/eightshift-libs/compare/6.2.0...6.3.0
[6.2.0]: https://github.com/infinum/eightshift-libs/compare/6.1.0...6.2.0
[6.1.0]: https://github.com/infinum/eightshift-libs/compare/6.0.0...6.1.0
[6.0.0]: https://github.com/infinum/eightshift-libs/compare/5.1.0...6.0.0
[5.1.0]: https://github.com/infinum/eightshift-libs/compare/5.0.2...5.1.0
[5.0.2]: https://github.com/infinum/eightshift-libs/compare/5.0.1...5.0.2
[5.0.1]: https://github.com/infinum/eightshift-libs/compare/5.0.0...5.0.1
[5.0.0]: https://github.com/infinum/eightshift-libs/compare/4.1.0...5.0.0
[4.1.0]: https://github.com/infinum/eightshift-libs/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/infinum/eightshift-libs/compare/3.1.0...4.0.0
[3.1.0]: https://github.com/infinum/eightshift-libs/compare/3.0.8...3.1.0
[3.0.8]: https://github.com/infinum/eightshift-libs/compare/3.0.7...3.0.8
[3.0.7]: https://github.com/infinum/eightshift-libs/compare/3.0.6...3.0.7
[3.0.6]: https://github.com/infinum/eightshift-libs/compare/3.0.5...3.0.6
[3.0.5]: https://github.com/infinum/eightshift-libs/compare/3.0.4...3.0.5
[3.0.4]: https://github.com/infinum/eightshift-libs/compare/3.0.3...3.0.4
[3.0.3]: https://github.com/infinum/eightshift-libs/compare/3.0.2...3.0.3
[3.0.2]: https://github.com/infinum/eightshift-libs/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/infinum/eightshift-libs/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/infinum/eightshift-libs/compare/2.5.0...3.0.0
[2.5.0]: https://github.com/infinum/eightshift-libs/compare/v2.4.1...v2.5.0
[2.4.1]: https://github.com/infinum/eightshift-libs/compare/v2.4.0...v2.4.1
[2.4.0]: https://github.com/infinum/eightshift-libs/compare/v2.3.0...v2.4.0
[2.3.0]: https://github.com/infinum/eightshift-libs/compare/v2.2.2...v2.3.0
[2.2.2]: https://github.com/infinum/eightshift-libs/compare/v2.2.1...v2.2.2
[2.2.1]: https://github.com/infinum/eightshift-libs/compare/v2.2.0...v2.2.1
[2.2.0]: https://github.com/infinum/eightshift-libs/compare/v2.1.1...v2.2.0
[2.1.1]: https://github.com/infinum/eightshift-libs/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/infinum/eightshift-libs/compare/v2.0.7...v2.1.0
[2.0.7]: https://github.com/infinum/eightshift-libs/compare/v2.0.6...v2.0.7
[2.0.6]: https://github.com/infinum/eightshift-libs/compare/v2.0.5...v2.0.6
[2.0.5]: https://github.com/infinum/eightshift-libs/compare/v2.0.4...v2.0.5
[2.0.4]: https://github.com/infinum/eightshift-libs/compare/v2.0.3...v2.0.4
[2.0.3]: https://github.com/infinum/eightshift-libs/compare/v2.0.2...v2.0.3
[2.0.2]: https://github.com/infinum/eightshift-libs/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/infinum/eightshift-libs/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/infinum/eightshift-libs/compare/0.9.0...v2.0.0
[0.9.0]: https://github.com/infinum/eightshift-libs/compare/0.8.0...0.9.0
[0.8.0]: https://github.com/infinum/eightshift-libs/compare/0.7.1...0.8.0
[0.7.1]: https://github.com/infinum/eightshift-libs/compare/0.7.0...0.7.1
[0.7.0]: https://github.com/infinum/eightshift-libs/compare/0.6.0...0.7.0
[0.6.0]: https://github.com/infinum/eightshift-libs/compare/0.5.0...0.6.0
[0.5.0]: https://github.com/infinum/eightshift-libs/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/infinum/eightshift-libs/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/infinum/eightshift-libs/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/infinum/eightshift-libs/compare/0.1.0...0.2.0
[0.1.0]: https://github.com/infinum/eightshift-libs/compare/cb5398515260043626dc0d7901abf22a2d7bdf9c...0.1.0
