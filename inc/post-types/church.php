<?php

namespace Diocese\Post_Types;

class Church {
	static function register_type() {
		register_post_type( 'church', array(
			'labels'            => array(
				'name'                => __( 'Churches', 'diocese' ),
				'singular_name'       => __( 'Church', 'diocese' ),
				'all_items'           => __( 'All Churches', 'diocese' ),
				'new_item'            => __( 'New Church', 'diocese' ),
				'add_new'             => __( 'Add New', 'diocese' ),
				'add_new_item'        => __( 'Add New Church', 'diocese' ),
				'edit_item'           => __( 'Edit Church', 'diocese' ),
				'view_item'           => __( 'View Church', 'diocese' ),
				'search_items'        => __( 'Search Churches', 'diocese' ),
				'not_found'           => __( 'No Churches found', 'diocese' ),
				'not_found_in_trash'  => __( 'No Churches found in trash', 'diocese' ),
				'parent_item_colon'   => __( 'Parent Church', 'diocese' ),
				'menu_name'           => __( 'Churches', 'diocese' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'supports'          => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions' ),
			'has_archive'       => true,
			'rewrite'           => true,
			'query_var'         => true,
			'menu_icon'         => 'dashicons-admin-post',
			'show_in_rest'      => true,
			'rest_base'         => 'church',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		) );
	} 

	static function church_updated_messages( $messages ) {
		global $post;
	
		$permalink = get_permalink( $post );
	
		$messages['church'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Church updated. <a target="_blank" href="%s">View Church</a>', 'diocese'), esc_url( $permalink ) ),
			2 => __('Custom field updated.', 'diocese'),
			3 => __('Custom field deleted.', 'diocese'),
			4 => __('Church updated.', 'diocese'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Church restored to revision from %s', 'diocese'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Church published. <a href="%s">View Church</a>', 'diocese'), esc_url( $permalink ) ),
			7 => __('Church saved.', 'diocese'),
			8 => sprintf( __('Church submitted. <a target="_blank" href="%s">Preview Church</a>', 'diocese'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			9 => sprintf( __('Church scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Church</a>', 'diocese'),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
			10 => sprintf( __('Church draft updated. <a target="_blank" href="%s">Preview Church</a>', 'diocese'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		);
	
		return $messages;
	}

	static function church_template( $single ) {
		
		global $wp_query, $post;
		
		/* Checks for single template by post type */
		if ( $post->post_type == 'church' ) {
			$filename = plugin_dir_path( __FILE__ );
			if ( file_exists( $filename . 'templates/church-template.php' ) ) {
				return $filename . 'templates/church-template.php';
			} else {
				throw new \Exception("File does not exist. {$filename}");
			}
		}
	
		return $single;
	}

	static function diocese_church_header() {
		the_title();
	}

	static function diocese_church_description() {
		the_post_thumbnail();
		$post = get_post();
		echo apply_filters( 'the_content', $post->post_content );
	}

	static function diocese_church_details() {
		$data = get_post_meta(get_the_id());
		
		var_dump($data);
	}

	static function bootstrap() {
		add_action( 'init', __NAMESPACE__ . '\\Church::register_type' );
		add_filter( 'post_updated_messages', __NAMESPACE__ . '\\Church::church_updated_messages', 11 );
		add_filter( 'single_template', __NAMESPACE__ . '\\Church::church_template', 10, 1 );

		add_action( 'diocese_church_header', __NAMESPACE__ . '\\Church::diocese_church_header' );
		add_action( 'diocese_church_description', __NAMESPACE__ . '\\Church::diocese_church_description' );
		add_action( 'diocese_church_details', __NAMESPACE__ . '\\Church::diocese_church_details' );
		add_action( 'init', __NAMESPACE__ . '\\Church::deanery_taxonomy' );
	}

	static function deanery_taxonomy() {
		// create a new taxonomy
		register_taxonomy(
			'deanery',
			'church',
			array(
				'label' => __( 'Deanery' ),
				'capabilities' => array(
					'assign_terms' => 'edit_guides',
					'edit_terms' => 'publish_guides'
				)
			)
		);
	}

}
