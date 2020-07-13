<?php
/**
 * File holding the REST route generation command
 *
 * @package Eightshift_Libs\Commands
 */

declare(strict_types=1);

namespace Eightshift_Libs\Commands;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class Generate_Rest_Route
 *
 * Symfony command generator class used for REST route generation.
 *
 * @package Eightshift_Libs\Commands
 */
class Generate_Rest_Route extends Command {

  /**
   * Command name property
   *
   * @var string Command name.
   */
  protected static $defaultName = 'generate:route'; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase

  /**
   * Configures the current command
   *
   * @inheritDoc
   */
  protected function configure() : void {
    $this
      ->setDescription( 'Generate a custom REST class' )
      ->setHelp( 'This command will create a template class for the custom WordPress REST API endpoint.' )
      ->addArgument( 'endpoint-name', InputArgument::REQUIRED, 'Pass the slug of your endpoint. Example: my-endpoint' )
      ->addArgument( 'method', InputArgument::REQUIRED, 'Pass the desired HTTP transport method (verb). Can be one of: \'GET\', \'POST\', \'PATCH\', \'PUT\', or \'DELETE\'' );
  }

  /**
   * Execute the current command
   *
   * @param InputInterface  $input  Input values.
   * @param OutputInterface $output Output values.
   *
   * @return int
   * @throws RuntimeException Validation exceptions.
   */
  protected function execute( InputInterface $input, OutputInterface $output ) : int {
    $io = new SymfonyStyle( $input, $output );

    /**
     * Passed endpoint name argument
     *
     * @var string
     */
    $endpoint_slug = $input->getArgument( 'endpoint-name' );

    /**
     * Passed method argument
     *
     * @var string
     */
    $method = $input->getArgument( 'method' );

    if ( empty( $endpoint_slug ) ) {
      throw new RuntimeException( 'Endpoint slug empty' );
    }

    if ( ! in_array( $method, [ 'GET', 'POST', 'PATCH', 'PUT', 'DELETE' ], true ) ) {
      throw new RuntimeException(
        sprintf( 'HTTP verb must be one of: \'GET\', \'POST\', \'PATCH\', \'PUT\', or \'DELETE\'. %s provided.', $method )
      );
    }

    $endpoint = str_replace( '_', '-', str_replace( ' ', '-', strtolower( $endpoint_slug ) ) );
    $class    = explode( '_', str_replace( '-', '_', str_replace( ' ', '_', strtolower( $endpoint_slug ) ) ) );

    $class_name = implode( '_', array_map( function( $item ) { // phpcs:ignore PEAR.Functions.FunctionCallSignature
        return ucfirst( $item );
    }, $class ) ); // phpcs:ignore PEAR.Functions.FunctionCallSignature

    $verb_mapping = [
      'GET'    => 'READABLE',
      'POST'   => 'CREATABLE',
      'PATCH'  => 'EDITABLE',
      'PUT'    => 'UPDATEABLE',
      'DELETE' => 'DELETABLE',
    ];

    // Read the template contents, and replace the placeholders with provided variables.
    $template_file = file_get_contents( __DIR__ . '/templates/class-route.tmpl.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    if ( $template_file === false ) {
        throw new RuntimeException( 'The template "/templates/class-route.tmpl.php seems to be missing.' );
    }

    $class_boilerplate = str_replace( '%CLASS_NAME%', $class_name, $template_file );
    $class_boilerplate = str_replace( '%ENDPOINT%', $endpoint, $class_boilerplate );
    $class_boilerplate = str_replace( '%VERB%', $verb_mapping[ $method ], $class_boilerplate );

    $rest_dir = dirname( __FILE__, 2 ) . '/rest';
    $file     = $rest_dir . "/class-{$endpoint}.php";

    if ( file_exists( $file ) ) {
        throw new RuntimeException(
          sprintf( 'The file "%s" can\'t be generated because it already exists.', "class-{$endpoint}.php" )
        );
    }

    $fp = fopen( $file, 'wb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    if ( $fp !== false ) {
        fwrite( $fp, $class_boilerplate ); // phpcs:ignore WordPress.WP.AlternativeFunctions
        fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions
    } else {
        $io->error( "File class-{$endpoint}.php couldn't be created in {$rest_dir} directory. There was an error." );
    }

    $io->success( "File class-{$endpoint}.php successfully created in {$rest_dir} directory." );

    return 0;
  }
}
