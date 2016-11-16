<div class="dsdf-members-sect ">
  <h3><?php _e('Permalinks','dsdfmembers'); ?></h3>

  <form method="post" action="">
    <input type="hidden" name="update_settings" />
    <?php wp_nonce_field( 'update_settings', 'sfm_nonce_check' ); ?>


    <table class="form-table">
    <?php

      $this->create_plugin_setting(
              'select',
              'registration_page_id',
              __('SF Musiker Registration Page','sfmusiker'),
              $this->get_all_sytem_pages(),
              __('Make sure you have the <code>[sfmusiker_registration]</code> shortcode on this page.','sfmusiker'),
              __('The default front-end Registration page where new users will sign up.','sfmusiker')
      );
      $this->create_plugin_setting(
              'select',
              'activation_page_id',
              __('SF Musiker Aktivierungs Page','sfmusiker'),
              $this->get_all_sytem_pages(),
              __('Make sure you have the <code>[sfmusiker_activate]</code> shortcode on this page.','sfmusiker'),
              __('The default front-end Registration page where new users will sign up.','sfmusiker')
      );

      $this->create_plugin_setting(
              'select',
              'login_page_id',
              __('SF Musiker Login Page','sfmusiker'),
              $this->get_all_sytem_pages(),
              __('If you wish to change default DSDF login page, you may set it here. Make sure you have the <code>[sfmusiker_login]</code> shortcode on this page.','investusers'),
              __('The default front-end login page.','sfmusiker')
      );

      $this->create_plugin_setting(
              'select',
              'sfmusiker_my_account_page',
              __('SF Musiker My Account','sfmusiker'),
              $this->get_all_sytem_pages(),
              __('Make sure you have the <code>[sfmusiker_my_account]</code> shortcode on this page.','sfmusiker'),
              __('This page is where users will view their account.','sfmusiker')
      );

    ?>
    </table>

    <p class="submit">
    	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','sfmusiker'); ?>"  />
    </p>
  </form>
</div>
