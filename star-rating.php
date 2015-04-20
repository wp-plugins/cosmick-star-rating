<?php
/**
 * Plugin Name: Cosmick Star Rating
 * Plugin URI: http://cosmicktechnologies.com/
 * Description: Google Organic Search Rich Snippets for Reviews and Rating.
 * Version: 1.0.9
 * Author: Cosmick Technologies
 * Author URI: http://cosmicktechnologies.com/
 * License: GPL2
 */

global $wpdb;
define('CSRVERSION', '1.0.9');
define('CSRVOTESTBL', $wpdb->prefix . 'csr_votes');
define('YASRVOTESTBL', $wpdb->prefix . 'yasr_votes');

register_activation_hook(__FILE__, 'csr_install');
register_deactivation_hook(__FILE__, 'csr_uninstall');
add_action('wp_enqueue_scripts', 'csr_include_scripts');
add_action('admin_enqueue_scripts', 'csr_include_scripts');

function csr_install() {

    $csr_votes_table = CSRVOTESTBL;
    $sql_yasr_votes_table = "CREATE TABLE IF NOT EXISTS $csr_votes_table (
  		id bigint(20) NOT NULL AUTO_INCREMENT,
  		post_id bigint(20) NOT NULL,
 	 	reviewer_id bigint(20) NOT NULL,
 	 	overall_rating decimal(2,1) NOT NULL,
 	 	number_of_votes bigint(20) NOT NULL,
  		sum_votes decimal(11,1) NOT NULL,
  		review_type VARCHAR(10),
 		PRIMARY KEY  (id),
 		UNIQUE KEY post_id (post_id)	
	);";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql_yasr_votes_table);

    //Makesure the posttype registered before flush rewrite
    csr_post_reviewinit();
    csr_rewriterules();
}

function csr_uninstall() {
    csr_rewriterules();
}

function csr_include_scripts() {
    wp_enqueue_script(
            'jqrate', plugins_url('/asset/jRate.min.js', __FILE__), array('jquery'), CSRVERSION, true
    );
    wp_enqueue_script(
            'jqmain', plugins_url('/asset/main.js', __FILE__), array('jquery', 'jqrate'), CSRVERSION, true
    );    
    wp_enqueue_style( 'csr-style', plugins_url('/asset/csr.css', __FILE__), array(), CSRVERSION );
}

function csr_rewriterules() {
    flush_rewrite_rules();
}

require ( dirname(__FILE__) . '/star-rating-post.php' );
require ( dirname(__FILE__) . '/star-rating-widget.php' );
require ( dirname(__FILE__) . '/star-rating-settings.php' );
