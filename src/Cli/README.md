# WP-CLI

We created an WP-CLI commands for all the features of the Eightshift-Libs.

To show all available commands in development mode run this command:

```wp eval-file bin/cli.php show_all --skip-wordpress```

To show all available commands in WordPress env run this command:

```wp boilerplate --help```

Quick note: please check if `boilerplate` is the correct command parent. This is defined in your project inside the `functions.php` file.
