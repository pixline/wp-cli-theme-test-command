<?php
/**
 * Install and run WordPress unit-tests
 *
 * @author pixline
 * @version 0.2.1
 * @when after_wp_load
 * @synopsis <action>
 */

class Unit_Test_Cmd extends WP_CLI_Command{

	function create_test_menus(){
		/*
		* menus are already in the usual test WXR file
		* todo: include as an option 
		* (might be useful with custom WXR files)
		*/
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
			WP_CLI::launch( 'wp core is_installed' );
		endif;
	}

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

			$this->maybe_reinstall( $assoc_args );

			# plugins check, install, activation
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

			# test data download + import
			$silent  = WP_CLI::get_config( 'quiet' ) ? '--silent ' : '';
			$cmdline = "curl -f $silent $download_url -o /tmp/theme-unit-test-data.xml";
			WP_CLI::launch( $cmdline );
			WP_CLI::launch( 'wp import /tmp/theme-unit-test-data.xml --authors=skip' );

			# option update
			$this->update_test_options();

			break;
		endswitch;
	}

}

WP_CLI::add_command( 'test', 'Unit_Test_Cmd' );
