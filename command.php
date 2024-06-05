<?php

if ( ! class_exists( 'WP_CLI' ) ) {
    return;
}

/**
 * Installs WordPress with custom settings.
 */
class Install_Command {
    /**
     * Run the installation script.
     *
     * ## EXAMPLES
     *
     *     wp install
     *
     * @when before_wp_load
     */
    public function __invoke() {
        $foldername = basename( getcwd() );

        // Check if the folder is empty excluding dotfiles and hidden files
        $files = array_filter( glob( "*", GLOB_NOCHECK ), function( $file ) {
            return ! preg_match( '/^\./', $file );
        });

        if ( ! empty( $files ) ) {
            WP_CLI::confirm( "The folder is not empty. Do you want to continue?" );
        }

        WP_CLI::log( "Downloading WordPress core..." );
        WP_CLI::runcommand( 'core download' );

        WP_CLI::log( "Creating MySQL database 'wp_{$foldername}'..." );
        $create_db_command = sprintf( 'mysql -u root -e "CREATE DATABASE wp_%s;"', $foldername );
        shell_exec( $create_db_command );

        WP_CLI::log( "Creating WordPress configuration file..." );
        WP_CLI::runcommand( "config create --dbname=wp_{$foldername} --dbuser=root" );

        WP_CLI::log( "Installing WordPress..." );
        WP_CLI::runcommand( sprintf( "core install --url=http://%s.test --title=%s --admin_user=%s --admin_password=%s --admin_email=admin@%s.test", $foldername, $foldername, $foldername, $foldername, $foldername ) );

        WP_CLI::success( "Installation complete. You can now open your site at http://{$foldername}.test" );
    }
}

WP_CLI::add_command( 'install', 'Install_Command' );
