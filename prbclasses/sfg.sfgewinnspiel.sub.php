<?php
class SFGSub
{

  var $options;

  public function __construct()
	{

		$this->options = get_option('sfgewinnspiel_options');


    // Our custom post type function
    add_action( 'init', array(&$this, 'create_custom_post_type' ));

    // Custom Meta Box
    add_action( 'add_meta_boxes', array(&$this, 'create_custom_meta_box' ));


    add_action('wp_ajax_add_new_sub', array( $this, 'add_new_sub' ));
    add_action('wp_ajax_nopriv_add_new_sub', array( $this, 'add_new_sub' ));

    add_action('wp_ajax_sub_upload', array( $this, 'sub_upload' ));
    add_action('wp_ajax_nopriv_sub_upload', array( $this, 'sub_upload' ));

    add_action('wp_ajax_load_more_subs', array( $this, 'load_more_subs' ));
    add_action('wp_ajax_nopriv_load_more_subs', array( $this, 'load_more_subs' ));

    add_action('wp_ajax_roate_sub_image', array( $this, 'roate_sub_image' ));
    add_action('wp_ajax_nopriv_roate_sub_image', array( $this, 'roate_sub_image' ));

    add_action('wp_ajax_show_pic_by_id', array( $this, 'show_pic_by_id' ));
    add_action('wp_ajax_nopriv_show_pic_by_id', array( $this, 'show_pic_by_id' ));

    add_action('wp_ajax_add_design_to_sub', array( $this, 'add_design_to_sub' ));
    add_action('wp_ajax_nopriv_add_design_to_sub', array( $this, 'add_design_to_sub' ));

    add_action('wp_ajax_export_sub_print', array( $this, 'export_sub_print' ));


    add_filter( 'single_template', array( $this, 'get_custom_post_type_template' ));
  }


  public function export_sub_print()
  {

    $id= $_REQUEST['dataID'];
    $test = get_the_post_thumbnail($id);
    var_dump($test);
    die();
  }

  public function add_design_to_sub()
  {
    $teilnehmer = $_REQUEST['teilnehmer_id'];
    $imageSrc = $_REQUEST['img_src'];

    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageSrc));

    $wp_upload_dir = wp_upload_dir();
    $upDir = wp_upload_dir();



    file_put_contents($upDir['path'].'/'.$teilnehmer.'.png', $data);
    $picUrl = $upDir['url'].'/'.$teilnehmer.'.png';


    // $filename should be the path to a file in the upload directory.
    $filename = $upDir['path'].'/'.$teilnehmer.'.png';

    // The ID of the post this attachment is for.
    $parent_post_id = $teilnehmer;

    // Check the type of file. We'll use this as the 'post_mime_type'.
    $filetype = wp_check_filetype( basename( $filename ), null );

    // Get the path to the upload directory.
    $wp_upload_dir = wp_upload_dir();

    // Prepare an array of post data for the attachment.
    $attachment = array(
    	'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
    	'post_mime_type' => $filetype['type'],
    	'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
    	'post_content'   => '',
    	'post_status'    => 'inherit'
    );

    // Insert the attachment.
    $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
    require_once( ABSPATH . 'wp-admin/includes/image.php' );

    // Generate the metadata for the attachment, and update the database record.
    $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
    wp_update_attachment_metadata( $attach_id, $attach_data );

    set_post_thumbnail( $parent_post_id, $attach_id );


    die();
  }

  public function roate_sub_image()
  {

    $attach_id = $_REQUEST['id'];


    $image_path = get_attached_file($attach_id);

    $image = wp_get_image_editor( $image_path );
    if ( ! is_wp_error( $image ) ) {



        $image->rotate( -90 );




        //$image->resize(234, 234);
        $saved = $image->save( $image_path );
        // Neu thumbs
        $attach_data = wp_generate_attachment_metadata( $attach_id, $image_path );
        wp_update_attachment_metadata( $attach_id,  $attach_data );


        /*$img = wp_get_attachment_image_src($attach_id, 'sub_pic_small');

        $img = '<img src="'.$img[0].'" />';

        echo $img;*/
    }



    die();
  }

  public function load_more_subs()
  {

    $aktuellePage = $_REQUEST['sub_page'];
    $loadPage = $aktuellePage*8;

    $args = array(
    	'posts_per_page'   => 8,
    	'offset'           => $loadPage,
    	'category'         => '',
    	'category_name'    => '',
    	'orderby'          => 'date',
    	'order'            => 'DESC',
    	'include'          => '',
    	'exclude'          => '',
    	'meta_key'         => '',
    	'meta_value'       => '',
    	'post_type'        => 'teilnehmer',
    	'post_mime_type'   => '',
    	'post_parent'      => '',
    	'author'	   => '',
    	'author_name'	   => '',
    	'post_status'      => 'publish',
    	'suppress_filters' => true
    );
    $posts_array = get_posts( $args );
    $amount = count($posts_array);
    $subs = array('amount' => $amount, 'html' =>$this->get_grid_moemax_subs_by_post_array($posts_array));

    //$subs = array('test' => 'test');
    $test = json_encode($subs);
    echo $test;

    //echo $subs;
    die();
  }

  public function get_custom_post_type_template($single_template) {

     global $post;

     if ($post->post_type == 'teilnehmer') {
       $single_template =  sfgewinnspiel_path. '/templates/'.sfgewinnspiel_template .'/view/single-teilnehmer.php';
     }
     return $single_template;
  }

  /*
  * sub upload
  * Uploads subscriber picture
  * validation done before with parsleyjs
  */
  public function sub_upload()
  {
    if ( !wp_verify_nonce( $_REQUEST['nonce'], "upload_sub_picture")) {
      // TODO ERRORE zurücksenden
      exit("No naughty business please");
    }

    $movefile = wp_handle_upload($_FILES['file'], array( 'test_form' => false ));
    if ( $movefile && ! isset( $movefile['error'] ) ) {

        $wp_filetype = $movefile['type'];
        $filename = $movefile['file'];
        $wp_upload_dir = wp_upload_dir();
        $attachment = array(
            'guid' => $wp_upload_dir['url'] . '/' . basename( $filename ),
            'post_mime_type' => $wp_filetype,
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $filename);
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id,  $attach_data );





        $return = $attach_id. ';;';
        $return .= '
        <div class="sub_single_pic marginTopMedium">
          <div class="tape">
            <img src="'.sfgewinnspiel_url. 'templates/'. sfgewinnspiel_template.'/img/tape.png" />
          </div>
          <div class="image_container">
            '.wp_get_attachment_image($attach_id, "sub_pic_small").'
            <div class="image_shadow"></div>
          </div>
          <div class="sub_single_name">&nbsp;</div>
        </div>
        <div class="roate_sub_single_pic">
          <i class="fa fa-repeat fa-2x rotate" data-id="'.$attach_id.'" aria-hidden="true"></i>
          <i class="fa fa-refresh fa-spin fa-2x fa-fw" aria-hidden="true"></i>
        </div>
        ';

        $return .= ';;';
        $return .= wp_get_attachment_image($attach_id, 'full');

        $return .= ';;';
        $return .= wp_get_attachment_image($attach_id);

        echo $return;

    } else {
        /**
         * Error generated by _wp_handle_upload()
         * @see _wp_handle_upload() in wp-admin/includes/file.php
         */
        echo $movefile['error'];
    }

    die();
  }

  /*
  * Custom Meta Box
  */
  function create_custom_meta_box()
  {
    add_meta_box(
      'custom-sub-box',      // Unique ID
      'Teilnehmer Informationen',    // Title
      array(&$this, 'sub_meta_box' ),   // Callback function
      'teilnehmer',         // Admin page (or post type)
      'normal',         // Context
      'default'         // Priority
    );
  }

  /*
  * Meta Box
  * Backend Anzeigen der Teilnehmer
  */
  function sub_meta_box( $object, $box ) {

    $subMeta = get_post_meta($object->ID);

    //$test = get_the_post_thumbnail($object->ID);
    $sub_print_url = get_the_post_thumbnail_url($object->ID, 'full');

    ?>
    <div class="postbox" id="boxid">
      <div class="marginTopMedium marginBottomMedium subInfo">
        <?php
        foreach($subMeta as $key => $value) {
          $this->showSubInformation($key, $value, $object->ID);
        }
        ?>
      </div>
    </div>
    <div class="postbox" id="exportSection">
      <div><b>Druck exportieren</b></div>
      <div class="marginTopMedium"></div>
      <a href="<?php echo $sub_print_url;?>" download>Download Print</a>
    </div>

  <?php
 }

  /*
  * show Subscriber Info
  */
  function showSubInformation($field, $value, $subID) {


    switch($field) {
      case 'vorname':
        $label = 'Vorname';
        $value = $value[0];
        break;
      case 'nachname':
        $label = 'Nachname';
        $value = $value[0];
        break;
      case 'land':
        $label = 'Land';
        $value = $value[0];
        break;
      case 'zip':
        $label = 'PLZ';
        $value = $value[0];
        break;
      case 'street':
        $label = 'Straße';
        $value = $value[0];
        break;
      case 'street_number':
        $label = 'Straßen Nummer';
        $value = $value[0];
        break;
      case 'telefon':
        $label = 'Telefon';
        $value = $value[0];
        break;
      case 'e-mail':
        $label = 'E-Mail';
        $value = $value[0];
        break;
        case 'email':
          $label = 'E-Mail';
          $value = $value[0];
          break;
      case 'select_pic':
        $label = 'Ausgewähltes Bild';
        $value = wp_get_attachment_image($value[0]);
        break;
      case 'file':
        $label = 'Hochgeladenes Bild';
        $value = wp_get_attachment_image($value[0]);
        break;
      default:
        $label = '';
        $value = '';

    }

    if(!empty($label) && !empty($value) ) {

     ?>

     <div class="field">
       <div class="label">
         <?php echo $label; ?>
       </div>
       <div class="value">
         <?php echo $value;?>
       </div>
     </div>

     <?php
   }
 }

  /*
  * Custom Post Type
  * Gewinnspielteilnehmer
  */
  function create_custom_post_type() {
    register_post_type( 'teilnehmer',
    // CPT Options
        array(
            'labels' => array(
                'name' => 'Teilnehmer',
            ),
            'public'    => true,
            'show_ui'            => true,
		        'show_in_menu'       => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'teilnehmer'),
            'supports' => array('title','thumbnail')
        )
    );
  }

  /*
  * Add New Subscriper
  * Neuen Teilnehmer hinzufügen
  * Jeweils neuen Eintrag Custom Post Type Teilnehmer
  */
  function add_new_sub()
  {

    if ( !wp_verify_nonce( $_REQUEST['nonce'], "user-submit-form")) {
      // TODO ERRORE zurücksenden
      exit("No naughty business please");
    }

    // Teilnehmer aufbereiten
    $subMeta = $this->prepareSub($_REQUEST['subForm']);
    //var_dump($subMeta);
    // Neuen Teilnehmer hinzufügen
    $new_sub = array(
      'post_title'    => wp_strip_all_tags( $subMeta['vorname'] ),
      'post_status'   => 'publish',
      'post_type'     => 'teilnehmer',
      'post_author'   => 1,
    );

    // Teilnehmer hinzufügen
    $teilnehmer_id = wp_insert_post( $new_sub );

    // Alle Daten dem Teilnehmer zuordnen
    if($teilnehmer_id !== 0) {
      foreach($subMeta as $key => $value) {
        update_post_meta($teilnehmer_id, $key, $value);
      }
    }
    echo $teilnehmer_id;
    die();
  }

  /*
  * Funktion ist noch nicht optimal, da nach genauem value abgefragt wird - unbedingt überarbeiten1111!!!
  */
  private function prepareSub($form)
  {
    // Alle Daten aufbereiten für den Teilnehmer
    //$allowedInputArray = $this->getAllowedInputs();
    //var_dump($allowedInputArray);
    foreach($form as $formField) {
      //var_dump($formField);
      $testValue = ($formField['name'] == 'street' || $formField['name'] == 'street_number' || $formField['name'] == 'zip') ? 'adresse' : $formField['name'];

      // Überprüfe, ob der Wert benutzt werden darf.
      //if(in_array($testValue,$allowedInputArray)) {
        $sub[$formField['name']] = $formField['value'];
      //}

    }

    // Wenn Vor- und Nachname gesetzt ist, setze neue Variable Name aus den beiden zusammen.
    if(isset($sub['vorname']) && isset($sub['nachname'])) {
      $sub['name'] = $sub['vorname']. ' '. $sub['nachname'];
    }
    return $sub;
  }

  /*
  * Allowed Inputs
  * Es dürfen nur die Inputs benutzt werden, die auch im Backend festgelegt werden.
  */
  private function getAllowedInputs()
  {
    $allowed = $this->options['gewinnspiel_form_custom_fields'];
    $inputs = array();
    foreach($allowed as $input) {

      switch($input['type1']) {
        case 'input':

          switch($input['type2']) {
            case 'file':
              $inputs = array_merge($inputs, array('_thumbnail_id'));
              break;
            default:
              $inputs = array_merge($inputs, array(strtolower($input['value'])));
          }
          break;
        case 'select':

          switch($input['type4']) {
            case 'select_pic':
              $inputs = array_merge($inputs, array(strtolower($input['type4'])));
              break;
          }
          break;

      }


    }

    return $inputs;
  }

  public function get_grid_moemax_subs_by_post_array($posts_array)
  {
    $subs = '';
    foreach($posts_array as $postObj) {
      //var_dump($postObj);
      // Nur Anzeigen, wenn auch ein Bild vorhanden ist
      if(has_post_thumbnail($postObj->ID)) {


        $subs .=  '<div class="sf_sub_single_container col-md-3 marginBottomBig">';
        $subs .=   '<div class="sub_single_pic marginTopMedium">';
        $subs .=     '<div class="tape">';
        $subs .=       '<img src="'.sfgewinnspiel_url. 'templates/'. sfgewinnspiel_template.'/img/tape.png" />';
        $subs .=     '</div>';
        $subs .=     '<a href="'.get_post_permalink($postObj->ID).'">';
        $subs .=        '<div class="backgroundBoden">';
        $subs .=          '<img src="/wp-content/plugins/sf-gewinnspiel/templates/basic/img/bg_designer.png">';
        $subs .=        '</div>';
        $subs .=        '<div class="backgroundBett">';
        $subs .=          '<img src="/wp-content/plugins/sf-gewinnspiel/templates/basic/img/bettmock_mm.jpg">';
        $subs .=        '</div>';
        $subs .=       '<div class="image_container">';
        $subs .=         get_the_post_thumbnail($postObj->ID, "sub_pic_small_tall");
        $subs .=       '</div>';
        $subs .=     '</a>';
        $subs .=   '<div class="sub_single_name">';
        $subs .=     '<a href="'.get_post_permalink($postObj->ID).'">';
        $subs .=       $postObj->vorname;
        $subs .=     '</a>';
        $subs .=   '</div>';
        $subs .=  '</div>';
        $subs .= '</div>';
      }
    }


    //$subs = 'test';
    return $subs;
  }


  public function get_grid_subs_by_post_array($posts_array)
  {

    $subs = '';
    foreach($posts_array as $postObj) {
      //var_dump($postObj);
      // Nur Anzeigen, wenn auch ein Bild vorhanden ist
      if(has_post_thumbnail($postObj->ID)) {


        $subs .=  '<div class="sf_sub_single_container col-md-3 marginBottomBig">';
        $subs .=   '<div class="sub_single_pic marginTopMedium">';
        $subs .=     '<div class="tape">';
        $subs .=       '<img src="'.sfgewinnspiel_url. 'templates/'. sfgewinnspiel_template.'/img/tape.png" />';
        $subs .=     '</div>';
        $subs .=     '<a href="'.get_post_permalink($postObj->ID).'">';
        $subs .=       '<div class="image_container">';
        $subs .=         get_the_post_thumbnail($postObj->ID, "sub_pic_small");
        $subs .=         '<div class="image_shadow"></div>';
        $subs .=       '</div>';
        $subs .=     '</a>';
        $subs .=   '<div class="sub_single_name">';
        $subs .=     '<a href="'.get_post_permalink($postObj->ID).'">';
        $subs .=       $postObj->vorname;
        $subs .=     '</a>';
        $subs .=   '</div>';
        $subs .=  '</div>';
        $subs .= '</div>';
      }
    }


    //$subs = 'test';
    return $subs;
  }

  public function showMoemaxSubGrid($atts)
  {
    ob_start();

    $args = array(
    	'posts_per_page'   => 8,
    	'offset'           => 0,
    	'category'         => '',
    	'category_name'    => '',
    	'orderby'          => 'date',
    	'order'            => 'DESC',
    	'include'          => '',
    	'exclude'          => '',
    	'meta_key'         => '',
    	'meta_value'       => '',
    	'post_type'        => 'teilnehmer',
    	'post_mime_type'   => '',
    	'post_parent'      => '',
    	'author'	   => '',
    	'author_name'	   => '',
    	'post_status'      => 'publish',
    	'suppress_filters' => true
    );
    $posts_array = get_posts( $args );

    $subs = $this->get_grid_moemax_subs_by_post_array($posts_array);
    ?>
    <div class="sf_sub_grid">
      <?php
        echo $subs;
      ?>

    </div>
    <div class="clear"></div>
    <div class="sf_load_more">
      <div class="sf_load_more_container">
        <div class="sf_load_more_wrap">
          <div class="sf_load_more_wrap_inner">
            <div class="sf_load_more_text">
              Load More
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="sf_load_more loading">
      <div class="sf_load_more_container">
        <div class="sf_load_more_wrap">
          <div class="sf_load_more_wrap_inner">
            <div class="sf_load_more_text">
              <i class="fa fa-spinner fa-2x fa-pulse" aria-hidden="true"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php
    //assign the file output to $content variable and clean buffer
		$content = ob_get_clean();
		return  $content;
  }

  public function showSubGrid($atts)
  {
    ob_start();

    $args = array(
    	'posts_per_page'   => 8,
    	'offset'           => 0,
    	'category'         => '',
    	'category_name'    => '',
    	'orderby'          => 'date',
    	'order'            => 'DESC',
    	'include'          => '',
    	'exclude'          => '',
    	'meta_key'         => '',
    	'meta_value'       => '',
    	'post_type'        => 'teilnehmer',
    	'post_mime_type'   => '',
    	'post_parent'      => '',
    	'author'	   => '',
    	'author_name'	   => '',
    	'post_status'      => 'publish',
    	'suppress_filters' => true
    );
    $posts_array = get_posts( $args );

    $subs = $this->get_grid_subs_by_post_array($posts_array);
    ?>
    <div class="sf_sub_grid">
      <?php
        echo $subs;
      ?>

    </div>
    <div class="clear"></div>
    <div class="sf_load_more">
      <div class="sf_load_more_container">
        <div class="sf_load_more_wrap">
          <div class="sf_load_more_wrap_inner">
            <div class="sf_load_more_text">
              Load More
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="sf_load_more loading">
      <div class="sf_load_more_container">
        <div class="sf_load_more_wrap">
          <div class="sf_load_more_wrap_inner">
            <div class="sf_load_more_text">
              <i class="fa fa-spinner fa-2x fa-pulse" aria-hidden="true"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php
    //assign the file output to $content variable and clean buffer
		$content = ob_get_clean();
		return  $content;
  }

}

$key = "sub";
$this->{$key} = new SFGSub();
