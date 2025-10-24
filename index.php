<?php
/**
 * Modèle principal de Trinity.
 *
 * @package Trinity
 */

get_header();

if ( is_front_page() ) {
  // Section héro modifiable via ACF (assignée à la page d'accueil si disponible).
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
    <section class="home-hero" <?php echo $header_style;  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
      <?php if ( $header_title ) : ?>
        <h1 class="home-hero__title"><?php echo esc_html( $header_title ); ?></h1>
      <?php endif; ?>
    </section>
    <?php
  }
}

if ( have_posts() ) {
  while ( have_posts() ) {
    the_post();
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>
      <header class="post-header">
        <h2 class="post-title">
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        <div class="post-meta">
          <span class="post-date"><?php echo get_the_date(); ?></span>
          <span class="post-author"><?php esc_html_e( 'par', 'trinity' ); ?> <?php the_author(); ?></span>
        </div>
      </header>

      <div class="post-content">
        <?php
        if ( is_singular() ) {
          the_content();
        } else {
          the_excerpt();
          ?>
          <p><a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Lire la suite', 'trinity' ); ?></a></p>
          <?php
        }
        ?>
      </div>

      <footer class="post-footer">
        <?php
        wp_link_pages(
          array(
            'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'trinity' ),
            'after'  => '</nav>',
          )
        );
        ?>
      </footer>
    </article>
    <?php
  }
  ?>

  <nav class="posts-navigation">
    <?php the_posts_pagination(); ?>
  </nav>

  <?php
} else {
  ?>
  <section class="no-results">
    <h2><?php esc_html_e( 'Aucun contenu disponible', 'trinity' ); ?></h2>
    <p><?php esc_html_e( 'Nous ne trouvons rien pour le moment. Essayez une nouvelle recherche.', 'trinity' ); ?></p>
    <?php get_search_form(); ?>
  </section>
  <?php
}

get_footer();
