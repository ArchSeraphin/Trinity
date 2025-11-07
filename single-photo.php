<?php
/**
 * Modèle single pour le post type photo.
 *
 * @package Trinity
 */

$queried_photo = get_queried_object();

if ( $queried_photo instanceof WP_Post && 'photo' === $queried_photo->post_type ) {
  $query_key        = trinity_get_contact_reference_query_key();
  $expected_ref     = trinity_get_photo_field_value( $queried_photo->ID, 'reference' );
  $current_ref      = isset( $_GET[ $query_key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $query_key ] ) ) : '';

  if ( $query_key && $expected_ref && $current_ref !== $expected_ref ) {
    $redirect_url = add_query_arg( $query_key, $expected_ref, get_permalink( $queried_photo ) );
    wp_safe_redirect( $redirect_url, 302 );
    exit;
  }
}

get_header();

if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    get_template_part( 'templates_part/photo-info', null, array( 'photo_post' => get_post() ) );
  }
}

get_template_part( 'templates_part/photo-lightbox' );

get_footer();
