<?php

/**
 * A helper script that will remove wp-cotent folder created after installing
 * mnsami/composer-custom-directory-installer for moving DB dropin to a custom
 * folder.
 */
$ds = DIRECTORY_SEPARATOR;
$path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'wp-content';

if (PHP_OS === 'Windows') {
    exec(sprintf("rd /s /q %s", escapeshellarg($path)));
} else {
    exec(sprintf("rm -rf %s", escapeshellarg($path)));
}
