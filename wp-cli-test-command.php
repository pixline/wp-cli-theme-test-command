<?php
/**
 * Install and run WordPress unit-tests
 *
 * @author pixline
 * @when after_wp_load
 * @synopsis <action>
 */

class Unit_Test_Cmd extends WP_CLI_Command{

    /**
     * Setup unit tests
     * 
     * @when after_wp_load
     * @synopsis <target> [--data=<wxr>] [--reset] --url=<url> --title=<site-title> [--admin_name=<username>] --admin_email=<email> --admin_password=<password>
     */
    public function setup( $args, $assoc_args = array() ){
      list( $target ) = $args;

      # default download URL
      $dataurl = 'https://wpcom-themes.svn.automattic.com/demo/theme-unit-test-data.xml';
      $download_url = isset( $assoc_args['data'] ) ? $assoc_args['data'] : $dataurl;

      switch ( $target ) :
        case 'theme' :
        default: 

          # WordPress reset/reinstall
          if ( isset( $assoc_args['reset'] ) ):
            $actual_URL = get_option( 'siteurl' );

            # reset wp
            WP_CLI::launch( 'wp db reset --yes' );

            # install wp
            WP_CLI::launch(
                'wp core install '
                .' --url='.$assoc_args['url']
                .' --title='.$assoc_args['title']
                .' --admin_name='.$assoc_args['admin_name']
                .' --admin_email='.$assoc_args['admin_email']
                .' --admin_password='.$assoc_args['admin_password']
            );
          endif;

          # test data download
          $silent = WP_CLI::get_config( 'quiet' ) ? '--silent ' : '';
          $cmd = "curl -f $silent $download_url -o /tmp/theme-unit-test-data.xml";
          WP_CLI::launch( $cmd );

          # test data xml import
          WP_CLI::launch( 'wp import /tmp/theme-unit-test-data.xml --authors=skip' );


          # plugins check, install, activation
          $plugins = array(
            'developer', 
            'theme-check', 
            'debug-bar', 
            'log-deprecated-notices', 
            'debogger', 
            'monster-widget', 
            'wordpress-beta-tester', 
            'regenerate-thumbnails',
            'zendesk',
          );

          foreach ( $plugins as $plugin ):
            $res = WP_CLI::launch( 'wp plugin status '.$plugin, false );
            if ( isset( $res) && $res === 1 ){
              # install and activate plugin
              WP_CLI::launch( 'wp plugin install '.$plugin.' --activate' );
            } else {
              # activate plugin
              WP_CLI::launch( 'wp plugin activate '.$plugin );
            }
          endforeach;

          # option update
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

}

WP_CLI::add_command( 'test', 'Unit_Test_Cmd' );
