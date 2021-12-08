<?php

namespace WPSCPT;

class Meta_Box{
	private static $cpts;

	public static function init(){
		add_action( 'wp_loaded', [ __CLASS__, 'after_wp_loaded_fully' ] );
	}

	public static function after_wp_loaded_fully() {
		// get all the custom post types
		$cpts = get_post_types( [ '_builtin' => false ], 'names' );
		foreach ( $cpts as $cpt ) {
			self::$cpts[] = $cpt;
		}

		add_action( 'add_meta_boxes', [ __CLASS__, 'add_metabox_to_cpt' ] );
		add_action( 'save_post', [ __CLASS__, 'save_restrict_cpt_meta_value' ] );
	}

	public static function add_metabox_to_cpt() {
		add_meta_box(
			'_restrict_cpt',
			__( 'Restrict cpt for logged in user only', 'textdomain' ),
			[__CLASS__, 'render_checkbox_for_logged_restriction'],
			self::$cpts
		);
	}

	public static function render_checkbox_for_logged_restriction( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'restrict-cpt-nonce', 'restrict_cpt_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$value = get_post_meta( $post->ID, '_restrict_cpt', true );

		// Display the form, using the current value.
		?>

		<input type="checkbox" id="restrict-cpt" name="restrict-cpt" value="1" <?php checked($value, true, true); ?> />
		<label for="restrict-cpt"><?php printf( __( 'Require Login to View This.', 'wp-secure-cpt' ), '' ); ?></label>
		<?php
	}

	public static function save_restrict_cpt_meta_value( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['restrict_cpt_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['restrict_cpt_nonce'], 'restrict-cpt-nonce' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && in_array( $_POST['post_type'], self::$cpts ) ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
			}
		}

		/* OK, it's safe for us to save the data now. */

		// Sanitize user input.
		$value = isset( $_POST['restrict-cpt'] ) ? filter_var( $_POST['restrict-cpt'], FILTER_SANITIZE_NUMBER_INT ) : 0;

		// Update the meta field in the database.
		update_post_meta( $post_id, '_restrict_cpt', $value );
	}
}
