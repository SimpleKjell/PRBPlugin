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

	}

  public function prbreakfast_form_function($atts)
  {
    ob_start();
    ?>
    <div class="prb_form_container">
    	<form class="prb_form">
    		<input class="prb_input" type="text" placeholder="Vorname" requiered>
    		<input class="prb_input" type="text" placeholder="Nachname" requiered>
    		<input class="prb_input" type="tel" placeholder="Handynummer" requiered>
    		<input class="prb_input" type="email" placeholder="E-Mail" requiered>
    		<select>
    			<option velue="bl1">
    				Bundesland1
    			</option>
    			<option velue="bl1">
    				Bundesland2
    			</option>
    		</select>
    		<input class="prb_input" type="submit" value="senden" id="prb_form_send">
    		
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
