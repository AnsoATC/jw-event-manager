<?php
// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class handling the customization of the Event post type admin list view.
 */
class JW_Event_Admin_List {

    /**
     * Initialize hooks.
     */
    public function __construct() {
        add_filter( 'manage_jw_event_posts_columns', array( $this, 'add_custom_columns' ) );
        add_action( 'manage_jw_event_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
        add_filter( 'manage_edit-jw_event_sortable_columns', array( $this, 'register_sortable_columns' ) );
        add_action( 'pre_get_posts', array( $this, 'sort_custom_columns' ) );
    }

    /**
     * Define custom columns for the Event list.
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public function add_custom_columns( $columns ) {
        $new_columns = array();
        
        foreach ( $columns as $key => $title ) {
            if ( 'date' === $key ) {
                // Insert our custom columns right before the standard 'date' column.
                $new_columns['event_location'] = __( 'Location', 'jw-event-manager' );
                $new_columns['event_date']     = __( 'Event Date', 'jw-event-manager' );
            } else {
                $new_columns[$key] = $title;
            }
        }
        
        // Remove the default publication date column to avoid confusion.
        unset( $new_columns['date'] );
        
        return $new_columns;
    }

    /**
     * Render the content for custom columns.
     *
     * @param string $column  The column slug.
     * @param int    $post_id The current post ID.
     */
    public function render_custom_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'event_date':
                $date = get_post_meta( $post_id, '_jw_event_date', true );
                echo ! empty( $date ) ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) : '—';
                break;

            case 'event_location':
                $location = get_post_meta( $post_id, '_jw_event_location', true );
                echo ! empty( $location ) ? esc_html( $location ) : '—';
                break;
        }
    }

    /**
     * Register sortable columns.
     */
    public function register_sortable_columns( $columns ) {
        $columns['event_date'] = 'event_date';
        return $columns;
    }

    /**
     * Handle the sorting logic for the Event Date column.
     */
    public function sort_custom_columns( $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        if ( 'event_date' === $query->get( 'orderby' ) ) {
            $query->set( 'orderby', 'meta_value' );
            $query->set( 'meta_key', '_jw_event_date' );
        }
    }
}