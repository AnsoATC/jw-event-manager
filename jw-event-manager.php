<?php
/**
 * Plugin Name:       JW Event Manager
 * Plugin URI:        https://anselmeatchogou.com
 * Description:       A custom plugin to manage events, RSVP, and notifications. Built for the Jack Westin technical assessment.
 * Version:           1.0.0
 * Author:            Anselme Atchogou
 * Text Domain:       jw-event-manager
 * Domain Path:       /languages
 */

// Prevent direct access to the file for security.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants for easy path referencing.
define( 'JW_EVENT_MANAGER_VERSION', '1.0.0' );
define( 'JW_EVENT_MANAGER_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Main Bootstrap Class for the Plugin.
 */
class JW_Event_Manager_Init {

    /**
     * Constructor: Load dependencies and hook into WordPress.
     */
    public function __construct() {
        $this->load_dependencies();
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
    }

    /**
     * Load required classes and initialize hooks.
     */
    private function load_dependencies() {
        // Load the Custom Post Type class.
        require_once JW_EVENT_MANAGER_PATH . 'includes/class-jw-event-cpt.php';
        
        $cpt = new JW_Event_CPT();
        add_action( 'init', array( $cpt, 'register_cpt_and_taxonomies' ) );

        // Load WP-CLI commands only if running in a CLI environment.
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            require_once JW_EVENT_MANAGER_PATH . 'includes/class-jw-event-cli.php';
            WP_CLI::add_command( 'jw_event', 'JW_Event_CLI' );
        }

        // Load Admin specific classes.
        if ( is_admin() ) {
            require_once JW_EVENT_MANAGER_PATH . 'admin/class-jw-event-meta-boxes.php';
            new JW_Event_Meta_Boxes();
        }

        // Load Public specific classes (Front-end).
        require_once JW_EVENT_MANAGER_PATH . 'public/class-jw-event-public.php';
        new JW_Event_Public();
    }

    /**
     * Plugin activation routine.
     * Flushes rewrite rules to ensure CPT URLs work correctly immediately.
     */
    public function activate() {
        require_once JW_EVENT_MANAGER_PATH . 'includes/class-jw-event-cpt.php';
        $cpt = new JW_Event_CPT();
        $cpt->register_cpt_and_taxonomies();
        
        flush_rewrite_rules();
    }
}

// Initialize the plugin.
new JW_Event_Manager_Init();