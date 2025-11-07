<?php
/**
 * Modèle single pour le post type photo.
 *
 * @package Trinity
 */

get_header();

if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    get_template_part( 'templates_part/photo-info', null, array( 'photo_post' => get_post() ) );
  }
}

get_template_part( 'templates_part/photo-lightbox' );

get_footer();
