<?php
/**
 * Plugin Name:     WP Secure CPT
 * Plugin URI:      https://codeware.io
 * Description:
 * Author:          Codeware Team
 * Author URI:      https://codeware.io
 * Text Domain:     wp-secure-cpt
 * Requires PHP: 5.4
 * Requires at least: 5.0
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         CODEWARE
 */

use WPSCPT\Meta_Box;

// defined required constants
define( 'WPSCPT_URL', plugins_url( '', __FILE__ ) );
define( 'WPSCPT_TEXT_DOMAIN', 'codeware-plugin-scaffold' );
define( 'WPSCPT_VERSION', '1.0.0');

require_once __DIR__ . '/inc/includes.php';

// init plugin
Meta_Box::init();

add_action( 'template_redirect', function() {
	if ( ! is_singular() ) return;
	if ( is_user_logged_in() ) return;
	if ( ! get_post_meta( get_the_ID(), '_restrict_cpt', true  ) ) return;

	$permalink = get_permalink();
	wp_redirect( wp_login_url( $permalink ) );
	die();
} );
