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
?>
<article class="photo-card">
  <figure class="photo-card__figure">
    <img class="photo-card__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" />
  </figure>
</article>
