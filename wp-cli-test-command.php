<?php
/**
 * Install and run WordPress unit-tests (core, plugin and themes)
 *
 * @author pixline
 */

class Unit_Test_Cmd extends WP_CLI_Command{

    /**
     * Setup unit tests
     * 
     * @synopsis <target> [--data=<wxr_url>] [--reset=<bool>] [--url=<url>] [--title=<title>] [--admin_name=<username>] [--admin_email=<email>] [--admin_password=<password>]
     */
    public function setup( $args, $assoc_args ){
      list( $target ) = $args;
      $download_url = 'https://wpcom-themes.svn.automattic.com/demo/theme-unit-test-data.xml';


      switch ( $target ) :
        case 'theme' :
        default:

          $silent = WP_CLI::get_config( 'quiet' ) ? '--silent ' : '';
          $cmd = "curl -f $silent $download_url -o /tmp/theme-unit-test-data.xml";
          WP_CLI::launch( $cmd );

          # reset wp
          #WP_CLI::launch( 'wp db reset --yes' );

          # install wp (install config file? args? both?)
          #WP_CLI::launch( 'wp core install --url=<$url> --title=<$title> --admin_name=<$admin> --admin_email=<$email> --admin_password=<$password>' );

          # import xml
          WP_CLI::launch( 'wp import /tmp/theme-unit-test-data.xml --authors=skip' );

          # install plugins
          WP_CLI::launch( 'wp plugin install developer --activate' );
          WP_CLI::launch( 'wp plugin install theme-check --activate' );
          WP_CLI::launch( 'wp plugin install debug-bar --activate' );
          WP_CLI::launch( 'wp plugin install log-deprecated-notices --activate' );
          WP_CLI::launch( 'wp plugin install debogger --activate' );
          WP_CLI::launch( 'wp plugin install monster-widget --activate' );
          WP_CLI::launch( 'wp plugin install wordpress-beta-tester --activate' );
          WP_CLI::launch( 'wp plugin install regenerate-thumbnails --activate' );

          # options 
          WP_CLI::launch( 'wp option update blogname "WordPress Theme Unit Test Site"' );
          WP_CLI::launch( 'wp option update posts_per_page 5' );
          WP_CLI::launch( 'wp option update thread_comments 1' );
          WP_CLI::launch( 'wp option update thread_comments_depth 3' );
          WP_CLI::launch( 'wp option update page_comments 1' );
          WP_CLI::launch( 'wp option update comments_per_page 5' );

          WP_CLI::launch( 'wp option update medium_max_w ""' );
          WP_CLI::launch( 'wp option update medium_max_h ""' );
          WP_CLI::launch( 'wp option update large_max_w ""' );
          WP_CLI::launch( 'wp option update large_max_h ""' );

          WP_CLI::launch( 'wp option update permalink_structure "/%year%/%monthnum%/%day%/%postname%/"' );

          # todo: create long custom menu, all pages
          # todo: create short custom menu, 2/3 pages
        break;
      endswitch;
    }

    /**
     * @alias run
     * @synopsis <target> [--phpunit-flags]
     */
    public function _run(){
      // run tests (core | plugin <slug> )
    }

}

WP_CLI::add_command( 'unittest', 'Unit_Test_Cmd' );
