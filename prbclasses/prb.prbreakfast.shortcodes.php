<?php
class PRBshortCode {

  var $options;
  var $session;

  function __construct()
	{
		add_action( 'init',   array(&$this,'sfgewinnspiel_shortcodes'));

    /*
    * Global Options
    */
    $this->options = get_option('prbreakfast_options');

	}

  /**
	* Add the shortcodes
	*/
	function sfgewinnspiel_shortcodes()
	{
    // Frontend Form
    add_shortcode( 'prbreakfast_form', array(&$this,'prbreakfast_form_function') );

    // Frontend GoalProgress
    add_shortcode( 'prbreakfast_donation_progress', array(&$this,'prbreakfast_donation_progress_function') );


    add_action('wp_ajax_send_prb_mails', array( $this, 'send_prb_mails' ));
    add_action('wp_ajax_nopriv_send_prb_mails', array( $this, 'send_prb_mails' ));

	}

  public function send_prb_mails()
  {
    if ( !wp_verify_nonce( $_REQUEST['nonce'], "send_prb_mails_nonce")) {
      // TODO ERRORE zurücksenden
      exit("No naughty business please");
    }


    $vorname =  $_REQUEST['vorname'];
    $nachname =  $_REQUEST['nachname'];
    $mail =  $_REQUEST['mail'];
    $tel =  $_REQUEST['tel'];
    $bundesland =  $_REQUEST['bundesland'];




    switch ($bundesland) {
      case 'burgenland':
        $mailTo = 'office@krebshilfe-bgld.at';
        $confirmationTextId = 'Burgenland';
        break;
      case 'oberoesterreich':
        $mailTo = 'office@krebshilfe-ooe.at';
        $confirmationTextId = 'Oberösterreich';
        break;
      case 'tirol':
        $mailTo = 'krebshilfe@i-med.ac.at';
        $confirmationTextId = 'Tirol';
        break;
      case 'kaernten':
        $mailTo = 'office@krebshilfe-ktn.at';
        $confirmationTextId = 'Kärnten';
        break;
      case 'salzburg':
        $mailTo = 'office@krebshilfe-sbg.at';
        $confirmationTextId = 'Salzburg';
        break;
      case 'voralberg':
        $mailTo = 'office@krebshilfe-vbg.at';
        $confirmationTextId = 'Vorarlberg';
        break;
      case 'niederoesterreich':
        $mailTo = 'krebshilfe@krebshilfe-noe.at';
        $confirmationTextId = 'Niederösterreich';
        break;
      case 'steiermark':
        $mailTo = 'office@krebshilfe.at';
        $confirmationTextId = 'Steiermark';
        break;
      case 'wien':
        $mailTo = 'service@krebshilfe-wien.at';
        $confirmationTextId = 'Wien';
        break;
      default:
        $mailTo = 'service@krebshilfe.net';
        break;
    }
    $advisorMail = 'service@krebshilfe.net';

    $headers = array('From: Pink Ribbon Breakfast <info@pinkribbonbreakfast.at>');

    // Testmails
    $advisorMail = 'kjell.weibrecht@hotmail.de';
    $mailTo = 'kjell@simplefox.de';

    $mailToArray = array($mailTo, $advisorMail);
    $subject = 'Pink Ribbon Breakfast Anfrage';
    $message = $vorname . ' ' . $nachname . ' möchte ein Pink Ribbon Breakfast organisieren.';
    $message .= '<br /><br />';
    $message .= 'Kontaktdaten:';
    $message .= '<br />';
    $message .= 'Vorname: ' . $vorname;
    $message .= '<br />';
    $message .= 'Nachname: ' . $nachname;
    $message .= '<br />';
    $message .= 'Telefon: ' . $tel;
    $message .= '<br />';
    $message .= 'E-Mail: ' . $mail;
    $message .= '<br />';
    $message .= 'Bundesland: ' . $bundesland;

    // Html in Mails erlauben
    add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

    // Mail an den Zuständigen im Bundesland und den Advisor
    $mailSent = wp_mail($mailToArray, $subject, $message, $headers);


    // Bestätigungsmail für den User
    $respondTo = $mail;
    $subject = 'Bestätigungsmail Pink Ribbon Breakfast';





    $message = $this->options['confirmationMail'.$confirmationTextId];

    if(empty($message)) {
      $message = 'Vielen Dank für Dein Interesse. Pink Ribbon Breakfast wird sich mit Dir in Verbindung setzen. <br/> Mit freundlichen Grüßen, <br/> Pink Ribbon Breakfast';
    }

    $mailSent = wp_mail($respondTo, $subject, $message, $headers);


    echo $mailSent;

    die();
  }

  function fillEmptyDonations($respDonations)
  {

    $bundeslaender = array('Burgenland', 'Oberösterreich', 'Tirol', 'Kärnten', 'Salzburg', 'Vorarlberg', 'Niederösterreich', 'Steiermark', 'Wien');

    foreach($bundeslaender as $bundeslandString) {
      if(array_key_exists($bundeslandString, $respDonations)) {
        $fullRespDonations[$bundeslandString] = $respDonations[$bundeslandString];
      } else {
        $fullRespDonations[$bundeslandString] = 0;
      }

    }
    return $fullRespDonations;
  }

  function getActualYearDonations($donations)
  {

    $actualYear =  date("Y");

    foreach($donations as $key => $val) {

      if (!strpos($val['month'], $actualYear)) {
        unset($donations[$key]);
      }
    }

    return $donations;
  }

  public function prbreakfast_donation_progress_function($atts)
  {
    ob_start();

    /*
    * Donations aufbereiten - gesamt und nach Monat
    */
    $donations = $this->options['prb_donations'];

    $donations = $this->getActualYearDonations($donations);

    $valueGesamt = 0;

    if(!empty($donations)) {
      foreach($donations as $donation) {
        $respDonations[$donation['resp']] += $donation['value'];
        if(!empty($donation['resp'])) {
            $valueGesamt += $donation['value'];
        }
      }
    }

    ?>
    <div class="mainDonationenZiel">
      <div data-value="<?php echo $valueGesamt;?>" id="main_goal"></div>
    </div>

    <div class="marginTopMedium showBundeslandGoals" style="text-align:right;">Bundesländer anzeigen <b style ="color:#EB6CA3 ;">></b></div>
    <?php


    $respDonations = $this->fillEmptyDonations($respDonations);

    ?>
    <div class="bundesland_goals">
      <?php
      foreach($respDonations as $bundesland => $value) {
        ?>
        <div class="panel">
          <label><?php echo $bundesland;?></label>
          <div data-value="<?php echo $value;?>" class="goals"></div>
        </div>
        <?php
      }
    ?>
      <div class="marginTopMedium hideBundeslandGoals" style="text-align:right;">Bundesländer verbergen <b style ="color:#EB6CA3 ;">></b></div>
    </div>


    <?php
    //assign the file output to $content variable and clean buffer
		$content = ob_get_clean();
		return  $content;
  }

  public function prbreakfast_form_function($atts)
  {
    ob_start();
    ?>
    <div class="prb_form_container">
    	<form class="prb_form">
        <?php $nonce = wp_create_nonce('send_prb_mails_nonce'); ?>
        <input type="hidden" id="mail_nonce" value="<?php echo $nonce;?>">
    		<input class="prb_input" type="text" id="vorname" placeholder="Vorname" required="">
    		<input class="prb_input" type="text" id="nachname" placeholder="Nachname" required="">
    		<input class="prb_input" type="tel" id="tel" placeholder="Handynummer" required="">
    		<input class="prb_input" type="email" id="mail" placeholder="E-Mail" required="">
    		<div class="clear"></div>
    		<select class="prb_form_select" id="bundesland" required="">
    			<option velue="waehle">Wähle dein Bundesland</option>
    			<option velue="burgenland">Burgenland</option>
    			<option velue="oberoesterreich">Oberösterreich</option>
    			<option velue="tirol">Tirol</option>
    			<option velue="kaernten">Kärnten</option>
    			<option velue="salzburg">Salzburg</option>
    			<option velue="voralberg">Voralberg</option>
    			<option velue="niederoesterreich">Niederösterreich</option>
    			<option velue="steiermark">Steiermark</option>
    			<option velue="wien">Wien</option>
    		</select>

    		<div class="prb_form_checkbox"><label for="prb_form_checkboxid"><input id="prb_form_checkboxid" type="checkbox" name="newsletter" value="newsletter">Ich darf vom Krebshilfeteam in meiner Nähe kontaktiert werden.<div class="prb_form_btn"></div></label></div>
    		<div class="clear"></div><input class="prb_input" type="submit" value="absenden" id="prb_form_send">

    	</form>
      <script>
        PRBFrontEnd.prototype.validateParsleyForm();
      </script>
		<div class="clear"></div>
    	<div class="prb_form_abschlusscreen">
			<span>Vielen Dank für Ihr Interesse!</span><br>Der jeweilige Ansprechpartner setzt sich mit Ihnen in Verbindung.
    	</div>
    </div>
    <?php
    //assign the file output to $content variable and clean buffer
		$content = ob_get_clean();
		return  $content;
  }

}

$key = "shortcode";
$this->{$key} = new PRBshortCode();
