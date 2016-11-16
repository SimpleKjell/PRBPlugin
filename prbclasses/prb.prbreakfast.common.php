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
}

$key = "commmonmethods";
$this->{$key} = new PRBCommon();
