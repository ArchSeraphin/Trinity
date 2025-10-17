<?php
/**
 * En-tête principal du thème Trinity.
 *
 * @package Trinity
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>
  <header class="site-header">
    <div class="site-header__inner">
      <div class="site-branding">
        <?php if ( has_custom_logo() ) : ?>
          <div class="site-logo"><?php the_custom_logo(); ?></div>
        <?php endif; ?>
        <div class="site-title-wrapper">
          <?php if ( is_front_page() && is_home() ) : ?>
            <h1 class="site-title">
              <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
            </h1>
          <?php else : ?>
            <p class="site-title">
              <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
            </p>
          <?php endif; ?>
          <?php if ( get_bloginfo( 'description' ) ) : ?>
            <p class="site-description"><?php bloginfo( 'description' ); ?></p>
          <?php endif; ?>
        </div>
      </div>

      <?php if ( has_nav_menu( 'primary' ) ) : ?>
        <nav class="primary-navigation" aria-label="<?php esc_attr_e( 'Menu principal', 'trinity' ); ?>">
          <?php
          wp_nav_menu(
            array(
              'theme_location' => 'primary',
              'menu_class'     => 'primary-menu',
              'menu_id'        => 'primary-menu',
              'container'      => false,
              'fallback_cb'    => false,
            )
          );
          ?>
        </nav>

        <button
          class="menu-toggle"
          aria-controls="primary-menu"
          aria-expanded="false"
          data-open-label="<?php esc_attr_e( 'Ouvrir le menu', 'trinity' ); ?>"
          data-close-label="<?php esc_attr_e( 'Fermer le menu', 'trinity' ); ?>"
        >
          <span class="menu-toggle__box" aria-hidden="true">
            <span class="menu-toggle__line"></span>
            <span class="menu-toggle__line"></span>
            <span class="menu-toggle__line"></span>
          </span>
          <span class="screen-reader-text"><?php esc_html_e( 'Ouvrir le menu', 'trinity' ); ?></span>
        </button>
      <?php endif; ?>
    </div>
  </header>
  <main class="site-content">
