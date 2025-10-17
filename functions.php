<?php
/**
 * Fonctions et définitions du thème Trinity.
 *
 * @package Trinity
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! function_exists( 'trinity_setup' ) ) {
  /**
   * Configure les fonctionnalités de base du thème.
   */
  function trinity_setup() {
    load_theme_textdomain( 'trinity', get_template_directory() . '/languages' );

    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );

    register_nav_menus(
      array(
        'primary' => __( 'Menu principal', 'trinity' ),
        'footer'  => __( 'Menu de pied de page', 'trinity' ),
      )
    );

    add_theme_support(
      'html5',
      array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
      )
    );

    add_theme_support(
      'custom-logo',
      array(
        'height'      => 100,
        'width'       => 100,
        'flex-height' => true,
        'flex-width'  => true,
      )
    );
  }
}
add_action( 'after_setup_theme', 'trinity_setup' );

/**
 * Enfile les styles et scripts du thème.
 */
function trinity_enqueue_assets() {
  $theme_version = wp_get_theme()->get( 'Version' );

  wp_enqueue_style(
    'trinity-fonts',
    'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Space+Mono:wght@400;700&display=swap',
    array(),
    null
  );

  wp_enqueue_style(
    'trinity-style',
    get_stylesheet_uri(),
    array( 'trinity-fonts' ),
    $theme_version
  );
}
add_action( 'wp_enqueue_scripts', 'trinity_enqueue_assets' );

/**
 * Déclare une zone de widgets par défaut.
 */
function trinity_widgets_init() {
  register_sidebar(
    array(
      'name'          => __( 'Barre latérale', 'trinity' ),
      'id'            => 'sidebar-1',
      'description'   => __( 'Ajoutez ici vos widgets.', 'trinity' ),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );
}
add_action( 'widgets_init', 'trinity_widgets_init' );
