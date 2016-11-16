<h3><?php _e('General Settings','sfmusiker'); ?></h3>
<form method="post" action="">
<input type="hidden" name="update_settings" />
<?php wp_nonce_field( 'update_settings', 'sfm_nonce_check' ); ?>
<table class="form-table">
<?php



$this->create_plugin_setting(
        'input',
        'social_media_facebook_app_id',
        __('Facebook App ID','sfmusiker'),array(),
        __('Obtained when you created the Facebook Application.','sfmusiker'),
        __('Obtained when you created the Facebook Application.','sfmusiker')
);

$this->create_plugin_setting(
        'input',
        'social_media_facebook_secret',
        __('Facebook App Secret','sfmusiker'),array(),
        __('Facebook settings','sfmusiker'),
        __('Obtained when you created the Facebook Application.','sfmusiker')
);


$this->create_plugin_setting(
        'input',
        'google_client_id',
        __('Google Client ID','sfmusiker'),array(),
        __('Paste the client id that you got from google API Console','sfmusiker'),
        __('Paste the client id that you got from google API Console','sfmusiker')
);

$this->create_plugin_setting(
        'input',
        'google_client_secret',
        __('Google Client Secret','sfmusiker'),array(),
        __('Set the client secret','sfmusiker'),
        __('Obtained when you created the Google Application.','sfmusiker')
);

$this->create_plugin_setting(
        'input',
        'google_redirect_uri',
        __('Google Redirect URI','sfmusiker'),array(),
        __('Paste the redirect URI where you given in APi Console. You will get the Access Token here during login success. Find more information here https://developers.google.com/console/help/new/#console.  <br><br> VERY IMPORTANT: Your URL should end with "?sfmusikerplus=1". Example: http://yourdomain.com/?sfmusikerplus=1','sfmusiker'),
        __('Your URL should end with "?sfmusikerplus=1". Example: http://yourdomain.com/?sfmusikerplus=1','sfmusiker')
);

?>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','sfmusiker'); ?>"  />
</p>
</form>
