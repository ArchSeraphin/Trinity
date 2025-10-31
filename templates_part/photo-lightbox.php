<?php
/**
 * Lightbox pour les photos du catalogue.
 *
 * @package Trinity
 */

?>
<div id="photo-lightbox" class="photo-lightbox" aria-hidden="true">
  <div class="photo-lightbox__overlay" data-lightbox-close></div>
  <div class="photo-lightbox__dialog" role="dialog" aria-modal="true" aria-labelledby="photo-lightbox-title">
    <h2 id="photo-lightbox-title" class="screen-reader-text"><?php esc_html_e( 'Agrandissement de la photo', 'trinity' ); ?></h2>
    <button type="button" class="photo-lightbox__close" aria-label="<?php esc_attr_e( 'Fermer la lightbox', 'trinity' ); ?>" data-lightbox-close>
      <span aria-hidden="true">&times;</span>
    </button>
    <button type="button" class="photo-lightbox__nav photo-lightbox__nav--prev" data-lightbox-prev>
      <span class="photo-lightbox__nav-arrow" aria-hidden="true">←</span>
      <span class="photo-lightbox__nav-label"><?php esc_html_e( 'Précédente', 'trinity' ); ?></span>
    </button>
    <figure class="photo-lightbox__figure">
      <img class="photo-lightbox__image" src="" alt="" data-lightbox-image />
      <figcaption class="photo-lightbox__foot">
        <span class="photo-lightbox__reference" data-lightbox-reference></span>
        <span class="photo-lightbox__category" data-lightbox-category></span>
      </figcaption>
    </figure>
    <button type="button" class="photo-lightbox__nav photo-lightbox__nav--next" data-lightbox-next>
      <span class="photo-lightbox__nav-label"><?php esc_html_e( 'Suivante', 'trinity' ); ?></span>
      <span class="photo-lightbox__nav-arrow" aria-hidden="true">→</span>
    </button>
  </div>
</div>
