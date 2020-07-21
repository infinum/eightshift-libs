<?php
/**
 * File holding the REST route generation command
 *
 * @package EightshiftLibs\Rest\Routes
 */

declare(strict_types=1);

namespace EightshiftLibs\Rest\Routes;

use EightshiftLibs\Console\ConsoleHelpers;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class GenerateRestRoute
 *
 * Symfony command generator class used for REST route generation.
 *
 * @package EightshiftLibs\Rest\Routes
 */
class GenerateRestRoute extends Command {

  /**
   * Project root
   */
  protected $root;

  /**
   * Undocumented function
   *
   * @param string $root
   */
  public function __construct( string $root ) {
    parent::__construct();

    $this->root = $root;
  }

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

    $endpoint_slug = $input->getArgument( 'endpoint-name' );

    if ( empty( $endpoint_slug ) ) {
      throw new RuntimeException( 'Endpoint slug empty' );
    }

    /**
     * Passed method argument
     *
     * @var string
     */
    $method = $input->getArgument( 'method' );

    if ( ! in_array( $method, [ 'GET', 'POST', 'PATCH', 'PUT', 'DELETE' ], true ) ) {
      throw new RuntimeException(
        sprintf( 'HTTP verb must be one of: \'GET\', \'POST\', \'PATCH\', \'PUT\', or \'DELETE\'. %s provided.', $method )
      );
    }

    $endpoint = str_replace( '_', '-', str_replace( ' ', '-', strtolower( $endpoint_slug ) ) );

    $class_name = ConsoleHelpers::get_class_name( $endpoint_slug );

    $verb_mapping = [
      'GET'    => 'READABLE',
      'POST'   => 'CREATABLE',
      'PATCH'  => 'EDITABLE',
      'PUT'    => 'UPDATEABLE',
      'DELETE' => 'DELETABLE',
    ];

    // Read the template contents, and replace the placeholders with provided variables.
    $template_file = file_get_contents( __DIR__ . '/Route.php' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    if ( $template_file === false ) {
        throw new RuntimeException( 'The template "/Route.php" seems to be missing.' );
    }

    $class = str_replace( 'class Route', "class Route{$class_name}", $template_file );
    $class = str_replace( "const ENDPOINT_SLUG = '/route'", "const ENDPOINT_SLUG = '/{$endpoint}'", $class );
    $class = str_replace( "'methods'  => static::READABLE,", "'methods'  => static::{$verb_mapping[ $method ]},", $class );

    $rest_dir = $this->root . '/src/rest/routes';
    $file     = $rest_dir . "/Route{$class_name}.php";

    if ( file_exists( $file ) ) {
        throw new RuntimeException(
          sprintf( 'The file "%s" can\'t be generated because it already exists.', "{$endpoint}.php" )
        );
    }

    if ( ! is_dir( $rest_dir ) ) {
      mkdir( $rest_dir, 0755, true );
    }

    $fp = fopen( $file, 'wb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions

    if ( $fp !== false ) {
        fwrite( $fp, $class ); // phpcs:ignore WordPress.WP.AlternativeFunctions
        fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions
    } else {
        $io->error( "File {$endpoint}.php couldn't be created in {$rest_dir} directory. There was an error." );
    }

    $io->success( "File {$endpoint}.php successfully created in {$rest_dir} directory." );

    return 0;
  }
}
