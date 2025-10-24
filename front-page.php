<?php
/**
 * Modèle de la page d'accueil.
 *
 * @package Trinity
 */

get_header();

// Récupère les champs ACF pour la section héro.
$header_image_source = null;
$header_title        = '';

if ( function_exists( 'get_field' ) ) {
  $front_page_id      = get_option( 'page_on_front' );
  $header_image_field = $front_page_id ? get_field( 'photo_header', $front_page_id ) : get_field( 'photo_header' );
  $header_title       = $front_page_id ? get_field( 'texte_header', $front_page_id ) : get_field( 'texte_header' );

  if ( is_array( $header_image_field ) ) {
    $header_image_source = isset( $header_image_field['url'] ) ? $header_image_field['url'] : '';
  } elseif ( is_int( $header_image_field ) || ( is_string( $header_image_field ) && ctype_digit( $header_image_field ) ) ) {
    $header_image_source = wp_get_attachment_image_url( (int) $header_image_field, 'full' );
  } elseif ( is_string( $header_image_field ) ) {
    $header_image_source = $header_image_field;
  }
}

if ( $header_image_source || $header_title ) {
  $header_image_url = $header_image_source ? esc_url( $header_image_source ) : '';
  $header_style     = $header_image_url ? sprintf( 'style="background-image: url(%s);"', $header_image_url ) : '';
  ?>
  <section class="home-hero" <?php echo $header_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if ( $header_title ) { ?>
      <h1 class="home-hero__title"><?php echo esc_html( $header_title ); ?></h1>
    <?php } ?>
  </section>
  <?php
}

// Récupère les 8 éléments du type de contenu photo.
$photo_query_args = array(
  'post_type'      => 'photo',
  'posts_per_page' => 8,
  'post_status'    => 'publish',
  'no_found_rows'  => false,
);

$photo_query = new WP_Query( $photo_query_args );

if ( $photo_query->have_posts() ) {
  $max_pages    = (int) $photo_query->max_num_pages;
  $per_page     = (int) $photo_query->get( 'posts_per_page' );
  $current_page = max( 1, (int) $photo_query->get( 'paged' ) );
  ?>
  <section
    class="home-photo-grid"
    data-load-more="true"
    data-current-page="<?php echo esc_attr( $current_page ); ?>"
    data-per-page="<?php echo esc_attr( $per_page ); ?>"
    data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
  >
    <div class="home-photo-grid__inner" id="photo-grid">
      <?php
      while ( $photo_query->have_posts() ) {
        $photo_query->the_post();
        $photo_post = get_post();
        get_template_part( 'templates_part/photo-card', null, array( 'photo_post' => $photo_post ) );
      }
      ?>
    </div>
    <?php if ( $max_pages > $current_page ) { ?>
      <div class="home-photo-grid__actions">
        <button type="button" class="home-photo-grid__load-more" id="photo-load-more">
          <?php esc_html_e( 'Charger plus', 'trinity' ); ?>
        </button>
      </div>
    <?php } ?>
  </section>
  <?php
}

wp_reset_postdata();

get_footer();
