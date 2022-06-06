# WP-CLI

We created an WP-CLI commands for all the features of the Eightshift-Libs.

## Develop
You can run this commands in development mode.

### Show all available commands:

```wp eval-file bin/cli.php develop_show_all --skip-wordpress```

### Run all commands:

```wp eval-file bin/cli.php develop_show_all --skip-wordpress```

### Reset output:

```wp eval-file bin/cli.php develop_reset --skip-wordpress```

## Boilerplate
You can run this command to show all available commands in WordPress env:

```wp boilerplate --help```

Quick note: please check if `boilerplate` is the correct command parent. This is defined in your project inside the `functions.php` file.
