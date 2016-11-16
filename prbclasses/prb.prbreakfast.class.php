<?php

/* Master Class*/

class PRBreakfast
{

  var $options;
  var $current_page;
  var $classes_array;

  public function __construct()
	{

		$this->current_page = $_SERVER['REQUEST_URI'];
		$this->options = get_option('prbreakfast_options');

  }

  // Init Funktion
  public function plugin_init()
  {


    /*Load Main classes*/
    $this->set_main_classes();
    $this->load_classes();

    /*Load Amin Classes*/
    if (is_admin())
    {
      $this->set_admin_classes();
      $this->load_classes();
    }

    //Initial Settings
    $this->intial_settings();

  }

  // Alle Klassen in ein Array schreiben
  public function set_main_classes()
	{
    $this->classes_array = array(

      "commmonmethods" =>"prb.prbreakfast.common" ,
      "shortocde" =>"prb.prbreakfast.shortcodes" ,
      //"form" =>"sfg.sfgewinnspiel.form" ,
      //"sub" =>"sfg.sfgewinnspiel.sub" ,
      //"register" =>"sfm.sfmusiker.register",
      //"search" =>"sfm.sfmusiker.search",
			//"activate" =>"dsdf.dsdfmembers.activate",
      //"userpanel" =>"dsdf.dsdfmembers.user",
			//"team" =>"dsdf.dsdfmembers.team",
    );
	}

  // Alle Klassen laden
  function load_classes()
	{
		foreach ($this->classes_array as $key => $class)
		{
			if (file_exists(sfprbreakfast_path."prbclasses/$class.php"))
			{
				require_once(sfprbreakfast_path."prbclasses/$class.php");
			}
		}
	}

  // Alle Admin Klassen
  public function set_admin_classes()
	{
    $this->classes_array = array(
      "prbadmin" =>"prb.prbreakfast.admin",
		);
	}

  // Nach dem Laden der Klassen werden hier die Initial Settings getroffen.
  public function intial_settings()
	{

		/* Remove bar except for admins */
		add_action('init', array(&$this, 'prbreakfast_remove_admin_bar'), 9);

		// Styles und Scripts
		add_action('wp_enqueue_scripts', array(&$this, 'add_front_end_styles'), 9);

	}


  /* Wenn es sich um keinen Admin handelt, zeige keine Adminbar */
  function prbreakfast_remove_admin_bar()
  {
  	if (!current_user_can('administrator') && !is_admin())
  	{
  		show_admin_bar(false);
  	}
  }

  public function add_front_end_styles()
	{


    /* Bootstrap */
		//wp_register_style( 'sfgewinnspiel_bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
		//wp_enqueue_style('sfgewinnspiel_bootstrap');


		/* Font Awesome */
		//wp_register_style( 'sfgewinnspiel_font_awesome', sfgewinnspiel_url.'libs/font-awesome/font-awesome.min.css');
		//wp_enqueue_style('sfgewinnspiel_font_awesome');

		/* Custom style */
		wp_register_style( 'prbreakfast_style', sfprbreakfast_url.'templates/'.sfprbreakfast_template.'/css/default.css');
		wp_enqueue_style('prbreakfast_style');

    // parsley.js
    //wp_register_script('parsley-js', sfgewinnspiel_url.'libs/parsley/parsley.min.js',array('jquery'));
		//wp_enqueue_script('parsley-js');

    //wp_register_script('canvg-js', sfgewinnspiel_url.'js/canvg.js',array('jquery'));
		//wp_enqueue_script('canvg-js');


		//wp_register_script('sf-custom-js', sfgewinnspiel_url.'js/custom.js',array('jquery', 'parsley-js'));
		// ajaxurl mitgeben
		//wp_localize_script( 'sf-custom-js', 'Custom', array('ajaxurl'  => admin_url( 'admin-ajax.php' ),'homeurl' => home_url(), 'upload_url' => admin_url('async-upload.php')));
		//wp_enqueue_script('sf-custom-js');


	}
}
