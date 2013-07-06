<?php
/**
 * Install and run WordPress unit-tests
 *
 * @author pixline <pixline@gmail.com>
 * @version 0.4.0
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
	private function update_test_options(){
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
	 * @since 0.2
	 */
	private function manage_plugins(){
		$plugins = array(
			'debogger', 
			'debug-bar', 
			'developer', 
			'log-deprecated-notices', 
			'monster-widget', 
			'regenerate-thumbnails',
			'theme-check', 
			'wordpress-beta-tester', 
			'wordpress-importer',
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
	}

	/**
	 * Download and install theme unit test datafile
	 * 
	 * @param array $assoc_args  Incoming args associative array
	 * @since 0.2
	 */
	private function import_test_data( $alt_data = null ){
		$std_data = 'https://wpcom-themes.svn.automattic.com/demo/theme-unit-test-data.xml';
		$download_url = isset( $alt_data ) ? $alt_data : $std_data;
		$silent  = WP_CLI::get_config( 'quiet' ) ? '--silent ' : '';
		$cmdline = "curl -f $silent $download_url -o /tmp/theme-unit-test-data.xml";

		WP_CLI::launch( $cmdline );
		WP_CLI::launch( 'wp import /tmp/theme-unit-test-data.xml --authors=skip' );
	}


	/**
	* Install and setup theme unit test options, data and plugins
	* 
	* Usage: wp theme-test setup [options]
	* 
	* --data=<url|path>				URL/path to WXR data file 
	* --menus 								Create custom nav menus (full page list, short random page list)
	* 
	* @when after_wp_load
	* @synopsis [--data=<data>] [--menus] 
	* @since 0.2
	*/
	public function install( $args = null, $assoc_args = array() ){

		# plugin check, download and activation
		$this->manage_plugins(); 	

		# download and import test data
		$this->import_test_data( $assoc_args['data'] );

		# update blog options
		$this->update_test_options();

		# (optional) create custom menus
		if ( isset( $assoc_args['menus'] ) ):
			$this->create_test_menus();
		endif;
	}

}

WP_CLI::add_command( 'theme-test', 'Theme_Test_Cmd' );
