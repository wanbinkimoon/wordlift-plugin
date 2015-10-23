<?php
require_once 'functions.php';
require_once( dirname( __FILE__ ) . '/../src/admin/WL_Metabox/WL_Metabox.php' );

/**
 * Class MetaboxTest
 */
class MetaboxTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();

        // Configure WordPress with the test settings.
        wl_configure_wordpress_test();

        // Reset data on the remote dataset.
        rl_empty_dataset();

        // Empty the blog.
        wl_empty_blog();
    }

    /*
     * Test the WL_Metabox obj is built properly
     */
    function testWL_Metabox_constructor() {
       
        $metabox = new WL_Metabox();
        
        // Verify the object has hooked correctly (default priority for hooks is 10)
        $this->assertEquals( 10, has_action( 'add_meta_boxes', array( $metabox, 'add_main_metabox' ) ) );
        $this->assertEquals( 10, has_action( 'wl_linked_data_save_post', array( $metabox, 'save_form_data' ) ) );
    }
    
    /*
     * Test the WL_Metabox fields are built properly
     */
    function testWL_Metabox_fields_instantiation() {
    
        // Create an entity of type Place
        $place_id = wl_create_post( '', 'p', 'A place', 'publish', WL_ENTITY_TYPE_NAME );
        wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );
        
        // Create Metabox and its Fields
        $metabox = new WL_Metabox();
        $metabox->instantiate_fields( $place_id );
        
        $fields = wl_entity_taxonomy_get_custom_fields( $place_id );
        $sameAs_field = array( 'sameas' => array( WL_CUSTOM_FIELD_SAME_AS => $fields[WL_CUSTOM_FIELD_SAME_AS] ) );
        $sameAs_field_obj = new WL_Metabox_Field_sameas( $sameAs_field );
        $coordinates_field = array( 'coordinates' => array(
                                        WL_CUSTOM_FIELD_GEO_LATITUDE => $fields[WL_CUSTOM_FIELD_GEO_LATITUDE],
                                        WL_CUSTOM_FIELD_GEO_LONGITUDE => $fields[WL_CUSTOM_FIELD_GEO_LONGITUDE],
        ) );
        $coordinates_field_obj = new WL_Metabox_Field_coordinates( $sameAs_field );
        $address_field = array( WL_CUSTOM_FIELD_ADDRESS => $fields[WL_CUSTOM_FIELD_ADDRESS] );
        $address_field_obj = new WL_Metabox_Field( $address_field );
        
        // Verify the correct fields have been built.
        $this->assertEquals( $address_field_obj, $metabox->fields[0] );
        $this->assertEquals( $coordinates_field_obj, $metabox->fields[1] );
        $this->assertEquals( $sameAs_field_obj, $metabox->fields[2] );
    }
    
    /*
     * Test the WL_Metabox field grouping mechanism
     */
    function testWL_Metabox_group_properties_by_input_field() {
        
        // Create an entity of type Place
        $place_id = wl_create_post( '', 'p', 'A place', 'publish', WL_ENTITY_TYPE_NAME );
        wl_set_entity_main_type( $place_id, 'http://schema.org/Place' );
        
        // Create Metabox and its Fields
        $metabox = new WL_Metabox();
        $entity_type = wl_entity_taxonomy_get_custom_fields( $place_id );
        $simple_and_grouped_fields = $metabox->group_properties_by_input_field( $entity_type );
        
        // Verify properties have been distinguished (single vs special/grouped)
        $this->assertArrayHasKey( WL_CUSTOM_FIELD_ADDRESS, $simple_and_grouped_fields[0] );     // Simple field
        $this->assertArrayHasKey( 'coordinates', $simple_and_grouped_fields[1] );               // Special/grouped Field
        $this->assertArrayHasKey( 'sameas', $simple_and_grouped_fields[1] );                    // Special/grouped Field
    }
    
    /*
     * Test the WL_Metabox $_POST saving mechanism
     */
    function testWL_Metabox_save_form_data() {
        // TODO
        //wl_write_log( 'piedo' );
        //wl_write_log( $simple_and_grouped_fields );
    }
    
    
    /*************************************************************************
     * Test Fields. The following are about the WL_Metabox_Filed_xxx classes. *
     *************************************************************************/
    
    // TODO: Test base class WL_Metabox_Field :(
    
    
    /*
     * Test the WL_Metabox_Field_uri obj is built properly
     */
    function testWL_Metabox_Field_uri_constructor() {
        
        // Build a single Field
        $author_custom_field = $this->getSampleCustomField( WL_DATA_TYPE_URI );
        $field = new WL_Metabox_Field_uri( $author_custom_field );
        
        // Verify Field has been built correctly
        $this->assertEquals( WL_CUSTOM_FIELD_AUTHOR, $field->meta_name );
        $this->assertEquals( 'http://schema.org/author', $field->predicate );
        $this->assertEquals( 'author', $field->label );
        $this->assertEquals( WL_DATA_TYPE_URI, $field->expected_wl_type );
        $this->assertEquals( array('Person', 'Organization'), $field->expected_uri_type );
        $this->assertEquals( INF, $field->cardinality );
        
        // Stress the constructor with invalid data
        $field = new WL_Metabox_Field_uri( null );
        $emptyField = array(
            'meta_name' => null,
            'raw_custom_field' => null,
            'predicate' => null,
            'label' => null,
            'expected_wl_type' => null,
            'expected_uri_type' => null,
            'cardinality' => null,
            'data' => null
        );
        $this->assertEquals( $emptyField, (array) $field );
    }
    
    /*
     * Test the WL_Metabox_Field_uri obj print correctly html
     */
    function testWL_Metabox_Field_uri_html() {
        
        $args = $this->getSampleCustomField( WL_DATA_TYPE_URI );
        
        $field = new WL_Metabox_Field_uri( $args );
        
        // verify html methods
        $html = $field->html();
        
        $this->assertContains( 'class="wl-metabox"', $html );                                       // CSS class
        $this->assertContains( 'data-cardinality="INF"', $html );                                   // Cardinality
        $this->assertContains( 'data-expected-types="Person,Organization"', $html );                // Expected types
        $this->assertContains( 'name="wl_metaboxes[wl_author][]"', $html );                         // $_POST array
        $this->assertContains( 'button class="wl-remove-input"', $html );                           // Remove button
        $this->assertContains( 'button class="wl-add-input"', $html );                              // Add button
    }
    
    /*
     * Test the WL_Metabox_Field loads and saves data correcly
     */
    function testWL_Metabox_Field_uri_data() {
        
        // Create an entity of type Person
        $person_id = wl_create_post( '', 'p', 'A person', 'publish', WL_ENTITY_TYPE_NAME );
        wl_set_entity_main_type( $person_id, 'http://schema.org/Person' );
        wl_schema_set_value( $person_id, 'author', 43 );
        
        // Create an entity of type CreativeWork
        $creative_work_id = wl_create_post( '', 'cw', 'A creative work', 'publish', WL_ENTITY_TYPE_NAME );
        wl_set_entity_main_type( $creative_work_id, 'http://schema.org/CreativeWork' );
        wl_schema_set_value( $creative_work_id, 'author', $person_id );     // Set authorship
        
        // Create fake context
        global $post;
        $post = get_post( $creative_work_id );
        
        // Build a single Field
        $author_custom_field = $this->getSampleCustomField( WL_DATA_TYPE_URI );
        $field = new WL_Metabox_Field_uri( $author_custom_field );
        
        // Verify data is loaded correctly from DB
        $field->get_data();
        $this->assertEquals( array( $person_id ), $field->data );
        
        // Save new DB values (third value is invalid and fourth is a new entity)
        $field->save_data( array( $person_id, 'http://some-triplestore/person2', null, 'Annibale' ) );
        
        // Verify data is loaded correctly from DB
        $new_entity = get_page_by_title( 'Annibale', OBJECT, WL_ENTITY_TYPE_NAME );
        $field->get_data();
        $this->assertEquals( array( $person_id, 'http://some-triplestore/person2', $new_entity->ID ), $field->data );
        
    }
    
    function getSampleCustomField( $type ){
        
        if( $type == WL_DATA_TYPE_URI ){
            return array(
                WL_CUSTOM_FIELD_AUTHOR => array(
                    'predicate' => 'http://schema.org/author',
                    'type' => WL_DATA_TYPE_URI,
                    'export_type' => 'http://schema.org/Person',
                    'constraints' => array(
                        'uri_type' => array('Person', 'Organization'),
                        'cardinality' => INF
                    )
                ),
            );
        }
        
        if( $type == WL_DATA_TYPE_STRING ){
            return array(
                // TODO
            );
        }
        
        if( $type == WL_DATA_TYPE_DATE ){
            return array(
                // TODO
            );
        }
    }
}