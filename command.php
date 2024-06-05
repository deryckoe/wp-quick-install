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

        // Confirm with the user if they are sure about installing WordPress in the current directory
        WP_CLI::confirm( "Are you sure you want to install WordPress in the directory '{$foldername}'?" );

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
