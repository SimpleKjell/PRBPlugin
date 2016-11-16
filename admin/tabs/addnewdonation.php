

<h3><?php _e('Neue Spende hinzufügen','sfgewinnspiel'); ?></h3>
<form method="post" action="">
  <input type="hidden" name="update_settings" />
  <?php wp_nonce_field( 'update_settings', 'prb_nonce_check' ); ?>



  <?php

  /*
  * Für die Spenden wird kein custom post type benutzt, einfach nur die wp options
  */
  $donations = $this->options['prb_donations'];
  if(!is_array($donations)) {
    $donations = array();
  }

  //var_dump($donations);


  if(!empty($donations)) {
    end($donations);
    $lastKey = key($donations);
    $nextKey = $lastKey + 1;
  } else {
    $nextKey = 1;
  }


  ?>

  <div class="hidden">
    <?php
    if(!empty($donations)) {

      foreach($donations as $key => $donation) {

        ?>
        <input type="text" name="prb_donations[<?php echo $key;?>][orga]" value="<?php echo $donation['orga'];?>" />
        <input type="text" name="prb_donations[<?php echo $key;?>][value]" value="<?php echo $donation['value'];?>" />
        <input type="text" name="prb_donations[<?php echo $key;?>][city]" value="<?php echo $donation['city'];?>" />
        <input type="text" name="prb_donations[<?php echo $key;?>][mail]" value="<?php echo $donation['mail'];?>" />
        <input type="text" name="prb_donations[<?php echo $key;?>][month]" value="<?php echo $donation['month'];?>" />
        <?php
      }
    }
    ?>

  </div>

  <div class="wrap">
    <div class="field">
      <label for="orga" >Organisator</label>
      <input value="" name="prb_donations[<?php echo $nextKey;?>][orga]" id="orga" type="text" />
    </div>
    <div class="field">

    </div>
    <div class="field">
      <label for="donation" >Spende</label>
      <input name="prb_donations[<?php echo $nextKey;?>][value]" id="donation" type="number" />
    </div>
    <div class="field">
      <label for="city" >Ort</label>
      <input name="prb_donations[<?php echo $nextKey;?>][city]" id="city" type="text" />
    </div>
    <div class="field">
      <label for="mail" >E-Mail</label>
      <input name="prb_donations[<?php echo $nextKey;?>][mail]" id="mail" type="email" />
    </div>
    <div class="field">
      <label for="mail" >Monat/Jahr</label>
      <select name="prb_donations[<?php echo $nextKey;?>][month]">
        <option value="<?php echo date('M Y');?>"><?php echo date('M Y');?></option>
        <option value="<?php echo date("M Y", strtotime("first day of previous month"));?>"><?php echo date("M Y", strtotime("first day of previous month"));?></option>        
      </select>
    </div>


  </div>


  <p class="submit">
  	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Spende hinzufügen','prbreakfast'); ?>"  />
  </p>
</form>
