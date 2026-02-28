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

        if ( ! empty( $search_query ) ) {
            $args['s'] = $search_query;
        }

        if ( ! empty( $event_type ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'jw_event_type',
                    'field'    => 'slug',
                    'terms'    => $event_type,
                ),
            );
        }

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
        $terms = get_terms( array( 'taxonomy' => 'jw_event_type', 'hide_empty' => false ) );
        
        ob_start();
        ?>
        
        <style>
            .jw-events-wrapper { font-family: inherit; margin: 20px 0; }
            .jw-filter-form { background: #f8f9fa; padding: 25px; border-radius: 8px; border: 1px solid #e2e4e7; margin-bottom: 30px; display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-end; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
            .jw-filter-group { display: flex; flex-direction: column; flex: 1; min-width: 160px; }
            .jw-filter-group label { font-size: 0.9em; font-weight: 600; margin-bottom: 8px; color: #1d2327; }
            .jw-filter-group input, .jw-filter-group select { padding: 10px 12px; border: 1px solid #8c8f94; border-radius: 4px; font-size: 14px; background: #fff; box-shadow: 0 0 0 transparent; transition: border-color 0.2s; }
            .jw-filter-group input:focus, .jw-filter-group select:focus { border-color: #2271b1; outline: none; box-shadow: 0 0 0 1px #2271b1; }
            .jw-filter-actions { display: flex; gap: 10px; align-items: flex-end; padding-bottom: 1px; }
            .jw-btn { padding: 11px 20px; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 600; transition: all 0.2s; text-decoration: none; text-align: center; }
            .jw-btn-primary { background: #2271b1; color: #fff; }
            .jw-btn-primary:hover { background: #135e96; color: #fff; }
            .jw-btn-secondary { background: #f0f0f1; color: #d63638; border: 1px solid #d63638; }
            .jw-btn-secondary:hover { background: #d63638; color: #fff; }
            
            .jw-event-card { background: #fff; border: 1px solid #c3c4c7; border-radius: 8px; padding: 20px; margin-bottom: 20px; transition: transform 0.2s, box-shadow 0.2s; }
            .jw-event-card:hover { transform: translateY(-3px); box-shadow: 0 6px 12px rgba(0,0,0,0.08); border-color: #a7aaad; }
            .jw-event-title { margin-top: 0; margin-bottom: 15px; font-size: 1.5em; }
            .jw-event-title a { color: #1d2327; text-decoration: none; }
            .jw-event-title a:hover { color: #2271b1; text-decoration: underline; }
            
            .jw-event-meta-container { display: flex; flex-wrap: wrap; gap: 10px; }
            .jw-meta-badge { display: inline-flex; align-items: center; gap: 6px; background: #f0f0f1; padding: 6px 12px; border-radius: 20px; font-size: 0.85em; color: #3c434a; font-weight: 500; }
            .jw-no-results { background: #fff8e5; border-left: 4px solid #f56e28; padding: 15px; font-weight: 500; }
        </style>

        <div class="jw-events-wrapper">
            <form method="GET" action="" class="jw-filter-form">
                
                <div class="jw-filter-group">
                    <label for="jw_search"><?php esc_html_e( 'Keyword Search', 'jw-event-manager' ); ?></label>
                    <input type="text" id="jw_search" name="jw_search" placeholder="<?php esc_attr_e( 'e.g., Conference...', 'jw-event-manager' ); ?>" value="<?php echo esc_attr( $search_query ); ?>">
                </div>
                
                <div class="jw-filter-group">
                    <label for="jw_type"><?php esc_html_e( 'Event Type', 'jw-event-manager' ); ?></label>
                    <select id="jw_type" name="jw_type">
                        <option value=""><?php esc_html_e( '— All Types —', 'jw-event-manager' ); ?></option>
                        <?php if ( ! is_wp_error( $terms ) ) : ?>
                            <?php foreach ( $terms as $term ) : ?>
                                <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $event_type, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="jw-filter-group">
                    <label for="jw_start_date"><?php esc_html_e( 'Start Date', 'jw-event-manager' ); ?></label>
                    <input type="date" id="jw_start_date" name="jw_start_date" value="<?php echo esc_attr( $start_date ); ?>">
                </div>

                <div class="jw-filter-group">
                    <label for="jw_end_date"><?php esc_html_e( 'End Date', 'jw-event-manager' ); ?></label>
                    <input type="date" id="jw_end_date" name="jw_end_date" value="<?php echo esc_attr( $end_date ); ?>">
                </div>
                
                <div class="jw-filter-actions">
                    <button type="submit" class="jw-btn jw-btn-primary"><?php esc_html_e( 'Filter Events', 'jw-event-manager' ); ?></button>
                    <a href="<?php echo esc_url( strtok( $_SERVER["REQUEST_URI"], '?' ) ); ?>" class="jw-btn jw-btn-secondary"><?php esc_html_e( 'Reset', 'jw-event-manager' ); ?></a>
                </div>
            </form>

            <div class="jw-events-list">
                <?php
                if ( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $date     = get_post_meta( get_the_ID(), '_jw_event_date', true );
                        $location = get_post_meta( get_the_ID(), '_jw_event_location', true );
                        $types    = get_the_term_list( get_the_ID(), 'jw_event_type', '', ', ' );
                        ?>
                        
                        <div class="jw-event-card">
                            <h3 class="jw-event-title">
                                <a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
                            </h3>
                            
                            <div class="jw-event-meta-container">
                                <?php if ( $date ) : ?>
                                    <span class="jw-meta-badge">📅 <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ); ?></span>
                                <?php endif; ?>
                                
                                <?php if ( $location ) : ?>
                                    <span class="jw-meta-badge">📍 <?php echo esc_html( $location ); ?></span>
                                <?php endif; ?>
                                
                                <?php if ( $types && ! is_wp_error( $types ) ) : ?>
                                    <span class="jw-meta-badge">🏷️ <?php echo wp_kses_post( $types ); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php
                    }
                    wp_reset_postdata();
                } else {
                    echo '<div class="jw-no-results">' . esc_html__( 'No events match your current filters. Try adjusting your search criteria.', 'jw-event-manager' ) . '</div>';
                }
                ?>
            </div>
        </div>

        <?php
        return ob_get_clean();
    }
}