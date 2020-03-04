# Change Log for the Eightshift Libs
All notable changes to this project will be documented in this file.

This projects adheres to [Semantic Versioning](https://semver.org/) and [Keep a CHANGELOG](https://keepachangelog.com/).

## [Unreleased]

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

- class-block.php - optimisation on loading blocks data. Removed caching blocks in transient.
- class-manifest.php - optimisation on loading manifest data. Removed caching manifest in transient.

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

- Complete refactor on project organisation.
- Moving, Babel, Webpack, linters config from boilerplate to eightshift-frontend-libs.
- Rewritten Gutenberg blocks setup.

## [0.9.0]

### Changed

- Renaming assets to manifest folder.

## [0.8.0]

### Removed

- Removing type hinting void for php 7.0.
- Removing Blocks folder and adding eigthshift-blocks lib.

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

[Unreleased]: https://github.com/infinum/eightshift-libs/compare/master...HEAD

[2.1.0]: https://github.com/infinum/eightshift-libs/compare/2.0.7...v2.1.0
[2.0.7]: https://github.com/infinum/eightshift-libs/compare/2.0.6...v2.0.7
[2.0.6]: https://github.com/infinum/eightshift-libs/compare/2.0.5...v2.0.6
[2.0.5]: https://github.com/infinum/eightshift-libs/compare/2.0.4...v2.0.5
[2.0.4]: https://github.com/infinum/eightshift-libs/compare/2.0.3...v2.0.4
[2.0.3]: https://github.com/infinum/eightshift-libs/compare/2.0.2...v2.0.3
[2.0.2]: https://github.com/infinum/eightshift-libs/compare/2.0.1...v2.0.2
[2.0.1]: https://github.com/infinum/eightshift-libs/compare/2.0.0...v2.0.1
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
