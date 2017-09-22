<?php

namespace Diocese\Post_Types;

use Diocese\Post_Types;

class Diocese {
	static function register_type() {
		register_post_type( 'diocese', array(
			'labels'            => array(
				'name'                => __( 'Dioceses', 'diocese' ),
				'singular_name'       => __( 'Diocese', 'diocese' ),
				'all_items'           => __( 'All Dioceses', 'diocese' ),
				'new_item'            => __( 'New Diocese', 'diocese' ),
				'add_new'             => __( 'Add New', 'diocese' ),
				'add_new_item'        => __( 'Add New Diocese', 'diocese' ),
				'edit_item'           => __( 'Edit Diocese', 'diocese' ),
				'view_item'           => __( 'View Diocese', 'diocese' ),
				'search_items'        => __( 'Search Dioceses', 'diocese' ),
				'not_found'           => __( 'No Dioceses found', 'diocese' ),
				'not_found_in_trash'  => __( 'No Dioceses found in trash', 'diocese' ),
				'parent_item_colon'   => __( 'Parent Diocese', 'diocese' ),
				'menu_name'           => __( 'Dioceses', 'diocese' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions' ),
			'has_archive'       => true,
			'rewrite'           => true,
			'query_var'         => true,
			'menu_icon'         => 'dashicons-admin-post',
			'show_in_rest'      => true,
			'rest_base'         => 'diocese',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		) );
	} 

	static function diocese_updated_messages( $messages ) {
		global $post;
	
		$permalink = get_permalink( $post );
	
		$messages['diocese'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Diocese updated. <a target="_blank" href="%s">View Diocese</a>', 'diocese'), esc_url( $permalink ) ),
			2 => __('Custom field updated.', 'diocese'),
			3 => __('Custom field deleted.', 'diocese'),
			4 => __('Diocese updated.', 'diocese'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Diocese restored to revision from %s', 'diocese'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Diocese published. <a href="%s">View Diocese</a>', 'diocese'), esc_url( $permalink ) ),
			7 => __('Diocese saved.', 'diocese'),
			8 => sprintf( __('Diocese submitted. <a target="_blank" href="%s">Preview Diocese</a>', 'diocese'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			9 => sprintf( __('Diocese scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Diocese</a>', 'diocese'),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
			10 => sprintf( __('Diocese draft updated. <a target="_blank" href="%s">Preview Diocese</a>', 'diocese'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		);
	
		return $messages;
	}

	static function diocese_template( $single ) {
		
		global $wp_query, $post;
		
		/* Checks for single template by post type */
		if ( $post->post_type == 'diocese' ) {
			$filename = plugin_dir_path( __FILE__ );
			if ( file_exists( $filename . 'templates/diocese-template.php' ) ) {
				return $filename . 'templates/diocese-template.php';
			} else {
				throw new \Exception("File does not exist. {$filename}");
			}
		}
	
		return $single;
	}

	static function diocese_diocese_header() {
		the_title();
	}

	static function diocese_diocese_description() {
		the_content();
	}

	static function diocese_diocese_details() {
		
	}

	static function bootstrap() {
		add_action( 'init', __NAMESPACE__ . '\\Diocese::register_type' );
		add_filter( 'post_updated_messages', __NAMESPACE__ . '\\Diocese::diocese_updated_messages', 11 );
		add_filter( 'single_template', __NAMESPACE__ . '\\Diocese::diocese_template', 10, 1 );

		add_action( 'diocese_diocese_header', __NAMESPACE__ . '\\Diocese::diocese_diocese_header' );
		add_action( 'diocese_diocese_description', __NAMESPACE__ . '\\Diocese::diocese_diocese_description' );
		add_action( 'diocese_diocese_details', __NAMESPACE__ . '\\Diocese::diocese_diocese_details' );
		add_action( 'init', __NAMESPACE__ . '\\Diocese::province_taxonomy' );
	}

	static function province_taxonomy() {
		// create a new taxonomy
		register_taxonomy(
			'province',
			'diocese',
			array(
				'label' => __( 'Province' ),
				'capabilities' => array(
					'assign_terms' => 'edit_guides',
					'edit_terms' => 'publish_guides'
				)
			)
		);
	}
}