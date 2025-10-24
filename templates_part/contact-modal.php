<?php
/**
 * Modale de contact du thème Trinity.
 *
 * @package Trinity
 */

?>
<div id="contact-modal" class="contact-modal" aria-hidden="true">
  <div class="contact-modal__overlay" data-contact-modal-close></div>
  <div class="contact-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="contact-modal-title" tabindex="-1">
    <div class="contact-modal__header">
      <h2 id="contact-modal-title" class="contact-modal__title">CONTACTCONTACTCONTACT<br/>CONTACTCONTACTCONTACT</h2>
    </div>
    <div class="contact-modal__content">
      <?php
      // formulaire via shortcode.
      echo do_shortcode( apply_filters( 'trinity_contact_modal_shortcode', '' ) );
      ?>
    </div>
  </div>
</div>
