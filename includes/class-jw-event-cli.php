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

        WP_CLI::line( "Generating {$count} events..." );

        for ( $i = 1; $i <= $count; $i++ ) {
            $post_id = wp_insert_post( array(
                'post_title'   => 'Sample Event ' . $i . ' - ' . wp_generate_password( 6, false ),
                'post_content' => 'This is an automatically generated event for testing purposes. It helps evaluate the plugin functionality.',
                'post_status'  => 'publish',
                'post_type'    => 'jw_event',
            ) );

            if ( ! is_wp_error( $post_id ) ) {
                WP_CLI::success( "Event created successfully with ID: {$post_id}" );
            } else {
                WP_CLI::warning( "Failed to create an event." );
            }
        }

        WP_CLI::success( "Process completed! {$count} events were generated successfully." );
    }
}