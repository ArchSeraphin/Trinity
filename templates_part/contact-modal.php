<?php
/**
 * Modale de contact du thème Trinity.
 *
 * @package Trinity
 */

?>
<div
  id="contact-modal"
  class="contact-modal"
  aria-hidden="true"
  data-contact-query-key="<?php echo esc_attr( trinity_get_contact_reference_query_key() ); ?>"
  data-contact-query-value="<?php echo esc_attr( trinity_get_contact_reference_from_request() ); ?>"
>
  <div class="contact-modal__overlay" data-contact-modal-close></div>
  <div class="contact-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="contact-modal-title" tabindex="-1">
    <div class="contact-modal__header">
      <h2 id="contact-modal-title" class="contact-modal__title">CONTACTCONTACTCONTACT<br/>CONTACTCONTACTCONTACT</h2>
    </div>
    <div class="contact-modal__content">
      <?php
      $contact_form_shortcode = apply_filters( 'trinity_contact_modal_shortcode', '[forminator_form id="92"]' );
      echo do_shortcode( $contact_form_shortcode );
      ?>
    </div>
  </div>
</div>
