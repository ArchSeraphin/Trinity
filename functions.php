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

  $page      = isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1;
  $per_page  = isset( $_POST['per_page'] ) ? max( 1, (int) $_POST['per_page'] ) : 8;
  $category  = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';
  $format    = isset( $_POST['format'] ) ? sanitize_text_field( wp_unslash( $_POST['format'] ) ) : '';
  $order_dir = isset( $_POST['order'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['order'] ) ) ) : 'desc';
  $exclude   = isset( $_POST['exclude'] ) ? sanitize_text_field( wp_unslash( $_POST['exclude'] ) ) : '';
  $exclude_ids = array();

  if ( $exclude ) {
    $exclude_parts = array_unique( array_filter( array_map( 'trim', explode( ',', $exclude ) ) ) );
    foreach ( $exclude_parts as $part ) {
      $exclude_ids[] = (int) $part;
    }
    $exclude_ids = array_filter( $exclude_ids );
  }

  $tax_query = array();

  if ( $category ) {
    $tax_query[] = array(
      'taxonomy' => 'categorie',
      'field'    => 'slug',
      'terms'    => $category,
    );
  }

  if ( $format ) {
    $tax_query[] = array(
      'taxonomy' => 'format',
      'field'    => 'slug',
      'terms'    => $format,
    );
  }

  if ( count( $tax_query ) > 1 ) {
    $tax_query['relation'] = 'AND';
  }

  $query_args = array(
    'post_type'      => 'photo',
    'posts_per_page' => $per_page,
    'paged'          => $page,
    'post_status'    => 'publish',
    'no_found_rows'  => false,
    'orderby'        => 'meta_value',
    'meta_key'       => 'date',
    'meta_type'      => 'DATE',
    'order'          => ( 'asc' === $order_dir ) ? 'ASC' : 'DESC',
  );

  if ( ! empty( $tax_query ) ) {
    $query_args['tax_query'] = $tax_query;
  }

  if ( ! empty( $exclude_ids ) ) {
    $query_args['post__not_in'] = $exclude_ids;
    $query_args['paged']        = 1;
    $query_args['offset']       = 0;
  }

  $photo_query = new WP_Query( $query_args );

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

  $retrieved_count = (int) $photo_query->post_count;
  $total_found     = (int) $photo_query->found_posts;
  $remaining_posts = max( 0, $total_found - $retrieved_count );
  $has_more        = $remaining_posts > 0;

  wp_send_json_success(
    array(
      'html'      => $html,
      'nextPage'  => $page + 1,
      'maxPages'  => (int) $photo_query->max_num_pages,
      'hasMore'   => $has_more,
    )
  );
}
add_action( 'wp_ajax_trinity_load_more_photos', 'trinity_ajax_load_more_photos' );
add_action( 'wp_ajax_nopriv_trinity_load_more_photos', 'trinity_ajax_load_more_photos' );

if ( ! function_exists( 'trinity_get_photo_field_value' ) ) {
  /**
   * Retourne une valeur ACF/meta nettoyée pour une photo.
   *
   * @param int    $photo_id   ID de la photo.
   * @param string $field_name Nom du champ.
   *
   * @return string
   */
  function trinity_get_photo_field_value( $photo_id, $field_name ) {
    if ( ! $photo_id || ! $field_name ) {
      return '';
    }

    $value = '';

    if ( function_exists( 'get_field' ) ) {
      $value = get_field( $field_name, $photo_id );

      if ( is_array( $value ) && isset( $value['value'] ) ) {
        $value = $value['value'];
      }
    } else {
      $value = get_post_meta( $photo_id, $field_name, true );
    }

    if ( $value instanceof DateTimeInterface ) {
      $value = $value->format( 'Y-m-d' );
    } elseif ( is_array( $value ) || is_object( $value ) ) {
      $value = '';
    } elseif ( is_numeric( $value ) ) {
      $value = (string) $value;
    }

    $value = is_string( $value ) ? trim( wp_strip_all_tags( $value ) ) : '';

    if ( 'date' === $field_name && $value ) {
      try {
        $date_obj = new DateTime( $value );
        $value    = $date_obj->format( 'Y-m-d' );
      } catch ( Exception $e ) {
        // Laisse la valeur telle quelle si le format ne peut pas être converti.
      }
    }

    return $value;
  }
}

if ( ! function_exists( 'trinity_get_photo_image_data' ) ) {
  /**
   * Récupère l'image principale d'une photo.
   *
   * @param int    $photo_id ID de la photo.
   * @param string $size     Taille souhaitée.
   *
   * @return array
   */
  function trinity_get_photo_image_data( $photo_id, $size = 'full' ) {
    $image_url = '';
    $image_alt = '';

    if ( function_exists( 'get_field' ) ) {
      $acf_image = get_field( 'photo', $photo_id );

      if ( is_array( $acf_image ) ) {
        $image_url = isset( $acf_image['url'] ) ? $acf_image['url'] : '';
        $image_alt = isset( $acf_image['alt'] ) ? $acf_image['alt'] : '';
      } elseif ( is_scalar( $acf_image ) && $acf_image ) {
        $attachment_id = (int) $acf_image;
        $image_url     = wp_get_attachment_image_url( $attachment_id, $size );
        $image_alt     = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
      }
    }

    if ( ! $image_url ) {
      $image_url = get_the_post_thumbnail_url( $photo_id, $size );
      $thumb_id  = get_post_thumbnail_id( $photo_id );
      if ( $thumb_id ) {
        $image_alt = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
      }
    }

    return array(
      'url' => $image_url ? esc_url_raw( $image_url ) : '',
      'alt' => $image_alt ? wp_strip_all_tags( $image_alt ) : get_the_title( $photo_id ),
    );
  }
}

if ( ! function_exists( 'trinity_get_photo_terms_as_text' ) ) {
  /**
   * Retourne les termes d'une taxonomie sous forme de texte.
   *
   * @param int    $photo_id ID de la photo.
   * @param string $taxonomy Taxonomie ciblée.
   *
   * @return string
   */
  function trinity_get_photo_terms_as_text( $photo_id, $taxonomy ) {
    if ( ! $photo_id || ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
      return '';
    }

    $terms = get_the_terms( $photo_id, $taxonomy );

    if ( empty( $terms ) || is_wp_error( $terms ) ) {
      return '';
    }

    $names = wp_list_pluck( $terms, 'name' );

    if ( empty( $names ) ) {
      return '';
    }

    return implode( ' / ', array_map( 'sanitize_text_field', $names ) );
  }
}

if ( ! function_exists( 'trinity_prepare_photo_nav_data' ) ) {
  /**
   * Prépare les données nécessaires pour un lien de navigation.
   *
   * @param int $photo_id ID de la photo.
   *
   * @return array|null
   */
  function trinity_prepare_photo_nav_data( $photo_id ) {
    if ( ! $photo_id ) {
      return null;
    }

    $image = trinity_get_photo_image_data( $photo_id, 'medium_large' );

    return array(
      'ID'         => $photo_id,
      'title'      => get_the_title( $photo_id ),
      'permalink'  => get_permalink( $photo_id ),
      'reference'  => trinity_get_photo_field_value( $photo_id, 'reference' ),
      'image_url'  => $image['url'],
      'image_alt'  => $image['alt'],
    );
  }
}

if ( ! function_exists( 'trinity_get_adjacent_photo_data' ) ) {
  /**
   * Retourne la photo adjacente selon la date ACF.
   *
   * @param int    $photo_id     Photo courante.
   * @param string $current_date Date courante (Y-m-d).
   * @param string $direction    prev|next.
   *
   * @return array|null
   */
  function trinity_get_adjacent_photo_data( $photo_id, $current_date, $direction = 'prev' ) {
    if ( ! $photo_id ) {
      return null;
    }

    $is_prev   = ( 'prev' === $direction );
    $compare   = $is_prev ? '>' : '<';
    $order     = $is_prev ? 'ASC' : 'DESC';
    $query_args = array(
      'post_type'      => 'photo',
      'post_status'    => 'publish',
      'posts_per_page' => 1,
      'no_found_rows'  => true,
      'post__not_in'   => array( $photo_id ),
      'orderby'        => 'meta_value',
      'meta_key'       => 'date',
      'meta_type'      => 'DATE',
      'order'          => $order,
      'fields'         => 'ids',
    );

    if ( $current_date ) {
      $query_args['meta_query'] = array(
        array(
          'key'     => 'date',
          'value'   => $current_date,
          'compare' => $compare,
          'type'    => 'DATE',
        ),
      );
    } else {
      $post_object = get_post( $photo_id );

      if ( ! $post_object ) {
        return null;
      }

      unset( $query_args['meta_key'], $query_args['meta_type'] );
      $query_args['orderby'] = 'date';
      $query_args['order']   = $is_prev ? 'ASC' : 'DESC';
      $query_args['date_query'] = array(
        array(
          $is_prev ? 'after' : 'before' => $post_object->post_date,
          'inclusive'                   => false,
        ),
      );
    }

    $results = get_posts( $query_args );

    if ( empty( $results ) ) {
      return null;
    }

    return trinity_prepare_photo_nav_data( (int) $results[0] );
  }
}

if ( ! function_exists( 'trinity_get_photo_navigation' ) ) {
  /**
   * Retourne les données de navigation (précédent / suivant).
   *
   * @param int $photo_id ID de la photo courante.
   *
   * @return array
   */
  function trinity_get_photo_navigation( $photo_id ) {
    $current_date = trinity_get_photo_field_value( $photo_id, 'date' );

    return array(
      'prev' => trinity_get_adjacent_photo_data( $photo_id, $current_date, 'prev' ),
      'next' => trinity_get_adjacent_photo_data( $photo_id, $current_date, 'next' ),
    );
  }
}

if ( ! function_exists( 'trinity_get_related_photos' ) ) {
  /**
   * Retourne une sélection de photos de la même catégorie.
   *
   * @param int $photo_id ID de la photo courante.
   * @param int $limit    Nombre maximum de photos.
   *
   * @return WP_Post[]
   */
  function trinity_get_related_photos( $photo_id, $limit = 2 ) {
    if ( ! $photo_id || ! taxonomy_exists( 'categorie' ) ) {
      return array();
    }

    $term_ids = wp_get_post_terms(
      $photo_id,
      'categorie',
      array(
        'fields' => 'ids',
      )
    );

    if ( empty( $term_ids ) || is_wp_error( $term_ids ) ) {
      return array();
    }

    $args = array(
      'post_type'      => 'photo',
      'posts_per_page' => $limit,
      'post_status'    => 'publish',
      'post__not_in'   => array( $photo_id ),
      'no_found_rows'  => true,
      'order'          => 'DESC',
      'orderby'        => 'meta_value',
      'meta_key'       => 'date',
      'meta_type'      => 'DATE',
      'tax_query'      => array(
        array(
          'taxonomy' => 'categorie',
          'field'    => 'term_id',
          'terms'    => $term_ids,
        ),
      ),
    );

    return get_posts( $args );
  }
}
