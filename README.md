<p align="center">
  <img alt="Eightshift Frontend Libs" src="https://raw.githubusercontent.com/infinum/eightshift-frontend-libs/develop/package/logo.svg?raw=true&sanitize=true"/>
</p>

[![Travis](https://img.shields.io/travis/infinum/eightshift-libs.svg?style=for-the-badge)](https://travis-ci.org/infinum/eightshift-libs)
[![GitHub tag](https://img.shields.io/github/tag/infinum/eightshift-libs.svg?style=for-the-badge)](https://github.com/infinum/eightshift-libs)
[![GitHub stars](https://img.shields.io/github/stars/infinum/eightshift-libs.svg?style=for-the-badge&label=Stars)](https://github.com/infinum/eightshift-libs)
[![license](https://img.shields.io/github/license/infinum/eightshift-libs.svg?style=for-the-badge)](https://github.com/infinum/eightshift-libs)

# Eightshift Libs

This library is aimed at bringing the modern development tools to the [Eightshift WordPress Boilerplate](https://github.com/infinum/eightshift-boilerplate), but you can use it on any WordPress project.

It uses central service instantiator that instatiates all classes that obey single responsibility principle (SRP). Every class is responsible for registering its own hooks. This provides a more testable environment for your project.

We provide some helpers, abstract classes, interfaces and abstractions on original WordPress functionality to help you write more modern code.

## Provided functionality:
* Main theme/plugin entrypoint.
* Post type registration.
* Taxonomy registration.
* Gutenberg blocks registration.
* Assets manifest data.
* Enqueue methods.
* Language translations.
* Login hooks.
* BEM menus.
* REST routes interfaces.
* Project config.

For detail documentation please check on [Eightshift Boilerplate Wiki](https://github.com/infinum/eightshift-boilerplate/wiki)

## :mailbox: Who do I talk to?

If you have any questions or problems, please [open an issue](https://github.com/infinum/eightshift-libs/issues) on github and we will do our best to give you a timely answer.

Eightshift WordPress Libs is maintained and sponsored by
[Eightshift](https://eightshift.com) and [Infinum](https://infinum.co).

## :scroll: License

Infinum WordPress Libs is Copyright &copy;2019 Infinum. It is free software, and may be redistributed under the terms specified in the LICENSE file.
