<?php
// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class handling RSVP submissions and Email Notifications.
 */
class JW_Event_Communications {

    public function __construct() {
        // Form processing hooks (Logged in and out users)
        add_action( 'admin_post_nopriv_jw_submit_rsvp', array( $this, 'handle_rsvp_submission' ) );
        add_action( 'admin_post_jw_submit_rsvp', array( $this, 'handle_rsvp_submission' ) );
        
        // Notification hooks
        add_action( 'transition_post_status', array( $this, 'send_event_notifications' ), 10, 3 );
    }

    /**
     * Process the RSVP form submission securely.
     */
    public function handle_rsvp_submission() {
        if ( ! isset( $_POST['jw_rsvp_nonce'] ) || ! wp_verify_nonce( $_POST['jw_rsvp_nonce'], 'jw_submit_rsvp_action' ) ) {
            wp_die( 'Security check failed. Please try again.' );
        }

        $event_id   = isset( $_POST['event_id'] ) ? intval( $_POST['event_id'] ) : 0;
        $user_name  = isset( $_POST['rsvp_name'] ) ? sanitize_text_field( wp_unslash( $_POST['rsvp_name'] ) ) : '';
        $user_email = isset( $_POST['rsvp_email'] ) ? sanitize_email( wp_unslash( $_POST['rsvp_email'] ) ) : '';

        if ( $event_id && is_email( $user_email ) && ! empty( $user_name ) ) {
            $attendees = get_post_meta( $event_id, '_jw_event_attendees', true );
            $attendees = is_array( $attendees ) ? $attendees : array();
            
            // Prevent duplicates
            $is_registered = false;
            foreach ( $attendees as $attendee ) {
                if ( $attendee['email'] === $user_email ) {
                    $is_registered = true;
                    break;
                }
            }

            if ( ! $is_registered ) {
                $attendees[] = array( 'name' => $user_name, 'email' => $user_email, 'time' => current_time( 'mysql' ) );
                update_post_meta( $event_id, '_jw_event_attendees', $attendees );
                
                // Send confirmation to the user
                $event_title = get_the_title( $event_id );
                wp_mail( $user_email, "RSVP Confirmed: {$event_title}", "Hello {$user_name},\n\nYour RSVP for '{$event_title}' is confirmed. We look forward to seeing you!" );
            }

            $redirect_url = add_query_arg( 'rsvp', 'success', get_permalink( $event_id ) );
            wp_redirect( esc_url_raw( $redirect_url ) );
            exit;
        }

        wp_die( 'Invalid form data.' );
    }

    /**
     * Send emails when an event is published or updated.
     */
    public function send_event_notifications( $new_status, $old_status, $post ) {
        if ( 'jw_event' !== $post->post_type ) {
            return;
        }

        // 1. Notify Admin when a NEW event is published
        if ( 'publish' === $new_status && 'publish' !== $old_status ) {
            $admin_email = get_option( 'admin_email' );
            $subject     = 'New Event Published: ' . $post->post_title;
            $message     = "A new event has been published.\n\nView it here: " . get_permalink( $post->ID );
            wp_mail( $admin_email, $subject, $message );
        } 
        // 2. Notify Attendees when an existing event is UPDATED
        elseif ( 'publish' === $new_status && 'publish' === $old_status ) {
            $attendees = get_post_meta( $post->ID, '_jw_event_attendees', true );
            if ( is_array( $attendees ) && ! empty( $attendees ) ) {
                $subject = 'Event Update: ' . $post->post_title;
                $message = "An event you are attending has been updated.\n\nCheck the latest details: " . get_permalink( $post->ID );
                foreach ( $attendees as $attendee ) {
                    wp_mail( $attendee['email'], $subject, $message );
                }
            }
        }
    }
}