<?php
/**
 * Plugin Name:     	WP Logged In Custom Posts
 * Plugin URI:
 * Description:
 * Author:          	Rajib Dey
 * Author URI:      	mailto:rajib.kuet07@gmail.com
 * Text Domain:     	wp-logged-in-posts
 * Requires PHP:			5.4
 * Requires at least: 5.0
 * Domain Path:     	/languages
 * Version:         	1.0.0
 */

use WPLICPT\Meta_Box;

// defined required constants
define( 'WPLICPT_URL', plugins_url( '', __FILE__ ) );
define( 'WPLICPT_VERSION', '1.0.0');

require_once __DIR__ . '/inc/includes.php';

// init plugin
Meta_Box::init();

add_action( 'template_redirect', function() {
	if ( ! is_singular() ) return;
	if ( is_user_logged_in() ) return;
	if ( ! get_post_meta( get_the_ID(), '_wplicpt_restrict_cpt', true  ) ) return;

	$permalink = get_permalink();
	wp_redirect( wp_login_url( $permalink ) );
	die();
} );
