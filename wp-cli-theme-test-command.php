<?php
/**
 * Install and run WordPress unit-tests
 *
 * @author pixline <pixline@gmail.com>
 * @version 0.4.2
 * @when after_wp_load
 * @synopsis <action>
 */

class Theme_Test_Cmd extends WP_CLI_Command{

	/**
	 * create optional test nav menu
	 * 
	 * At least two custom menus should be created in order to test a theme
	 * The standard Theme data file now ships with optimal menus built-in
	 * This method actually makes sense with custom WXR files only
	 * 
	 * @since 0.2
	 */
	private function create_test_menus(){
		$pages = get_all_page_ids();
		$items = array(); 
		foreach ( $pages as $page_ID ):
			$info = get_page( $page_ID );
			$items[ $info->post_title ] = get_permalink( $page_ID );
		endforeach;

		# pick three random entries
		$random = array_rand( $items, 3 );

		# build menus
		$menus = array(
			'Full Menu' => array(
			'slug' => 'full-menu',
			'menu_items' => $items,
		),
			'Short Menu' => array(
			'slug' => 'short-menu',
			'menu_items' => array(
				$items[ $random[0] ],
				$items[ $random[1] ],
				$items[ $random[2] ],
			)
		),
		);

		# register menus
		foreach ( $menus as $title => $data ):
			register_nav_menu( $data['slug'], $title );
			if ( false == is_nav_menu( $title ) ):
				$menu_ID = wp_create_nav_menu( $title );
				foreach ( $data['menu_items'] as $name => $url ):
					$add_item = array(
						'menu-item-type' => 'custom',
						'menu-item-url' => $url,
						'menu-item-title' => $name,
					);
					wp_update_nav_menu_item( $menu_ID, 0, $add_item );
				endforeach;
				WP_CLI::success( 'Created menu '.$title );
			endif;
		endforeach;
	}

	/**
	 * Update blog options with optimal testing standards
	 * 
	 * @since 0.2
	 */
	private function update_test_options( $option = NULL ){
		if ( 'skip' === $option )
			return;

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
	}

	/**
	 * Check plugin status, install and activate as needed
	 * 
	 * @param string $option  --plugin value
	 * @since 0.2
	 */
	private function manage_plugins( $option = NULL ){
		if ( 'skip' === $option )
			return;

		# default plugin set
		$std_plugin = array(
			'debug-bar',
			'debug-bar-console',
			'debug-bar-cron',
			'debug-bar-extender',
			'developer',
			'log-viewer',
			'monster-widget',
			'piglatin',
			'regenerate-thumbnails',
			'rewrite-rules-inspector',
			'rtl-tester',
			'simply-show-ids',
			'theme-check',
			'theme-test-drive',
			'user-switching',
			'wordpress-importer',
			'wordpress-beta-tester',
		);

		# wpcom VIP plugin set
		$vip_plugin = array(
			'grunion-contact-form',
			'jetpack',
			'mp6',
			'polldaddy',
			'vip-scanner',
		);
		
		# plugin developers bundle
		$dev_plugin = array(
			'log-deprecated-notices',
		);
		
		# debug plugin bundle (author's choice)
		# please file a pull request to include/exclude plugins
		$debug_plugin = array(
			'debug-bar-actions-and-filters-addon',
			'debug-bar-constants',
			'debug-my-plugin',
			'debug-objects',
			'uploadplus',
		);

		switch ( $option ):
		case 'vip':
				$plugin_list = array_merge( $std_plugin, $vip_plugin );
				break;

		case 'devel':
				$plugin_list = array_merge( $std_plugin, $dev_plugin );
				break;

		case 'debug':
				$plugin_list = array_merge( $std_plugin, $debug_plugin );
				break;

		case 'all':
				$plugin_list = array_merge( $std_plugin, $vip_plugin, $dev_plugin, $debug_plugin );

		case 'theme':
		default:
				$plugin_list = $std_plugin;
				break;
		endswitch;

		$skip_activation = array( 'piglatin', 'wordpress-beta-tester' );
		# do install
		foreach ( $plugin_list as $plugin ) :
			$res = WP_CLI::launch( 'wp plugin status '.$plugin, false );
			
			if ( isset( $res ) && $res === 1 ) {
				# install plugin (maybe skip piglatin)
				$cmdflag = ( in_array( $plugin, $skip_activation ) ) ? '' : ' --activate';
				WP_CLI::launch( 'wp plugin install ' . $plugin . $cmdflag );
			} else {
				# activate plugin (maybe skip piglatin)
				if ( false === in_array( $plugin, $skip_activation ) )
					WP_CLI::launch( 'wp plugin activate '.$plugin );
			}
		endforeach;
	}

	/**
	 * Download and install theme unit test datafile
	 * 
	 * @param string $option  --data value
	 * @since 0.2
	 */
	private function import_test_data( $option = NULL ){
		if ( 'skip' === $option )
			return;

		$option = ( NULL === $option ) ? 'unit-test' : $option;

		$datafiles = array(
			'unit-test' => 'https://wpcom-themes.svn.automattic.com/demo/theme-unit-test-data.xml',
			'wpcom-theme' => 'https://wpcom-themes.svn.automattic.com/demo/wordpress-com-theme-test.xml',
			'wpcom-demo' => 'https://wpcom-themes.svn.automattic.com/demo/demo-data.xml',
			'wptest' => 'https://raw.github.com/manovotny/wptest/master/wptest.xml',
		);
		$keys = array_values( array_keys( $datafiles ) );

		if ( in_array( $option, $keys ) ):
			$download_url = $datafiles[$option];			
		elseif ( false != $option ):
			$download_url = $option;
		else :
			WP_CLI::error( 'Missing WXR path/URL.' );
		endif;

		WP_CLI::line( 'WXR data URL: ' . $download_url );
		$silent  = WP_CLI::get_config( 'quiet' ) ? '--silent ' : '';
		$cmdline = "curl -f $silent $download_url -o /tmp/wp-cli-test-data.xml";

		WP_CLI::launch( $cmdline );
		WP_CLI::launch( 'wp import /tmp/wp-cli-test-data.xml --authors=skip' );
	}


	/**
	* Install and setup theme unit test options, data and plugins
	* 
	* Usage: wp theme-test setup [options]
	* 
	* ## OPTIONS
	*
	* --data=<data>
	* : URL/path to WXR data file 	[unit-test*|wpcom-theme|wpcom-demo|wptest|skip]
	*
	* --plugin=<plugin>
	* : Install and activate suggested plugin bundle  [theme*|vip|devel|debug|all|skip]
	*
	* --option=<option>
	* : Update blog options with optimal testing standards [NULL*|skip]
	*
	* --menus
	* : Create custom nav menus (full page list, short random page list)
	* 
	* ## EXAMPLES
	*
	* wp theme-test install
	* wp theme-test install --data=skip --option=skip --plugin=skip
	* wp theme-test install --data=wpcom-theme --plugin=all
	*
	* @when after_wp_load
	* @synopsis [--data=<data>] [--plugin=<plugin>] [--option=<option>] [--menus] 
	* @since 0.2
	* @todo check wp-config.php for debug constants
	*/
	public function install( $args = null, $assoc_args = array() ){

		# plugin check, download and activation
		$this->manage_plugins( $assoc_args['plugin'] );

		# download and import test data
		$this->import_test_data( $assoc_args['data'] );

		# update blog options
		$this->update_test_options( $assoc_args['option'] );

		# (optional) create custom menus
		if ( isset( $assoc_args['menus'] ) ):
			$this->create_test_menus();
		endif;
	}

}

WP_CLI::add_command( 'theme-test', 'Theme_Test_Cmd' );
