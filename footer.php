<?php
/**
 * Pied de page du thème Trinity.
 *
 * @package Trinity
 */
?>
  </main>
  <footer class="site-footer">
    <?php if ( has_nav_menu( 'footer' ) ) : ?>
      <nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Liens de pied de page', 'trinity' ); ?>">
        <?php
        wp_nav_menu(
          array(
            'theme_location' => 'footer',
            'menu_class'     => 'footer-menu',
            'container'      => false,
            'depth'          => 1,
            'fallback_cb'    => false,
          )
        );
        ?>
      </nav>
    <?php endif; ?>
    <span class="site-footer__note"><?php esc_html_e( 'Tous droits réservés.', 'trinity' ); ?></span>
  </footer>
  <?php wp_footer(); ?>
</body>
</html>
