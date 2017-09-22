<?php
/**
 * Plugin Name:     Diocese
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Manage Dioceses and their respective Churches
 * Author:          ABijedic
 * Author URI:      YOUR SITE HERE
 * Text Domain:     diocese
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Diocese
 */

namespace Diocese;

require __DIR__ . '/inc/post-types/church.php';
require __DIR__ . '/inc/post-types/diocese.php';

require __DIR__ . '/inc/namespace.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require __DIR__ . '/lib/parsecsv-for-php/parsecsv.lib.php';
    require __DIR__ . '/inc/utilities/class-command.php';
}

bootstrap();