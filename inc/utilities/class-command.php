<?php


namespace Diocese\Utilities;

use WP_CLI_Command;
use WP_CLI;
use WP_CLI\Utils;


class Command extends WP_CLI_Command {
	/**
	* Generate a code challenge.
	*
	* ## OPTIONS
	*
	* [<name_of_file>]
	* : Name of the CSV file as input
	*
	*
	* [--dioceses]
	* : Populates the dioceses if this flag is set
	* ---
    *
    * [--churches]
	* : Populates the churches if this flag is set
	* ---
	*
	* ## EXAMPLES
	*
	*     wp diocese populate --dioceses | --churches
	*
	* @alias populate
	*/
	function populate( $args, $assoc_args ) {

        if ( ! empty( $assoc_args['dioceses'] ) ) {
            $csv = $this->get_csv_file( 'dioceses' );

            //taxonomies, province

            foreach( $csv as $diocese ) {

                $args = array(
                    'post_type' => array( 'diocese' ),
                    'post_status' => array('publish', 'draft'),
                    'meta_query' => array(
                        array(
                            'key' => 'diocese',
                            'value' => strtolower( $diocese['title'] ),
                            'compare' => '=',
                        )
                    )
                 );
                 $query = new \WP_Query($args);

                 if ( $query->have_posts() ) {
                     WP_CLI::warning( "Diocese {$diocese['title']} already added. Updating." );

                     $query->the_post();

                     update_post_meta( get_the_id(), 'website', strpos( $diocese['website'], 'http' ) === false ? 'https://' . $diocese['website'] : $diocese['website'] );
                     update_post_meta( get_the_id(), 'cathedral', $diocese['cathedral'] );
                     update_post_meta( get_the_id(), 'founded', $diocese['founded'] );
                    
                     $update_diocese = array(
                        'ID'           => get_the_id(),
                        'post_content' => ! empty( $diocese['content'] ) ? $diocese['content'] : '',
                    );
                  
                    wp_update_post( $update_diocese );
                    wp_set_object_terms( get_the_id(), $diocese['province'], 'province' );
                    WP_CLI::warning( "Diocese {$diocese['title']} updated." );
                    continue;
                 }

                $dioceseNew = array (
                    'post_type' => 'diocese',
                    'post_title' => 'The Diocese of ' . $diocese['title'],
                    'post_content' => ! empty( $diocese['content'] ) ? $diocese['content'] : '',
                    'post_status' => $diocese['title'] === 'London' ? 'publish' : 'draft',
                    'comment_status' => 'closed',   // if you prefer
                    'ping_status' => 'closed',      // if you prefer
                    'post_parent' => 0,
                    'tax_input' => [],
                    'meta_input' => [
                        'website' => strpos( $diocese['website'], 'http' ) === false ? 'https://' . $diocese['website'] : $diocese['website'],
                        'cathedral' => $diocese['cathedral'],
                        'founded' => $diocese['founded'],
                        'diocese' => strtolower( $diocese['title'] )
                    ]
                );
                $diocese_id = wp_insert_post($dioceseNew);
                wp_set_object_terms( $diocese_id, $diocese['province'], 'province' );
                WP_CLI::warning( "Diocese {$diocese['title']} inserted." );
            }

        }

		if ( ! empty( $assoc_args['churches'] ) ) {
            $csv = $this->get_csv_file( 'churches' );
            
            foreach( $csv as $church ) {

                $args = array(
                    'post_type' => array( 'diocese' ),
                    'post_status' => array('publish', 'draft'),
                    'meta_query' => array(
                        array(
                            'key' => 'diocese',
                            'value' => strtolower( $church['diocese'] ),
                            'compare' => '=',
                        )
                    )
                );
                $query = new \WP_Query($args);
                

                if ( $query->have_posts() ) {
                    $query->the_post();
                    $diocese_id = get_the_id();
                } else {
                    WP_CLI::warning("{$church['diocese']} does not exist. Skipped.");
                    continue;
                }

                if ( intval( $church['church_id'] ) === 0 ) {
                    WP_CLI::warning("Wrong id");
                    continue;
                }
                
                $args = array(
                    'post_type' => array( 'church' ),
                    'post_status' => array('publish', 'draft'),
                    'meta_query' => array(
                        array(
                            'key' => 'church_id',
                            'value' => $church['church_id'],
                            'compare' => '=',
                        )
                    )
                );
                $query = new \WP_Query($args);

                if ( $query->have_posts() ) {
                    WP_CLI::warning( "Diocese {$church['church_id']} already added. Updating." );

                    $query->the_post();

                
                    update_post_meta( get_the_id(), 'telephone', $church['telephone'] );
                    update_post_meta( get_the_id(), 'contact_email', $church['contact_email'] );
                    update_post_meta( get_the_id(), 'website', $church['website'] );
                    update_post_meta( get_the_id(), 'church_wardens', $church['church_wardens'] );
                    update_post_meta( get_the_id(), 'priest', $church['priest'] );
                    update_post_meta( get_the_id(), 'parish', $church['parish'] );
                    update_post_meta( get_the_id(), 'town', $church['town'] );
                    // deanery
                    // diocese
                    update_post_meta( get_the_id(), 'coordinates', $church['coordinates'] );
                    update_post_meta( get_the_id(), 'address', $church['address'] );

                    $update_church = array(
                        'ID'           => get_the_id(),
                        'post_content' => ! empty( $church['content'] ) ? $church['content'] : '',
                        'post_title' => $church['name'],
                        'post_parent' => $diocese_id
                    );
                  
                    wp_update_post( $update_church );

                    if ( ! empty( $church['deanery']) ) {
                        wp_set_object_terms( get_the_id(), $church['deanery'], 'deanery' );
                    }
                    WP_CLI::warning( "Diocese {$church['church_id']} updated." );
        
                }

                $churchNew = array (
                    'post_type' => 'church',
                    'post_title' => $church['name'],
                    'post_content' => ! empty( $church['content'] ) ? $church['content'] : '',
                    'post_status' => 'draft',
                    'comment_status' => 'closed',   // if you prefer
                    'ping_status' => 'closed',      // if you prefer
                    'post_parent' => 0,
                    'tax_input' => [],
                    'meta_input' => [
                        'telephone' => $church['telephone'],
                        'contact_email' => $church['contact_email'],
                        'website' => $church['website'],
                        'church_wardens' => $church['church_wardens'],
                        'priest' => $church['priest'],
                        'parish' => $church['parish'],
                        'church_id' => $church['church_id'],
                        'town' => $church['town'],
                        'coordinates' => $church['coordinates'],
                        'address' => $church['address'],
                    ]
                );
                $church_id = wp_insert_post($churchNew);
                if ( ! empty( $church['deanery']) ) {
                    wp_set_object_terms( $church_id, $church['deanery'], 'deanery' );
                }
                WP_CLI::warning( "Church {$church['name']} inserted." );
            }
                
        }


		/*$items = [
			[
				'1' => '',
				'2' => '',
			],
		];

		Utils\format_items( 'table', $items, [ '1', '2' ] );*/
    }
    
    function get_csv_file( $type ) {
        $filename = plugin_dir_path( __FILE__ );
        
        $csv = new \parseCSV( $filename . "../../data/{$type}.csv" );
        return $csv->data;
    }
}
