<?php
/**
 * Template for displaying a single Event.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header(); ?>

<style>
    .jw-single-meta { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 30px; padding: 20px 0; border-top: 1px solid #e2e4e7; border-bottom: 1px solid #e2e4e7; }
    .jw-meta-badge { display: inline-flex; align-items: center; gap: 6px; background: #f0f0f1; padding: 8px 14px; border-radius: 20px; font-size: 0.9em; color: #3c434a; font-weight: 500; }
    .jw-rsvp-wrapper { margin-top: 50px; padding: 35px; background: #f8f9fa; border: 1px solid #e2e4e7; border-radius: 8px; }
    .jw-rsvp-title { margin-top: 0; font-size: 1.6em; margin-bottom: 25px; color: #1d2327; }
    .jw-form-group { margin-bottom: 20px; }
    .jw-form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #1d2327; }
    .jw-form-group input { width: 100%; padding: 12px; border: 1px solid #8c8f94; border-radius: 4px; max-width: 450px; font-size: 16px; }
    .jw-form-group input:focus { border-color: #2271b1; outline: none; box-shadow: 0 0 0 1px #2271b1; }
    .jw-submit-btn { background: #2271b1; color: #fff; border: none; padding: 14px 28px; font-size: 16px; font-weight: 600; border-radius: 4px; cursor: pointer; transition: background 0.2s; }
    .jw-submit-btn:hover { background: #135e96; }
</style>

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
                    <?php the_title( '<h1 class="entry-title" style="font-size: 3em; margin-bottom: 15px; color: #1d2327;">', '</h1>' ); ?>
                    
                    <div class="jw-single-meta">
                        <?php if ( $event_date ) : ?>
                            <span class="jw-meta-badge">📅 <?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $event_date ) ) ); ?></span>
                        <?php endif; ?>
                        
                        <?php if ( $event_location ) : ?>
                            <span class="jw-meta-badge">📍 <?php echo esc_html( $event_location ); ?></span>
                        <?php endif; ?>

                        <?php if ( $event_types && ! is_wp_error( $event_types ) ) : ?>
                            <span class="jw-meta-badge">🏷️ <?php echo wp_kses_post( $event_types ); ?></span>
                        <?php endif; ?>
                    </div>
                </header>

                <div class="entry-content" style="font-size: 1.15em; line-height: 1.7; color: #3c434a;">
                    <?php the_content(); ?>
                </div>
                
                <div class="jw-rsvp-wrapper">
                    <h3 class="jw-rsvp-title">
                        <?php esc_html_e( 'RSVP for this Event', 'jw-event-manager' ); ?>
                    </h3>
                    
                    <?php if ( isset( $_GET['rsvp'] ) && 'success' === $_GET['rsvp'] ) : ?>
                        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 25px; font-weight: bold; border: 1px solid #c3e6cb;">
                            <?php esc_html_e( 'Thank you! Your RSVP has been successfully recorded. A confirmation email has been sent.', 'jw-event-manager' ); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST">
                        <input type="hidden" name="action" value="jw_submit_rsvp">
                        <input type="hidden" name="event_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
                        <?php wp_nonce_field( 'jw_submit_rsvp_action', 'jw_rsvp_nonce' ); ?>

                        <div class="jw-form-group">
                            <label for="rsvp_name"><?php esc_html_e( 'Full Name *', 'jw-event-manager' ); ?></label>
                            <input type="text" id="rsvp_name" name="rsvp_name" required>
                        </div>

                        <div class="jw-form-group">
                            <label for="rsvp_email"><?php esc_html_e( 'Email Address *', 'jw-event-manager' ); ?></label>
                            <input type="email" id="rsvp_email" name="rsvp_email" required>
                        </div>

                        <button type="submit" class="jw-submit-btn">
                            <?php esc_html_e( 'Confirm Attendance', 'jw-event-manager' ); ?>
                        </button>
                    </form>
                </div>

            </article>

        <?php endwhile; ?>

    </main>
</div>

<?php get_footer(); ?>