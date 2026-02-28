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
        add_filter( 'template_include', array( $this, 'load_event_templates' ) );
        add_shortcode( 'jw_events', array( $this, 'render_events_shortcode' ) );
    }

    /**
     * Load custom templates for Single Event and Event Archive.
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
     * Render the [jw_events] shortcode with Search and Filtering.
     */
    public function render_events_shortcode( $atts ) {
        // 1. Retrieve filter values from the URL safely.
        $search_query = isset( $_GET['jw_search'] ) ? sanitize_text_field( wp_unslash( $_GET['jw_search'] ) ) : '';
        $event_type   = isset( $_GET['jw_type'] ) ? sanitize_text_field( wp_unslash( $_GET['jw_type'] ) ) : '';
        $start_date   = isset( $_GET['jw_start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['jw_start_date'] ) ) : '';
        $end_date     = isset( $_GET['jw_end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['jw_end_date'] ) ) : '';

        // 2. Build the Query Arguments.
        $args = array(
            'post_type'      => 'jw_event',
            'posts_per_page' => 10,
            'post_status'    => 'publish',
        );

        // Filter by keyword.
        if ( ! empty( $search_query ) ) {
            $args['s'] = $search_query;
        }

        // Filter by taxonomy (Event Type).
        if ( ! empty( $event_type ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'jw_event_type',
                    'field'    => 'slug',
                    'terms'    => $event_type,
                ),
            );
        }

        // Filter by date range using Meta Query.
        if ( ! empty( $start_date ) || ! empty( $end_date ) ) {
            $args['meta_query'] = array();
            
            if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
                $args['meta_query'][] = array(
                    'key'     => '_jw_event_date',
                    'value'   => array( $start_date, $end_date ),
                    'compare' => 'BETWEEN',
                    'type'    => 'DATE',
                );
            } elseif ( ! empty( $start_date ) ) {
                $args['meta_query'][] = array(
                    'key'     => '_jw_event_date',
                    'value'   => $start_date,
                    'compare' => '>=',
                    'type'    => 'DATE',
                );
            } elseif ( ! empty( $end_date ) ) {
                $args['meta_query'][] = array(
                    'key'     => '_jw_event_date',
                    'value'   => $end_date,
                    'compare' => '<=',
                    'type'    => 'DATE',
                );
            }
        }

        $query = new WP_Query( $args );
        
        ob_start();

        // 3. Render the Search and Filter Form.
        $terms = get_terms( array( 'taxonomy' => 'jw_event_type', 'hide_empty' => false ) );
        ?>
        <form method="GET" action="" class="jw-events-filter-form" style="background: #f1f1f1; padding: 20px; margin-bottom: 20px; border-radius: 5px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <input type="text" name="jw_search" placeholder="<?php esc_attr_e( 'Search events...', 'jw-event-manager' ); ?>" value="<?php echo esc_attr( $search_query ); ?>" style="flex: 1; min-width: 150px; padding: 8px;">
            
            <select name="jw_type" style="flex: 1; min-width: 150px; padding: 8px;">
                <option value=""><?php esc_html_e( 'All Types', 'jw-event-manager' ); ?></option>
                <?php if ( ! is_wp_error( $terms ) ) : ?>
                    <?php foreach ( $terms as $term ) : ?>
                        <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $event_type, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <input type="date" name="jw_start_date" value="<?php echo esc_attr( $start_date ); ?>" style="flex: 1; min-width: 130px; padding: 8px;" title="<?php esc_attr_e( 'Start Date', 'jw-event-manager' ); ?>">
            <input type="date" name="jw_end_date" value="<?php echo esc_attr( $end_date ); ?>" style="flex: 1; min-width: 130px; padding: 8px;" title="<?php esc_attr_e( 'End Date', 'jw-event-manager' ); ?>">
            
            <button type="submit" style="background: #0073aa; color: #fff; border: none; padding: 10px 20px; cursor: pointer; border-radius: 3px;"><?php esc_html_e( 'Filter', 'jw-event-manager' ); ?></button>
            <a href="<?php echo esc_url( strtok( $_SERVER["REQUEST_URI"], '?' ) ); ?>" style="padding: 10px; text-decoration: none; color: #d63638;"><?php esc_html_e( 'Reset', 'jw-event-manager' ); ?></a>
        </form>
        <?php

        // 4. Render the Event Results.
        if ( $query->have_posts() ) {
            echo '<div class="jw-events-list">';
            while ( $query->have_posts() ) {
                $query->the_post();
                $date     = get_post_meta( get_the_ID(), '_jw_event_date', true );
                $location = get_post_meta( get_the_ID(), '_jw_event_location', true );
                $types    = get_the_term_list( get_the_ID(), 'jw_event_type', '', ', ' );
                
                echo '<div class="jw-event-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; background: #fff;">';
                echo '<h3 style="margin-top: 0;"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
                if ( $date ) echo '<p style="margin: 5px 0;">📅 <strong>' . esc_html__( 'Date:', 'jw-event-manager' ) . '</strong> ' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) . '</p>';
                if ( $location ) echo '<p style="margin: 5px 0;">📍 <strong>' . esc_html__( 'Location:', 'jw-event-manager' ) . '</strong> ' . esc_html( $location ) . '</p>';
                if ( $types && ! is_wp_error( $types ) ) echo '<p style="margin: 5px 0;">🏷️ <strong>' . esc_html__( 'Type:', 'jw-event-manager' ) . '</strong> ' . wp_kses_post( $types ) . '</p>';
                echo '</div>';
            }
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . esc_html__( 'No events match your criteria.', 'jw-event-manager' ) . '</p>';
        }

        return ob_get_clean();
    }
}