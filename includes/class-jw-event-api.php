<?php
// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class handling the REST API integration for the Event post type.
 */
class JW_Event_API {

    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_rest_fields' ) );
    }

    /**
     * Register custom fields in the WP REST API responses.
     */
    public function register_rest_fields() {
        register_rest_field( 'jw_event', 'event_details', array(
            'get_callback'    => array( $this, 'get_event_meta' ),
            'update_callback' => null,
            'schema'          => null,
        ) );
    }

    /**
     * Callback to retrieve event meta data for the API.
     */
    public function get_event_meta( $object, $field_name, $request ) {
        return array(
            'date'     => get_post_meta( $object['id'], '_jw_event_date', true ),
            'location' => get_post_meta( $object['id'], '_jw_event_location', true ),
        );
    }
}