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

// Sécurité absolue : Empêcher l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Définir les constantes pour faciliter les chemins d'accès
define( 'JW_EVENT_MANAGER_VERSION', '1.0.0' );
define( 'JW_EVENT_MANAGER_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Classe principale d'amorçage (Bootstrap) du plugin
 */
class JW_Event_Manager_Init {

    public function __construct() {
        $this->load_dependencies();
        // Hook d'activation pour rafraîchir les permaliens
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
    }

    private function load_dependencies() {
        // Charger la classe du Custom Post Type
        require_once JW_EVENT_MANAGER_PATH . 'includes/class-jw-event-cpt.php';
        
        // Instancier et accrocher à l'initialisation de WordPress
        $cpt = new JW_Event_CPT();
        add_action( 'init', array( $cpt, 'register_cpt_and_taxonomies' ) );
    }

    public function activate() {
        // Régénérer les URLs au moment de l'activation pour éviter les erreurs 404 en front-end
        require_once JW_EVENT_MANAGER_PATH . 'includes/class-jw-event-cpt.php';
        $cpt = new JW_Event_CPT();
        $cpt->register_cpt_and_taxonomies();
        flush_rewrite_rules();
    }
}

// Lancer le plugin
new JW_Event_Manager_Init();