<?php
// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom WP-CLI commands for the JW Event Manager plugin.
 */
class JW_Event_CLI {

    /**
     * Generates dummy events for testing purposes.
     *
     * ## OPTIONS
     *
     * [--count=<number>]
     * : The number of events to generate.
     * ---
     * default: 5
     * ---
     *
     * ## EXAMPLES
     *
     * wp jw_event generate --count=10
     *
     * @when after_wp_load
     */
    public function generate( $args, $assoc_args ) {
        $count = isset( $assoc_args['count'] ) ? intval( $assoc_args['count'] ) : 5;

        WP_CLI::line( "Generating {$count} events with dummy meta data..." );

        $dummy_locations = array( 'New York City', 'San Francisco', 'London, UK', 'Online', 'Paris, France', 'Istanbul, Turkey' );
        $dummy_types     = array( 'Conference', 'Webinar', 'Meetup', 'Workshop' );

        for ( $i = 1; $i <= $count; $i++ ) {
            $post_id = wp_insert_post( array(
                'post_title'   => 'Sample Event ' . $i . ' - ' . wp_generate_password( 6, false ),
                'post_content' => 'This is an automatically generated event for testing purposes. It helps evaluate the plugin functionality.',
                'post_status'  => 'publish',
                'post_type'    => 'jw_event',
            ) );

            if ( ! is_wp_error( $post_id ) ) {
                $random_timestamp = time() + rand( 0, 60 * 24 * 60 * 60 );
                $random_date      = date( 'Y-m-d', $random_timestamp );
                $random_location  = $dummy_locations[ array_rand( $dummy_locations ) ];
                $random_type      = $dummy_types[ array_rand( $dummy_types ) ];

                // Update meta data
                update_post_meta( $post_id, '_jw_event_date', $random_date );
                update_post_meta( $post_id, '_jw_event_location', $random_location );

                // Assign a random taxonomy term
                wp_set_object_terms( $post_id, $random_type, 'jw_event_type' );

                WP_CLI::success( "Event created ID: {$post_id} | Type: {$random_type} | Date: {$random_date}" );
            } else {
                WP_CLI::warning( "Failed to create an event." );
            }
        }

        WP_CLI::success( "Process completed! {$count} events were generated successfully." );
    }
}