<?php
class PRBCommon
{

  // get value in admin option
  function get_value($option_id)
	{

    if (isset($this->options[$option_id]) && $this->options[$option_id] != '' ) {
      if(is_string($this->options[$option_id])){
				 return stripslashes($this->options[$option_id]);
			}else{
				 return $this->options[$option_id];
			}

    } else {
      return null;
    }
  }


  function getResponsiblePerson()
  {

    $currUser = wp_get_current_user();

    switch ($currUser->user_email) {
      case 'office@krebshilfe-bgld.at':
        $resp = 'Burgenland';
        break;
      case 'office@krebshilfe-ooe.at':
        $resp = 'Oberösterreich';
        break;
      case 'krebshilfe@i-med.ac.at':
        $resp = 'Tirol';
        break;
      case 'office@krebshilfe-ktn.at':
        $resp = 'Kärnten';
        break;
      case 'office@krebshilfe-sbg.at':
        $resp = 'Salzburg';
        break;
      case 'office@krebshilfe-vbg.at':
        $resp = 'Vorarlberg';
        break;
      case 'krebshilfe@krebshilfe-noe.at':
        $resp = 'Niederösterreich';
        break;
      case 'office@krebshilfe.at':
        $resp = 'Steiermark';
        break;
      case 'service@krebshilfe-wien.at':
        $resp = 'Wien';
        break;
      case 'service@krebshilfe.net':
        $resp = 'Admin';
        break;
      default:
        $resp = 'Admin';
        break;
    }

    return $resp;
  }

}

$key = "commmonmethods";
$this->{$key} = new PRBCommon();
