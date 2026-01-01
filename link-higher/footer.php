<?php
/**
 * The template for displaying the footer
 */
?>

<?php
// Use Elementor footer if available and custom footer is designed
if ( function_exists( 'elementor_theme_do_location' ) ) {
    if ( elementor_theme_do_location( 'footer' ) ) {
        wp_footer();
        ?>
        </body>
        </html>
        <?php
        return; // Stop executing the rest of the theme footer
    }
}

// If no Elementor footer or Elementor not active, use theme footer
?>
</main>

<footer class="lh-site-footer">

    <!-- Secondary footer: external footer / menu (opt-in) -->
    <?php if ( get_theme_mod('lh_partner_footer', false) ) : ?>
    <div class="lh-footer-secondary">
		    <div style="max-width: 800px; padding-top: 40px; border-radius: 8px; margin: auto;">
                
        <div style="font-size: 1.1rem; color: #333; text-align:center">
            <p style="margin-bottom: 20px;">
                <span style="color: #33A5E3; font-weight: 600;">ToonStream</span> is your go-to destination for animated entertainment. Explore top-rated anime movies, classic cartoons, trending kids shows, and timeless favorites across every genre.
            </p>
            
            <p style="margin-bottom: 0px;">
                From nostalgic gems to modern masterpieces, we celebrate animation for all ages. Discover your next animated obsession and dive into stories that bring imagination to life.
            </p>
        </div>
        
    </div>
        <div class="lh-footer-container lh-footer-secondary-inner">
            <div class="lh-footer-iframe-wrap">
                <iframe 
                    src="https://linkhigher.com/partner-site" 
                    title="Partner site footer"
                    loading="lazy" 
                    width="100%">
                </iframe>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Primary footer: bottom bar -->
    <div class="lh-footer-primary">
        <div class="lh-footer-container lh-footer-primary-inner">
			
			<nav class="lh-footer-legal mobile-footer" aria-label="Legal links">
                <?php 
                $privacy = esc_url( get_theme_mod('lh_privacy_url', '') );
                $terms   = esc_url( get_theme_mod('lh_terms_url', '') ); 
                ?>

                <?php if ( $privacy ) : ?>
                    <a href="<?php echo $privacy; ?>"><?php _e('Privacy Policy', 'link-higher'); ?></a>
                <?php endif; ?>

                <?php if ( $privacy && $terms ) : ?>
                    <span class="lh-footer-separator">•</span>
                <?php endif; ?>

                <?php if ( $terms ) : ?>
                    <a href="<?php echo $terms; ?>"><?php _e('Terms &amp; Conditions', 'link-higher'); ?></a>
                <?php endif; ?>
            </nav>

            <p class="lh-footer-copy">
                <?php 
                echo wp_kses_post( 
                    get_theme_mod(
                        'lh_footer_text', 
                        sprintf('&copy; %s %s. All rights reserved.', date('Y'), get_bloginfo('name'))
                    ) 
                ); 
                ?>
            </p>


        </div>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
