<?php
// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class handling the public-facing side of the site (Templates & Shortcodes).
 */
class JW_Event_Public {

    /**
     * Initialize hooks.
     */
    public function __construct() {
        // Override default theme templates with our plugin templates.
        add_filter( 'template_include', array( $this, 'load_event_templates' ) );
        
        // Register the shortcode.
        add_shortcode( 'jw_events', array( $this, 'render_events_shortcode' ) );
    }

    /**
     * Load custom templates for Single Event and Event Archive.
     *
     * @param string $template The path of the template to include.
     * @return string Modified template path.
     */
    public function load_event_templates( $template ) {
        if ( is_singular( 'jw_event' ) ) {
            $plugin_template = JW_EVENT_MANAGER_PATH . 'templates/single-jw_event.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }

        if ( is_post_type_archive( 'jw_event' ) ) {
            $plugin_template = JW_EVENT_MANAGER_PATH . 'templates/archive-jw_event.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }

        return $template;
    }

    /**
     * Render the [jw_events] shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public function render_events_shortcode( $atts ) {
        // We will build the filtering logic here in the next step!
        $args = array(
            'post_type'      => 'jw_event',
            'posts_per_page' => 5,
            'post_status'    => 'publish',
        );

        $query = new WP_Query( $args );
        
        ob_start();

        if ( $query->have_posts() ) {
            echo '<div class="jw-events-list">';
            while ( $query->have_posts() ) {
                $query->the_post();
                $date     = get_post_meta( get_the_ID(), '_jw_event_date', true );
                $location = get_post_meta( get_the_ID(), '_jw_event_location', true );
                
                echo '<div class="jw-event-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;">';
                echo '<h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
                if ( $date ) echo '<p><strong>Date:</strong> ' . esc_html( $date ) . '</p>';
                if ( $location ) echo '<p><strong>Location:</strong> ' . esc_html( $location ) . '</p>';
                echo '</div>';
            }
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__( 'No events found.', 'jw-event-manager' ) . '</p>';
        }

        return ob_get_clean();
    }
}