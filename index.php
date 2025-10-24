<?php
/**
 * Modèle principal générique.
 *
 * @package Trinity
 */

get_header();

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
