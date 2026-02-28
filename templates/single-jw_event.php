<?php
/**
 * Template for displaying a single Event.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header(); ?>

<div id="primary" class="content-area" style="max-width: 800px; margin: 40px auto; padding: 20px;">
    <main id="main" class="site-main">

        <?php
        while ( have_posts() ) :
            the_post();

            $event_date     = get_post_meta( get_the_ID(), '_jw_event_date', true );
            $event_location = get_post_meta( get_the_ID(), '_jw_event_location', true );
            $event_types    = get_the_term_list( get_the_ID(), 'jw_event_type', '', ', ' );
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title( '<h1 class="entry-title" style="font-size: 2.5em; margin-bottom: 10px;">', '</h1>' ); ?>
                    
                    <div class="event-meta" style="background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa; margin-bottom: 20px;">
                        <?php if ( $event_date ) : ?>
                            <p style="margin: 0 0 5px;">📅 <strong><?php esc_html_e( 'Date:', 'jw-event-manager' ); ?></strong> <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $event_date ) ) ); ?></p>
                        <?php endif; ?>
                        
                        <?php if ( $event_location ) : ?>
                            <p style="margin: 0 0 5px;">📍 <strong><?php esc_html_e( 'Location:', 'jw-event-manager' ); ?></strong> <?php echo esc_html( $event_location ); ?></p>
                        <?php endif; ?>

                        <?php if ( $event_types && ! is_wp_error( $event_types ) ) : ?>
                            <p style="margin: 0;">🏷️ <strong><?php esc_html_e( 'Type:', 'jw-event-manager' ); ?></strong> <?php echo wp_kses_post( $event_types ); ?></p>
                        <?php endif; ?>
                    </div>
                </header>

                <div class="entry-content">
                    <?php
                    // Display the main content (description)
                    the_content();
                    ?>
                </div>
                
                </article>

        <?php endwhile; ?>

    </main>
</div>

<?php get_footer(); ?>