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

        $dummy_locations = array( 'New York City', 'San Francisco', 'London, UK', 'Online/Virtual', 'Paris, France', 'Istanbul, Turkey' );

        for ( $i = 1; $i <= $count; $i++ ) {
            $post_id = wp_insert_post( array(
                'post_title'   => 'Sample Event ' . $i . ' - ' . wp_generate_password( 6, false ),
                'post_content' => 'This is an automatically generated event for testing purposes. It helps evaluate the plugin functionality.',
                'post_status'  => 'publish',
                'post_type'    => 'jw_event',
            ) );

            if ( ! is_wp_error( $post_id ) ) {
                // Generate a random date within the next 60 days
                $random_timestamp = time() + rand( 0, 60 * 24 * 60 * 60 );
                $random_date      = date( 'Y-m-d', $random_timestamp );
                
                // Pick a random location
                $random_location  = $dummy_locations[ array_rand( $dummy_locations ) ];

                // Update meta data
                update_post_meta( $post_id, '_jw_event_date', $random_date );
                update_post_meta( $post_id, '_jw_event_location', $random_location );

                WP_CLI::success( "Event created successfully with ID: {$post_id} | Date: {$random_date} | Loc: {$random_location}" );
            } else {
                WP_CLI::warning( "Failed to create an event." );
            }
        }

        WP_CLI::success( "Process completed! {$count} events were generated successfully." );
    }
}