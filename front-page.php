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
    <div class="home-photo-grid__container">
      <form class="home-photo-grid__filters" id="photo-filters" aria-label="<?php esc_attr_e( 'Filtres du catalogue photo', 'trinity' ); ?>">
        <div class="home-photo-grid__filter photo-filter" data-filter="category">
          <input type="hidden" name="category" value="">
          <button
            type="button"
            class="photo-filter__toggle"
            aria-haspopup="listbox"
            aria-expanded="false"
            aria-controls="filter-category-menu"
          >
            <span class="photo-filter__text" data-default-label="<?php esc_attr_e( 'Catégories', 'trinity' ); ?>"><?php esc_html_e( 'Catégories', 'trinity' ); ?></span>
            <span class="photo-filter__chevron" aria-hidden="true"></span>
          </button>
          <div class="photo-filter__menu-wrapper">
            <ul class="photo-filter__menu" id="filter-category-menu" role="listbox">
              <li class="photo-filter__option is-selected" role="option" aria-selected="true" data-value="" tabindex="0"><?php esc_html_e( 'Catégories', 'trinity' ); ?></li>
              <li class="photo-filter__option" role="option" aria-selected="false" data-value="reception" tabindex="0"><?php esc_html_e( 'Réception', 'trinity' ); ?></li>
              <li class="photo-filter__option" role="option" aria-selected="false" data-value="mariage" tabindex="0"><?php esc_html_e( 'Mariage', 'trinity' ); ?></li>
              <li class="photo-filter__option" role="option" aria-selected="false" data-value="concert" tabindex="0"><?php esc_html_e( 'Concert', 'trinity' ); ?></li>
              <li class="photo-filter__option" role="option" aria-selected="false" data-value="television" tabindex="0"><?php esc_html_e( 'Télévision', 'trinity' ); ?></li>
            </ul>
          </div>
        </div>

        <div class="home-photo-grid__filter photo-filter" data-filter="format">
          <input type="hidden" name="format" value="">
          <button
            type="button"
            class="photo-filter__toggle"
            aria-haspopup="listbox"
            aria-expanded="false"
            aria-controls="filter-format-menu"
          >
            <span class="photo-filter__text" data-default-label="<?php esc_attr_e( 'Formats', 'trinity' ); ?>"><?php esc_html_e( 'Formats', 'trinity' ); ?></span>
            <span class="photo-filter__chevron" aria-hidden="true"></span>
          </button>
          <div class="photo-filter__menu-wrapper">
            <ul class="photo-filter__menu" id="filter-format-menu" role="listbox">
              <li class="photo-filter__option is-selected" role="option" aria-selected="true" data-value="" tabindex="0"><?php esc_html_e( 'Formats', 'trinity' ); ?></li>
              <li class="photo-filter__option" role="option" aria-selected="false" data-value="paysage" tabindex="0"><?php esc_html_e( 'Paysage', 'trinity' ); ?></li>
              <li class="photo-filter__option" role="option" aria-selected="false" data-value="portrait" tabindex="0"><?php esc_html_e( 'Portrait', 'trinity' ); ?></li>
            </ul>
          </div>
        </div>

        <div class="home-photo-grid__filter home-photo-grid__filter--align-right photo-filter" data-filter="order">
          <input type="hidden" name="order" value="desc">
          <button
            type="button"
            class="photo-filter__toggle"
            aria-haspopup="listbox"
            aria-expanded="false"
            aria-controls="filter-order-menu"
          >
            <span class="photo-filter__text" data-default-label="<?php esc_attr_e( 'Trier par', 'trinity' ); ?>"><?php esc_html_e( 'Plus récentes', 'trinity' ); ?></span>
            <span class="photo-filter__chevron" aria-hidden="true"></span>
          </button>
          <div class="photo-filter__menu-wrapper">
            <ul class="photo-filter__menu" id="filter-order-menu" role="listbox">
              <li class="photo-filter__option is-selected" role="option" aria-selected="true" data-value="desc" tabindex="0"><?php esc_html_e( 'Plus récentes', 'trinity' ); ?></li>
              <li class="photo-filter__option" role="option" aria-selected="false" data-value="asc" tabindex="0"><?php esc_html_e( 'Plus anciennes', 'trinity' ); ?></li>
            </ul>
          </div>
        </div>
      </form>
      <div class="home-photo-grid__inner" id="photo-grid">
        <?php
        while ( $photo_query->have_posts() ) {
          $photo_query->the_post();
          $photo_post = get_post();
          get_template_part( 'templates_part/photo-card', null, array( 'photo_post' => $photo_post ) );
        }
        ?>
      </div>
    </div>
    <div class="home-photo-grid__actions">
      <button
        type="button"
        class="home-photo-grid__load-more<?php echo ( $max_pages > $current_page ) ? '' : ' is-hidden'; ?>"
        id="photo-load-more"
        <?php echo ( $max_pages > $current_page ) ? '' : ' hidden="hidden"'; ?>
      >
        <?php esc_html_e( 'Charger plus', 'trinity' ); ?>
      </button>
    </div>
  </section>
  <?php
}

wp_reset_postdata();

get_footer();
