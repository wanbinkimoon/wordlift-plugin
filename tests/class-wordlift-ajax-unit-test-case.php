<?php
/**
 * This file provides a Wordlift_Ajax_Unit_Test_Case abstract class to support
 * WP's AJAX testing while configuring Wordlift.
 *
 * @since   3.8.0
 * @package Wordlift
 */

require_once( 'functions.php' );

/**
 * Define the {@link Wordlift_Ajax_Unit_Test_Case} class.
 *
 * @since 3.8.0
 */
abstract class Wordlift_Ajax_Unit_Test_Case extends WP_Ajax_UnitTestCase {

	function setUp() {
		parent::setUp();

		// Default behaviour: push entities to the remote Linked Data store.
		Wordlift_Unit_Test_Case::turn_off_entity_push();

		// Configure WordPress with the test settings.
		wl_configure_wordpress_test();

	}

	/**
	 * Mimic the ajax handling of admin-ajax.php
	 * Capture the output via output buffering, and if there is any, store
	 * it in $this->_last_message.
	 *
	 * @param string      $action
	 * @param string|null $body The http request body.
	 */
	protected function _handleAjax( $action, $body = null ) {

		// Start output buffering
		ini_set( 'implicit_flush', false );
		ob_start();

		// Build the request
		$_POST['action'] = $action;
		$_GET['action']  = $action;
		$_REQUEST        = array_merge( $_POST, $_GET );

		// Call the hooks
		do_action( 'admin_init' );
		do_action( 'wp_ajax_' . $_REQUEST['action'], $body );

		// Save the output
		$buffer = ob_get_clean();
		if ( ! empty( $buffer ) ) {
			$this->_last_response = $buffer;
		}
	}

}
