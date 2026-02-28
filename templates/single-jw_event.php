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
                
                <div class="jw-rsvp-section" style="margin-top: 40px; padding: 25px; background: #ffffff; border: 1px solid #e2e4e7; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <h3 style="margin-top: 0; font-size: 1.5em; border-bottom: 2px solid #0073aa; padding-bottom: 10px; display: inline-block;">
                        <?php esc_html_e( 'RSVP for this Event', 'jw-event-manager' ); ?>
                    </h3>
                    
                    <?php if ( isset( $_GET['rsvp'] ) && 'success' === $_GET['rsvp'] ) : ?>
                        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: bold;">
                            <?php esc_html_e( 'Thank you! Your RSVP has been successfully recorded. A confirmation email has been sent.', 'jw-event-manager' ); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" style="display: flex; flex-direction: column; gap: 15px; max-width: 400px;">
                        <input type="hidden" name="action" value="jw_submit_rsvp">
                        <input type="hidden" name="event_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                        <?php wp_nonce_field( 'jw_submit_rsvp_action', 'jw_rsvp_nonce' ); ?>

                        <div>
                            <label for="rsvp_name" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Full Name *', 'jw-event-manager' ); ?></label>
                            <input type="text" id="rsvp_name" name="rsvp_name" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>

                        <div>
                            <label for="rsvp_email" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Email Address *', 'jw-event-manager' ); ?></label>
                            <input type="email" id="rsvp_email" name="rsvp_email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>

                        <button type="submit" style="background: #0073aa; color: #fff; border: none; padding: 12px 20px; font-size: 16px; font-weight: bold; border-radius: 4px; cursor: pointer; transition: background 0.3s;">
                            <?php esc_html_e( 'Confirm Attendance', 'jw-event-manager' ); ?>
                        </button>
                    </form>
                </div>

            </article>

        <?php endwhile; ?>

    </main>
</div>

<?php get_footer(); ?>