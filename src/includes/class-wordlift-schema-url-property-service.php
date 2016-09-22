<?php

/**
 * This file defines a Property Service class for the schema:url property. Ideally
 * this class should extend an abstract class that provides a common infrastructure
 * for all properties (we'll get there).
 */

/**
 * The Wordlift_Schema_Url_Property_Service provides handling of the
 *
 * @since 3.6.0
 */
class Wordlift_Schema_Url_Property_Service extends Wordlift_Property_Service {

	/**
	 * The meta key used to store data for this property. We don't use wl_url to
	 * avoid potential confusion about other URLs.
	 *
	 * @since 3.6.0
	 */
	const META_KEY = 'wl_schema_url';

	/**
	 * A field has the following parameters:
	 *  * predicate: the RDF predicate as expanded URI,
	 *  * type: a WL type keyword (from the ones available in Wordlift_Schema_Service),
	 *  * export_type: an XSD data type,
	 *  * constraints: such as cardinality,
	 *  * metabox: the metabox class to use to display edit the field.
	 *  * validate: the validation function.
	 *
	 * @since 3.6.0
	 * @var array
	 */
	protected $params = array(
		'predicate'   => 'http://schema.org/url',
		'type'        => Wordlift_Schema_Service::DATA_TYPE_URI,
		'export_type' => 'xsd:anyURI',
		'constraints' => array(
			'cardinality' => INF
		),
		// Use the standard metabox for these URI (the URI metabox creates local entities).
		'metabox'     => 'WL_Metabox_Field'
	);

	/**
	 * Create a Wordlift_Schema_Url_Property_Service instance.
	 * @since 3.6.0
	 */
	public function __construct() {
		parent::__construct();

		// Finally listen for metadata requests for this field.
		$this->add_filter_get_post_metadata();

	}

	/**
	 * Get the schema:url value for the specified post/entity.
	 *
	 * @since 3.6.0
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array|NULL The schema:url value or NULL if not set.
	 */
	public function get( $post_id ) {

		// Get the schema:url values set in WP.
		$values = get_post_meta( $post_id, self::META_KEY, FALSE );

		// If the property has never been set, we set its default value the first
		// time to <permalink>.
		if ( 0 === count( $values ) ) {
			return array( '<permalink>' );
		}

		// If there's only one value and that value is empty, we return NULL, i.e.
		// variable not set.
		if ( 1 === count( $values ) && empty( $values[0] ) ) {
			return NULL;
		}

		// Finally return whatever values the editor set.
		return $values;
	}

	/**
	 * {@inheritdoc}
	 */
	public function sanitize( $value ) {

		// TODO: check that it's an URL or that is <permalink>

		return $value;
	}

	/**
	 * Get direct calls to read this meta and alter the response according to our
	 * own strategy, i.e. if a value has never been set for this meta, then return
	 * <permalink>.
	 *
	 * @since 3.6.0
	 *
	 * @param mixed $value The original value.
	 * @param int $object_id The post id.
	 * @param string $meta_key The meta key. We expect wl_schema_url or we return straight the value.
	 * @param bool $single Whether to return a single value.
	 *
	 * @return array|mixed|NULL|string
	 */
	public function get_post_metadata( $value, $object_id, $meta_key, $single ) {

		// It's not us, return the value.
		if ( self::META_KEY !== $meta_key ) {
			return $value;
		}

		$this->remove_filter_get_post_metadata();

		$new_value = $this->get( $object_id );

		$this->add_filter_get_post_metadata();

		// If we must return a single value, but we don't have a value, then return an empty string.
		if ( $single && ( is_null( $new_value ) || 0 === count( $new_value ) ) ) {
			return '';
		}

		// If we have a value and we need to return it as single, return the first value.
		if ( $single ) {
			return $new_value[0];
		}

		// Otherwise return the array.
		return $new_value;
	}

	private function add_filter_get_post_metadata() {

		add_filter( 'get_post_metadata', array(
			$this,
			'get_post_metadata'
		), 10, 4 );

	}

	private function remove_filter_get_post_metadata() {

		remove_filter( 'get_post_metadata', array(
			$this,
			'get_post_metadata'
		), 10 );

	}

}