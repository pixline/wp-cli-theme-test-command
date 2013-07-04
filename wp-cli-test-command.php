<?php
/**
 * Install and run WordPress unit-tests
 *
 * @author pixline
 * @version 0.2.2
 * @when after_wp_load
 * @synopsis <action>
 */

class Unit_Test_Cmd extends WP_CLI_Command{

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
	 * Install or reset/reinstall WordPress 
	 * 
	 * @param array $assoc_args  Incoming args associative array
	 */
	private function maybe_reinstall( $assoc_args ){
		# WordPress reset/reinstall
		if ( isset( $assoc_args['reset'] ) ):
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
		else :
			WP_CLI::launch( 'wp core is-installed' );
		endif;
	}

	/**
	 * Check plugin status, install and activate as needed
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
	 */
	private function import_test_data( $assoc_args ){
		$dataurl = 'https://wpcom-themes.svn.automattic.com/demo/theme-unit-test-data.xml';
		$download_url = isset( $assoc_args['data'] ) ? $assoc_args['data'] : $dataurl;
		$silent  = WP_CLI::get_config( 'quiet' ) ? '--silent ' : '';
		$cmdline = "curl -f $silent $download_url -o /tmp/theme-unit-test-data.xml";

		WP_CLI::launch( $cmdline );
		WP_CLI::launch( 'wp import /tmp/theme-unit-test-data.xml --authors=skip' );
	}


	/**
	* Install and setup themes unit test options, data and plugins
	* 
	* @when after_wp_load
	* @synopsis <target> 
	*/
	public function install( $args, $assoc_args = array() ){
		print_r( $args );
	}


	/**
	* Setup theme test options, data and plugins
	* 
	* @when after_wp_load
	* @synopsis <target> [--data=<wxr>] [--menus] [--reset] --url=<url> --title=<site-title> [--admin_name=<username>] --admin_email=<email> --admin_password=<password>
	*/
	public function setup( $args, $assoc_args = array() ){
		list( $target ) = $args;
		print_r( $args );
		die();
		switch ( $target ):
			case 'theme':
				$this->maybe_reinstall( $assoc_args );
				$this->manage_plugins(); // plugin check and activation
				$this->import_test_data( $assoc_args ); // test data download and import
				$this->update_test_options(); // blog option update
				if ( isset( $assoc_args['menus'] ) ):
					$this->create_test_menus(); // custom menu optional setup
				endif;
			break;

			case 'plugin':
				WP_CLI::launch( 'wp scaffold plugin-tests ' );
			break;

			case 'core':
				WP_CLI::launch( 'wp core init-tests' );
			break;

			default:
				WP_CLI::line( 'Usage: wp test setup [theme|plugin|core]' );
			break;
		endswitch;
	}


}

WP_CLI::add_command( 'test', 'Unit_Test_Cmd' );
