<?php
// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class handling the custom meta boxes for the Event post type.
 */
class JW_Event_Meta_Boxes {

    /**
     * Initialize hooks.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_box_data' ) );
    }

    /**
     * Add the meta box to the Event post type.
     */
    public function add_meta_boxes() {
        add_meta_box(
            'jw_event_details',
            __( 'Event Details', 'jw-event-manager' ),
            array( $this, 'render_meta_box_content' ),
            'jw_event', // Target post type
            'normal',
            'high'
        );
    }

    /**
     * Render the meta box content (HTML fields).
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content( $post ) {
        // Add a nonce field for security validation.
        wp_nonce_field( 'jw_event_save_data', 'jw_event_meta_box_nonce' );

        // Retrieve existing values from the database.
        $event_date     = get_post_meta( $post->ID, '_jw_event_date', true );
        $event_location = get_post_meta( $post->ID, '_jw_event_location', true );

        // Output the HTML for the fields.
        echo '<div style="padding: 10px 0;">';
        
        echo '<p>';
        echo '<label for="jw_event_date" style="display:block; margin-bottom:5px;"><strong>' . esc_html__( 'Event Date:', 'jw-event-manager' ) . '</strong></label>';
        echo '<input type="date" id="jw_event_date" name="jw_event_date" value="' . esc_attr( $event_date ) . '" style="width: 100%; max-width: 400px;" />';
        echo '</p>';

        echo '<p>';
        echo '<label for="jw_event_location" style="display:block; margin-bottom:5px;"><strong>' . esc_html__( 'Event Location:', 'jw-event-manager' ) . '</strong></label>';
        echo '<input type="text" id="jw_event_location" name="jw_event_location" value="' . esc_attr( $event_location ) . '" placeholder="e.g., New York City, Online" style="width: 100%; max-width: 400px;" />';
        echo '</p>';

        echo '</div>';
    }

    /**
     * Save the meta box data securely.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save_meta_box_data( $post_id ) {
        // 1. Check if our nonce is set and verify it.
        if ( ! isset( $_POST['jw_event_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['jw_event_meta_box_nonce'], 'jw_event_save_data' ) ) {
            return;
        }

        // 2. Ignore autosaves.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // 3. Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'jw_event' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        // 4. Sanitize user input (Crucial step for security).
        $event_date     = isset( $_POST['jw_event_date'] ) ? sanitize_text_field( wp_unslash( $_POST['jw_event_date'] ) ) : '';
        $event_location = isset( $_POST['jw_event_location'] ) ? sanitize_text_field( wp_unslash( $_POST['jw_event_location'] ) ) : '';

        // 5. Update the meta fields in the database.
        update_post_meta( $post_id, '_jw_event_date', $event_date );
        update_post_meta( $post_id, '_jw_event_location', $event_location );
    }
}