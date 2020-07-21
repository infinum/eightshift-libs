<?php
/**
 * Helpers for components
 *
 * @package EightshiftLibs\Console
 */

declare( strict_types=1 );

namespace EightshiftLibs\Console;

/**
 * Helpers for components
 */
class ConsoleHelpers {
  public static function get_class_name( string $file_name ) : string {
    $class    = explode( '_', str_replace( '-', '_', str_replace( ' ', '_', strtolower( $file_name ) ) ) );

    $class_name = implode( '_', array_map( function( $item ) { // phpcs:ignore PEAR.Functions.FunctionCallSignature
        return ucfirst( $item );
    }, $class ) ); // phpcs:ignore PEAR.Functions.FunctionCallSignature

    return $class_name;
  }
}
