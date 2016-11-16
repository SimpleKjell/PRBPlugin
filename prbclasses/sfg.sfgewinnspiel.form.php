<?php
class SFGForm
{

  var $options;

  public function __construct()
	{

		$this->options = get_option('sfgewinnspiel_options');


    add_action('wp_ajax_show_dank_screen', array( $this, 'show_dank_screen' ));
    add_action('wp_ajax_nopriv_show_dank_screen', array( $this, 'show_dank_screen' ));

  }

  public function show_dank_screen()
  {
    $return = '';

    $return .= $this->options['gewinnspiel_form_dank'];
    echo str_replace('\\','',$return);
    die();
  }

  public function showNavi($atts)
  {
    ob_start();
    ?>
    <div class="sf_form_nav_step <?php echo $this->options['gewinnspiel_form_style'];?>">
      <ul class="sf_form_navi">
        <?php
        $i = 1;
        foreach($this->options['gewinnspiel_form_screen_name'] as $screen) {
          ?>

            <li data-nav="<?php echo $i-1;?>" class="sf_form_navi_element <?php echo ($i==1) ?'current' :''; ?>">
              <a href="" class="sf_form_navi_name">
                <span class="sf_form_navi_number">
                    <?php echo $i;?>
                </span>
                <span class="sf_form_navi_desc">
                  <?php echo $screen;?>
                </span>
              </a>
            </li>

          <?php
          $i++;
        }
        ?>
      </ul>
    </div>

    <?php
    //assign the file output to $content variable and clean buffer
		$content = ob_get_clean();
		return  $content;
  }

  public function show($atts)
  {

    //var_dump($this->options['gewinnspiel_form_layout']);
    //turn on output buffering to capture script output
		ob_start();

    ?>

    <div class="sfgewinnspiel_main_container <?php echo $this->options['gewinnspiel_form_style'];?>">
      <div class="formInputs <?php echo $this->options['gewinnspiel_form_layout'];?>">
        <form id="addNewSubForm">
          <?php
          /*
          * Layout STANDARD
          */
          if($this->options['gewinnspiel_form_layout'] != 'stepByStepLayout') {
            $this->renderStandardLayout();
          } else {
            $this->renderStepByStepLayout();
          }
          ?>
        </form>
      </div>

    </div>


    <?php
    //assign the file output to $content variable and clean buffer
		$content = ob_get_clean();
		return  $content;


  }

  public function renderStepByStepLayout()
  {

    //$image_path = get_attached_file(294);

    //$image = wp_get_image_editor( $image_path );
    //$rotate = $image->rotate( 90 );
    //$saved = $image->save( $image_path );
    //var_dump($rotate);
    //$image_meta = get_post_meta(294, '_wp_attachment_metadata', true);
    //var_dump($image_meta);


    ?>
    <div class="form-section-container">

      <!--div class="col-md-4 inputGroupContainer">
      <div class="input-group">
      <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
      <input  name="first_name" placeholder="First Name" class="form-control"  type="text">
        </div>
      </div-->
      <?php
      for ($i = 0; $i < $this->options['gewinnspiel_step_by_step_screen_amount']; $i++) {
        ?>
        <div class="form-section">
          <?php
          $a = 0;
          foreach($this->options['gewinnspiel_form_custom_fields'] as $field) {
            if($field['pos'] == $i) {
              $this->showFormFields($field);
            }
            $a ++;
          }
          if($i+1 == $this->options['gewinnspiel_step_by_step_screen_amount']) {
            $this->addAGBLink();
          }
          ?>
        </div>
        <?php


      }
      ?>
    </div>
    <?php
    $nonce = wp_create_nonce( 'user-submit-form' );
    ?>
    <input type="hidden" name="nonce" class="nonce" data-nonce="<?php echo $nonce;?>" />
    <div class="form-navigation">
      <button type="button" class="previous sf_form_btn pull-left">&#x25C4; Zurück</button>
      <button type="button" class="next sf_form_btn pull-right">Weiter &#9658;</button>
      <input type="submit" class="sf_form_btn pull-right" value="Teilnehmen &#9658;" />
      <span class="clearfix"></span>
    </div>
    <?php
  }

  function addAGBLink()
  {
    ?>
    <div class="sf_agb_field">
      <input required="" type="checkbox" id="agb"/>
      <label for="agb">Ich habe die <a href="<?php echo $this->options['gewinnspiel_form_agb_link']?>">AGB</a> gelesen und zur Kenntnis genommen.</label>
    </div>

    <?php
  }

  function showFormFields($field, $object=null)
  {

    // type2 = text / email / file
    // type3 = required / not required
    // type4 = standard / adresse / upload
    $required = ($field['type3'] == 'required') ? 'required=""' : '';

    switch($field['type4']) {
      case 'upload_1mb':
        ?>
        <?php $nonce = wp_create_nonce('upload_sub_picture'); ?>
        <center>

        <p class="form-notice"></p>
        <input type="hidden" name="nonce" id="nonce_upload" value="<?php echo $nonce;?>" />


          <div class="image-preview"></div>
          <label class="sf_form_upload_btn btn-file marginTopMedium">
              <i class="fa fa-upload fa-2x" aria-hidden="true"></i><br/>Bild hochladen<input style="display:none;" id="upload_image" data-parsley-max-file-size="3000" type="<?php echo $field['type2'];?>"  data-parsley-trigger="change" <?php echo $required;?> placeholder="<?php echo $field['value'];?>" value="<?php echo esc_attr( get_post_meta( $object->ID, strtolower($field['value']), true ) ); ?>" name="<?php echo strtolower($field['value']);?>" />
          </label>
        <input type="hidden" name="_thumbnail_id" id="put_image_id">


        <div class="bar_container">
          <div id="main_container">
            <div id="pbar" class="progress-pie-chart" data-percent="0">
              <div class="ppc-progress">
                <div class="ppc-progress-fill"></div>
              </div>
              <div class="ppc-percents">
                <div class="pcc-percents-wrapper">
                  <span>%</span>
                </div>
              </div>
            </div>

            <progress style="display: none" id="progress_bar" value="0" max="0"></progress>
          </div>
        </div>


        </center>
        <?php

        break;
      case 'upload':
        ?>

        <input id="upload_image" data-parsley-trigger="change" type="<?php echo $field['type2'];?>" data-parsley-max-file-size="10000" <?php echo $required;?> placeholder="<?php echo $field['value'];?>" value="<?php echo esc_attr( get_post_meta( $object->ID, strtolower($field['value']), true ) ); ?>" name="<?php echo strtolower($field['value']);?>" />

        <?php

        break;
      case 'adresse':

        ?>
        <div class="sf_clear form-group">
          <!--label class="col-md-5 control-label"><?php //echo $field['value'];?></label-->
          <div class="col-md-5 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-home"></i></span>
              <input class="zip form-control" data-parsley-trigger="change" maxlength="6" data-parsley-type="digits" type="<?php echo $field['type2'];?>" <?php echo $required;?> placeholder="PLZ" value="<?php echo esc_attr( get_post_meta( $object->ID, 'zip', true ) ); ?>" name="zip" />
              <input class="street form-control" data-parsley-trigger="change" maxlength="25" type="<?php echo $field['type2'];?>" <?php echo $required;?> placeholder="Straße" name="street" value="<?php echo esc_attr( get_post_meta( $object->ID, 'street', true ) ); ?>" />
              <input class="street_number form-control" data-parsley-trigger="change" maxlength="15" type="<?php echo $field['type2'];?>" <?php echo $required;?> placeholder="Hausnummer" name="street_number" value="<?php echo esc_attr( get_post_meta( $object->ID, 'street_number', true ) ); ?>" />
            </div>
          </div>
        </div>

        <?php
        break;
      case 'standard':

        switch($field['type2']) {
          case 'text':
            $iconValue = 'user';
            break;
          case 'email':
            $iconValue = 'envelope';
            break;
        }
        ?>
        <div class="sf_clear form-group">
          <!--label class="col-md-5 control-label"><?php //echo $field['value'];?></label-->
          <div class="col-md-5 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-<?php echo $iconValue;?>"></i></span>
              <input class="form-control" type="<?php echo $field['type2'];?>" data-parsley-trigger="change" maxlength="30" <?php echo $required;?> placeholder="<?php echo $field['value'];?>" value="<?php echo esc_attr( get_post_meta( $object->ID, strtolower($field['value']), true ) ); ?>" name="<?php echo strtolower($field['value']);?>" />
            </div>
          </div>

        </div>
        <?php

        break;
      case 'info':
        echo str_replace('\\','',$field['value']);
        echo '<div class="marginBottomMedium"></div>';
        break;

      case 'select_pic':
        /*
        * Bild auswahl
        */

        echo '<div class="imageSelect">';

        $images = explode(';',$field['select_pic']);

        foreach($images as $image) {
          $image_array = explode(',',$image);
          $imageSorted[] = array('image' =>$image_array[0], 'overlay' => $image_array[1]);
        }

        $imgIndex = 0;
        foreach($imageSorted as $image) {
          $firstElement = ($imgIndex == 0) ? 'required=""' : '';

          echo '<div class="col-md-6 alignCenter">';
          echo '<input type="radio" name="select_pic" '.$firstElement.' id="image_'.$image['image'].'" value="'.$image['image'].'" />';
          echo '<label for="image_'.$image['image'].'">';
          echo '<span class="image">';
          echo wp_get_attachment_image($image['image'], 'sub_pic_small');
          echo '</span>';
          echo '<span class="image_overlay">';
          echo wp_get_attachment_image($image['overlay'], 'sub_pic_small');
          echo '</span>';
          echo '</label>';
          echo '</div>';

          $imgIndex++;
        }
        echo '<div class="clear"></div>';
        echo '</div>';
        break;

      case 'tel':
        ?>
        <div class="sf_clear form-group">
          <!--label class="col-md-5 control-label"><?php //echo $field['value'];?></label-->
          <div class="col-md-5 inputGroupContainer">
            <div class="input-group">
              <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
              <input class="form-control" data-parsley-trigger="change" type="<?php echo $field['type2'];?>" <?php echo $required;?> maxlength="20" data-parsley-type="digits" placeholder="<?php echo $field['value'];?>" value="<?php echo esc_attr( get_post_meta( $object->ID, strtolower($field['value']), true ) ); ?>" name="<?php echo strtolower($field['value']);?>" />
            </div>
          </div>
        </div>
        <?php
        break;
    }
    echo '<div class="marginBottomMedium"></div>';
  }

  public function renderStandardLayout()
  {
    foreach($this->options['gewinnspiel_form_custom_fields'] as $field) {
      ?>
      <div class="field">
        <div class="label">
          <?php echo $field['value'];?>
        </div>
        <div class="input">
          <?php
          // If required
          $required = ($field['type3'] == 'required') ? 'required=""' : '';

          ?>
          <input type="text" <?php echo $required;?> placeholder="<?php echo $field['value'];?>" name="<?php echo strtolower($field['value']);?>" />
        </div>
      </div>
      <?php
    }
    $nonce = wp_create_nonce( 'user-submit-form' );
    ?>
    <input type="hidden" name="nonce" class="nonce" data-nonce="<?php echo $nonce;?>" />
    <div class="formSubmit">
      <input class="sf_form_btn" type="submit" value="Teilnehmen" />
    </div>
    <div class="formAGB">
      <div class="checkbox">
        <input type="checkbox" required="" name="agb" />
      </div>
      <div class="txt">
        <a href="<?php echo $this->options['gewinnspiel_form_agb_link'];?>">AGBs</a> gelesen und verstanden.
      </div>
    </div>
    <?php
  }

}
$key = "form";
$this->{$key} = new SFGForm();

?>
