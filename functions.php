<?php
/**
 * Fonctions et définitions du thème Trinity.
 *
 * @package Trinity
 */

require_once get_template_directory() . '/addon/resize-upload.php';


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
    'trinity-style',
    get_stylesheet_uri(),
    array(),
    $theme_version
  );

  wp_enqueue_script(
    'trinity-navigation',
    get_template_directory_uri() . '/assets/js/navigation.js',
    array(),
    $theme_version,
    true
  );

  wp_enqueue_script(
    'trinity-scripts',
    get_template_directory_uri() . '/assets/js/scripts.js',
    array(),
    $theme_version,
    true
  );

  wp_localize_script(
    'trinity-scripts',
    'TrinityLoadMore',
    array(
      'ajaxUrl' => admin_url( 'admin-ajax.php' ),
      'nonce'   => wp_create_nonce( 'trinity_load_more_photos' ),
      'strings' => array(
        'error' => __( 'Impossible de charger plus de photos pour le moment.', 'trinity' ),
      ),
    )
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

/**
 * Ajoute un réglage Customizer pour ajuster la hauteur du logo.
 *
 * @param WP_Customize_Manager $wp_customize Gestionnaire du customizer.
 */
function trinity_customize_register( $wp_customize ) {
  $wp_customize->add_setting(
    'trinity_logo_max_height',
    array(
      'default'           => 48,
      'sanitize_callback' => 'trinity_sanitize_logo_height',
      'transport'         => 'postMessage',
    )
  );

  $wp_customize->add_control(
    'trinity_logo_max_height',
    array(
      'label'       => __( 'Hauteur maximale du logo (px)', 'trinity' ),
      'section'     => 'title_tagline',
      'type'        => 'range',
      'input_attrs' => array(
        'min'  => 10,
        'max'  => 200,
        'step' => 2,
      ),
    )
  );

  $wp_customize->add_setting(
    'display_title_and_tagline',
    array(
      'default'           => true,
      'sanitize_callback' => 'trinity_sanitize_checkbox',
      'transport'         => 'postMessage',
    )
  );

  $wp_customize->add_control(
    'display_title_and_tagline',
    array(
      'label'   => __( 'Afficher le titre et le slogan du site', 'trinity' ),
      'section' => 'title_tagline',
      'type'    => 'checkbox',
    )
  );
}
add_action( 'customize_register', 'trinity_customize_register' );

/**
 * Valide la hauteur du logo saisie dans le Customizer.
 *
 * @param mixed $value Valeur à valider.
 * @return int Hauteur positive.
 */
function trinity_sanitize_logo_height( $value ) {
  $value = absint( $value );

  if ( 0 === $value ) {
    return 48;
  }

  return max( 10, min( 200, $value ) );
}

/**
 * Sanitize pour les cases à cocher Customizer.
 *
 * @param mixed $checked Valeur du champ.
 * @return bool
 */
function trinity_sanitize_checkbox( $checked ) {
  return (bool) $checked;
}

/**
 * Applique les styles personnalisés du logo.
 */
function trinity_enqueue_custom_logo_style() {
  $max_height = get_theme_mod( 'trinity_logo_max_height', 48 );
  $max_height = trinity_sanitize_logo_height( $max_height );

  if ( ! $max_height ) {
    return;
  }

  $custom_css = sprintf(
    '.site-logo img { height: %1$dpx; width: auto; max-height: none; }',
    $max_height
  );

  wp_add_inline_style( 'trinity-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'trinity_enqueue_custom_logo_style', 20 );

/**
 * Enfile les scripts Customizer côté panneau.
 */
function trinity_customize_controls_enqueue_scripts() {
  wp_enqueue_script(
    'trinity-customize-controls',
    get_template_directory_uri() . '/assets/js/customize-controls.js',
    array( 'customize-controls', 'jquery' ),
    wp_get_theme()->get( 'Version' ),
    true
  );
}
add_action( 'customize_controls_enqueue_scripts', 'trinity_customize_controls_enqueue_scripts' );

/**
 * Enfile les scripts Customizer côté prévisualisation.
 */
function trinity_customize_preview_enqueue_scripts() {
  wp_enqueue_script(
    'trinity-customize-preview',
    get_template_directory_uri() . '/assets/js/customize-preview.js',
    array( 'customize-preview' ),
    wp_get_theme()->get( 'Version' ),
    true
  );
}
add_action( 'customize_preview_init', 'trinity_customize_preview_enqueue_scripts' );

/**
 * Handler AJAX pour charger plus de photos.
 */
function trinity_ajax_load_more_photos() {
  check_ajax_referer( 'trinity_load_more_photos', 'nonce' );

  $page     = isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1;
  $per_page = isset( $_POST['per_page'] ) ? max( 1, (int) $_POST['per_page'] ) : 8;

  $photo_query = new WP_Query(
    array(
      'post_type'      => 'photo',
      'posts_per_page' => $per_page,
      'paged'          => $page,
      'post_status'    => 'publish',
      'no_found_rows'  => false,
    )
  );

  if ( ! $photo_query->have_posts() ) {
    wp_send_json_error(
      array(
        'message' => __( 'Plus de photos disponibles.', 'trinity' ),
      )
    );
  }

  ob_start();

  while ( $photo_query->have_posts() ) {
    $photo_query->the_post();
    $photo_post = get_post();
    get_template_part( 'templates_part/photo-card', null, array( 'photo_post' => $photo_post ) );
  }

  wp_reset_postdata();

  $html = ob_get_clean();

  wp_send_json_success(
    array(
      'html'      => $html,
      'nextPage'  => $page + 1,
      'maxPages'  => (int) $photo_query->max_num_pages,
      'hasMore'   => $page < (int) $photo_query->max_num_pages,
    )
  );
}
add_action( 'wp_ajax_trinity_load_more_photos', 'trinity_ajax_load_more_photos' );
add_action( 'wp_ajax_nopriv_trinity_load_more_photos', 'trinity_ajax_load_more_photos' );
