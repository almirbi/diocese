<?php

namespace Diocese;

use Diocese\Post_Types;
use WP_CLI;

function bootstrap() {
    
    
    Post_Types\Church::bootstrap();
    Post_Types\Diocese::bootstrap();

    // WP-Cli
	if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( __NAMESPACE__ . '\\Utilities\\Command' ) ) {
		WP_CLI::add_command( 'diocese', __NAMESPACE__ . '\\Utilities\\Command' );
	}

    
}
