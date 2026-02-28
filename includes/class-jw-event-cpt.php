<?php
// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class handling the registration of the Event Custom Post Type and Taxonomy.
 */
class JW_Event_CPT {

    /**
     * Registers the "Event" post type and the "Event Type" taxonomy.
     * Hooked into 'init'.
     */
    public function register_cpt_and_taxonomies() {
        
        // 1. Register Taxonomy: Event Type.
        $tax_labels = array(
            'name'              => __( 'Event Types', 'jw-event-manager' ),
            'singular_name'     => __( 'Event Type', 'jw-event-manager' ),
            'search_items'      => __( 'Search Event Types', 'jw-event-manager' ),
            'all_items'         => __( 'All Event Types', 'jw-event-manager' ),
            'edit_item'         => __( 'Edit Event Type', 'jw-event-manager' ),
            'update_item'       => __( 'Update Event Type', 'jw-event-manager' ),
            'add_new_item'      => __( 'Add New Event Type', 'jw-event-manager' ),
            'new_item_name'     => __( 'New Event Type Name', 'jw-event-manager' ),
            'menu_name'         => __( 'Event Types', 'jw-event-manager' ),
        );

        $tax_args = array(
            'hierarchical'      => true,
            'labels'            => $tax_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true, // Required for Block Editor and REST API integration.
            'rewrite'           => array( 'slug' => 'event-type' ),
        );

        register_taxonomy( 'jw_event_type', array( 'jw_event' ), $tax_args );

        // 2. Register Custom Post Type: Event.
        $cpt_labels = array(
            'name'                  => __( 'Events', 'jw-event-manager' ),
            'singular_name'         => __( 'Event', 'jw-event-manager' ),
            'menu_name'             => __( 'Events', 'jw-event-manager' ),
            'add_new'               => __( 'Add New', 'jw-event-manager' ),
            'add_new_item'          => __( 'Add New Event', 'jw-event-manager' ),
            'edit_item'             => __( 'Edit Event', 'jw-event-manager' ),
            'view_item'             => __( 'View Event', 'jw-event-manager' ),
            'all_items'             => __( 'All Events', 'jw-event-manager' ),
            'search_items'          => __( 'Search Events', 'jw-event-manager' ),
            'not_found'             => __( 'No events found.', 'jw-event-manager' ),
            'not_found_in_trash'    => __( 'No events found in Trash.', 'jw-event-manager' ),
        );

        $cpt_args = array(
            'labels'             => $cpt_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'events' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'show_in_rest'       => true, // Exposes the CPT to the WordPress REST API.
        );

        register_post_type( 'jw_event', $cpt_args );
    }
}