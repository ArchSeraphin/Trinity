<?php
/**
 * Template part : page d'information d'une photo.
 *
 * @package Trinity
 */

$photo_post = isset( $args['photo_post'] ) ? $args['photo_post'] : get_post();

if ( ! $photo_post instanceof WP_Post ) {
  return;
}

$photo_id       = $photo_post->ID;
$photo_title    = get_the_title( $photo_id );
$photo_image    = trinity_get_photo_image_data( $photo_id, 'full' );
$photo_reference = trinity_get_photo_field_value( $photo_id, 'reference' );
$photo_category = trinity_get_photo_terms_as_text( $photo_id, 'categorie' );
$photo_format   = trinity_get_photo_terms_as_text( $photo_id, 'format' );
$photo_type     = trinity_get_photo_field_value( $photo_id, 'type' );
$photo_date     = trinity_get_photo_field_value( $photo_id, 'date' );
$photo_year     = '';

if ( $photo_date ) {
  try {
    $photo_year = ( new DateTime( $photo_date ) )->format( 'Y' );
  } catch ( Exception $e ) {
    $photo_year = $photo_date;
  }
}

if ( ! $photo_year ) {
  $photo_year = get_the_date( 'Y', $photo_id );
}

$navigation      = trinity_get_photo_navigation( $photo_id );
$prev_photo      = isset( $navigation['prev'] ) ? $navigation['prev'] : null;
$next_photo      = isset( $navigation['next'] ) ? $navigation['next'] : null;
$has_navigation  = $prev_photo || $next_photo;
$related_photos  = trinity_get_related_photos( $photo_id, 2 );
$contact_query_key = trinity_get_contact_reference_query_key();
$contact_link    = get_permalink( $photo_id );

if ( $photo_reference ) {
  $contact_link = add_query_arg( $contact_query_key, $photo_reference, $contact_link );
}

$contact_link .= '#contact-modal';
?>

<section class="photo-info" data-photo-id="<?php echo esc_attr( $photo_id ); ?>">
  <div class="photo-info__layout">
    <div class="photo-info__details">
      <header class="photo-info__header">
        <h2 class="photo-info__title"><?php echo esc_html( $photo_title ); ?></h2>
      </header>

      <dl class="photo-info__meta">
        <?php if ( $photo_reference ) : ?>
          <div class="photo-info__meta-item">
            <dt><?php esc_html_e( 'Référence', 'trinity' ); ?></dt>
            <dd><?php echo esc_html( $photo_reference ); ?></dd>
          </div>
        <?php endif; ?>

        <?php if ( $photo_category ) : ?>
          <div class="photo-info__meta-item">
            <dt><?php esc_html_e( 'Catégorie', 'trinity' ); ?></dt>
            <dd><?php echo esc_html( $photo_category ); ?></dd>
          </div>
        <?php endif; ?>

        <?php if ( $photo_format ) : ?>
          <div class="photo-info__meta-item">
            <dt><?php esc_html_e( 'Format', 'trinity' ); ?></dt>
            <dd><?php echo esc_html( $photo_format ); ?></dd>
          </div>
        <?php endif; ?>

        <?php if ( $photo_type ) : ?>
          <div class="photo-info__meta-item">
            <dt><?php esc_html_e( 'Type', 'trinity' ); ?></dt>
            <dd><?php echo esc_html( $photo_type ); ?></dd>
          </div>
        <?php endif; ?>

        <?php if ( $photo_year ) : ?>
          <div class="photo-info__meta-item">
            <dt><?php esc_html_e( 'Année', 'trinity' ); ?></dt>
            <dd><?php echo esc_html( $photo_year ); ?></dd>
          </div>
        <?php endif; ?>
      </dl>
    </div>

    <?php if ( $photo_image['url'] ) : ?>
      <div class="photo-info__visual">
        <figure class="photo-info__figure">
          <img src="<?php echo esc_url( $photo_image['url'] ); ?>" alt="<?php echo esc_attr( $photo_image['alt'] ); ?>" loading="lazy" />
        </figure>
      </div>
    <?php endif; ?>
  </div>

  <div class="photo-info__cta-nav">
    <div class="photo-info__cta">
      <p class="photo-info__cta-text"><?php esc_html_e( 'Cette photo vous intéresse ?', 'trinity' ); ?></p>
      <a
        class="photo-info__contact-button"
        href="<?php echo esc_url( $contact_link ); ?>"
        data-contact-modal-open
        <?php if ( $photo_reference ) : ?>
          data-photo-reference="<?php echo esc_attr( $photo_reference ); ?>"
        <?php endif; ?>
        data-photo-title="<?php echo esc_attr( $photo_title ); ?>"
      >
        <?php esc_html_e( 'Contact', 'trinity' ); ?>
      </a>
    </div>

    <?php if ( $has_navigation ) : ?>
      <nav class="photo-info__nav" aria-label="<?php esc_attr_e( 'Navigation entre les photos', 'trinity' ); ?>">
        <?php if ( $prev_photo ) : ?>
          <a class="photo-info__nav-link photo-info__nav-link--prev" href="<?php echo esc_url( $prev_photo['permalink'] ); ?>">
            <span class="photo-info__nav-arrow" aria-hidden="true">←</span>
            <span class="photo-info__nav-label"><?php esc_html_e( 'Précédente', 'trinity' ); ?></span>
            <?php if ( ! empty( $prev_photo['image_url'] ) ) : ?>
              <span class="photo-info__nav-preview" aria-hidden="true">
                <img src="<?php echo esc_url( $prev_photo['image_url'] ); ?>" alt="<?php echo esc_attr( $prev_photo['title'] ); ?>" loading="lazy" />
              </span>
            <?php endif; ?>
          </a>
        <?php endif; ?>

        <?php if ( $next_photo ) : ?>
          <a class="photo-info__nav-link photo-info__nav-link--next" href="<?php echo esc_url( $next_photo['permalink'] ); ?>">
            <span class="photo-info__nav-label"><?php esc_html_e( 'Suivante', 'trinity' ); ?></span>
            <span class="photo-info__nav-arrow" aria-hidden="true">→</span>
            <?php if ( ! empty( $next_photo['image_url'] ) ) : ?>
              <span class="photo-info__nav-preview" aria-hidden="true">
                <img src="<?php echo esc_url( $next_photo['image_url'] ); ?>" alt="<?php echo esc_attr( $next_photo['title'] ); ?>" loading="lazy" />
              </span>
            <?php endif; ?>
          </a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  </div>

  <?php if ( ! empty( $related_photos ) ) : ?>
    <section class="photo-info__related" aria-labelledby="photo-info-related-title">
      <h2 id="photo-info-related-title" class="photo-info__related-title"><?php esc_html_e( 'Vous aimerez aussi', 'trinity' ); ?></h2>
      <div class="photo-info__related-grid">
        <?php
        foreach ( $related_photos as $related_post ) {
          get_template_part( 'templates_part/photo-card', null, array( 'photo_post' => $related_post ) );
        }
        ?>
      </div>
    </section>
  <?php endif; ?>
</section>
