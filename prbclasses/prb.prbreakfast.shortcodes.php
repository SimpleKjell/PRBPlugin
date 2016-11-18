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


    echo $vorname;
    echo $nachname;
    echo $mail;
    echo $tel;
    echo $bundesland;


    switch ($bundesland) {
      case 'burgenland':
        $mailTo = 'office@krebshilfe-bgld.at';
        break;
      case 'oberoesterreich':
        $mailTo = 'office@krebshilfe-ooe.at';
        break;
      case 'tirol':
        $mailTo = 'krebshilfe@i-med.ac.at';
        break;
      case 'kaernten':
        $mailTo = 'office@krebshilfe-ktn.at';
        break;
      case 'salzburg':
        $mailTo = 'office@krebshilfe-sbg.at';
        break;
      case 'voralberg':
        $mailTo = 'office@krebshilfe-vbg.at';
        break;
      case 'niederoesterreich':
        $mailTo = 'krebshilfe@krebshilfe-noe.at';
        break;
      case 'steiermark':
        $mailTo = 'office@krebshilfe.at';
        break;
      case 'wien':
        $mailTo = 'service@krebshilfe-wien.at';
        break;
      default:
        $mailTo = 'service@krebshilfe.net';
        break;
    }
    $koordinatorMail = 'service@krebshilfe.net';

    // Testmail
    $mailTo = 'kjell@simplefox.de';

    

    die();
  }

  public function prbreakfast_donation_progress_function($atts)
  {
    ob_start();
    ?>


    <div id="sample_goal"></div>


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
    		<input class="prb_input" type="text" id="vorname" placeholder="Vorname" requiered>
    		<input class="prb_input" type="text" id="nachname" placeholder="Nachname" requiered>
    		<input class="prb_input" type="tel" id="tel" placeholder="Handynummer" requiered>
    		<input class="prb_input" type="email" id="mail" placeholder="E-Mail" requiered>
    		<div class="clear"></div>
    		<select class="prb_form_select" id="bundesland">
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
    </div>
    <?php
    //assign the file output to $content variable and clean buffer
		$content = ob_get_clean();
		return  $content;
  }

}

$key = "shortcode";
$this->{$key} = new PRBshortCode();
