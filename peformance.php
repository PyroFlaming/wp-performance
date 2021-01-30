<?php
/*
Plugin Name: Performance optimizer
Plugin URI:  https://-----.com
Description: Optimize your performance for your users also.
Version:     v0.1
Author:      Lazy TEAM
License:     GPL2
Text Domain: wporg
Domain Path: /languages
*/
define('BASE_PATH', plugin_dir_path(__FILE__));
define('BASE_URL', plugin_dir_url(__FILE__));

require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use PerformanceBoost\Booster;

/**
 * Output Buffering
 *
 * Buffers the entire WP process, capturing the final output for manipulation.
 */

ob_start();

add_action('shutdown', function() {
    $final = '';

    // We'll need to get the number of ob levels we're in, so that we can iterate over each, collecting
    // that buffer's output into the final output.
    $levels = ob_get_level();

    for ($i = 0; $i < $levels; $i++) {
        $final .= ob_get_clean();
    }

    // Apply any filters to the final output
    echo apply_filters('booster_filter_output_content', $final);
}, 0);
 
new Booster();