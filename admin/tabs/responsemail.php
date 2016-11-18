
<h3><?php _e('Bestätigungsmail','prbreakfast'); ?></h3>

<form method="post" action="">
  <input type="hidden" name="update_settings" />
  <?php wp_nonce_field( 'update_settings', 'prb_nonce_check' ); ?>

  <?php
  global $pr_breakfast;

  $resp = $this->getResponsiblePerson();




  $settings = array( 'media_buttons' => false );
  wp_editor($this->options['confirmationMail'.$resp], 'confirmationMail'.$resp, $settings)

  ?>

  <input type="hidden" name="confirmation_mail_tab" value="1"/>
  <p class="submit">
  	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Einstellung speichern','prbreakfast'); ?>"  />
  </p>
</form>
