<?php
/**
 * Carte photo réutilisable.
 *
 * Attentes :
 * - Variables disponibles :
 *   - $photo_post (WP_Post) ou ID du post photo.
 *
 * @package Trinity
 */

if ( isset( $args['photo_post'] ) ) {
  $photo_post = $args['photo_post'];
} else {
  $photo_post = null;
}

if ( ! $photo_post ) {
  return;
}

$photo_id = $photo_post instanceof WP_Post ? $photo_post->ID : $photo_post;

$image_url = '';
$image_alt = '';

if ( function_exists( 'get_field' ) ) {
  $acf_image = get_field( 'photo', $photo_id ); // Champ image ACF principal.

  if ( is_array( $acf_image ) ) {
    $image_url = isset( $acf_image['url'] ) ? $acf_image['url'] : '';
    $image_alt = isset( $acf_image['alt'] ) ? $acf_image['alt'] : '';
  } elseif ( is_int( $acf_image ) || ( is_string( $acf_image ) && ctype_digit( $acf_image ) ) ) {
    $attachment_id = (int) $acf_image;
    $image_url     = wp_get_attachment_image_url( $attachment_id, 'large' );
    $image_alt     = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
  } elseif ( is_string( $acf_image ) ) {
    $image_url = $acf_image;
  }
}

if ( ! $image_url ) {
  $image_url = get_the_post_thumbnail_url( $photo_id, 'large' );
  $image_alt = get_post_meta( get_post_thumbnail_id( $photo_id ), '_wp_attachment_image_alt', true );
}

if ( ! $image_url ) {
  return;
}

$image_alt = $image_alt ? $image_alt : get_the_title( $photo_id );

$photo_title      = get_the_title( $photo_id );
$photo_link       = get_permalink( $photo_id );
$photo_term       = '';
$photo_categories = array();

if ( function_exists( 'get_field' ) ) {
  $raw_terms = get_field( 'categorie', $photo_id );

  if ( $raw_terms instanceof WP_Term ) {
    $raw_terms = array( $raw_terms );
  }

  if ( is_array( $raw_terms ) && ! empty( $raw_terms ) ) {
    $term_names = array();

    foreach ( $raw_terms as $term_item ) {
      if ( $term_item instanceof WP_Term ) {
        $term_names[]      = $term_item->name;
        $photo_categories[] = $term_item->name;
      } elseif ( is_array( $term_item ) && isset( $term_item['name'] ) ) {
        $term_names[]      = $term_item['name'];
        $photo_categories[] = $term_item['name'];
      } elseif ( is_numeric( $term_item ) ) {
        $term_object = get_term( (int) $term_item );
        if ( $term_object && ! is_wp_error( $term_object ) ) {
          $term_names[]      = $term_object->name;
          $photo_categories[] = $term_object->name;
        }
      } elseif ( is_string( $term_item ) && $term_item ) {
        $term_names[]      = $term_item;
        $photo_categories[] = $term_item;
      }
    }

    if ( ! empty( $term_names ) ) {
      $photo_categories = array_map( 'sanitize_text_field', $term_names );
      $photo_term       = implode( ' / ', $photo_categories );
    }
  } elseif ( is_string( $raw_terms ) ) {
    $photo_term = $raw_terms;
    $photo_categories[] = $raw_terms;
  }
}

$photo_term = is_string( $photo_term ) ? $photo_term : '';
$photo_reference = '';

if ( function_exists( 'get_field' ) ) {
  $photo_reference = get_field( 'reference', $photo_id );

  if ( is_array( $photo_reference ) ) {
    $photo_reference = isset( $photo_reference['value'] ) ? $photo_reference['value'] : '';
  }
}

$photo_reference = is_string( $photo_reference ) ? $photo_reference : '';
$photo_categories = array_map( 'sanitize_text_field', $photo_categories );
$photo_categories_string = ! empty( $photo_categories ) ? implode( ' / ', $photo_categories ) : '';
?>
<article
  class="photo-card"
  data-photo-id="<?php echo esc_attr( $photo_id ); ?>"
  data-photo-image="<?php echo esc_attr( $image_url ); ?>"
  data-photo-title="<?php echo esc_attr( $photo_title ); ?>"
  data-photo-category="<?php echo esc_attr( $photo_categories_string ); ?>"
  data-photo-reference="<?php echo esc_attr( $photo_reference ); ?>"
  data-photo-link="<?php echo esc_attr( $photo_link ); ?>"
>
  <figure class="photo-card__figure">
    <img class="photo-card__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" />
    <div class="photo-card__overlay">
      <a class="photo-card__link" href="<?php echo esc_url( $photo_link ); ?>">
        <span class="photo-card__icon photo-card__icon--view" aria-hidden="true"></span>
        <span class="screen-reader-text"><?php esc_html_e( 'Voir la photo', 'trinity' ); ?></span>
      </a>
      <button type="button" class="photo-card__icon photo-card__icon--expand" data-lightbox-trigger aria-label="<?php esc_attr_e( 'Agrandir la photo', 'trinity' ); ?>"></button>
    </div>
    <figcaption class="photo-card__meta">
      <span class="photo-card__title"><?php echo esc_html( $photo_title ); ?></span>
      <?php if ( $photo_term ) { ?>
        <span class="photo-card__category"><?php echo esc_html( $photo_term ); ?></span>
      <?php } ?>
    </figcaption>
  </figure>
</article>
