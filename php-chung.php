<?php  
	function XuatThongBao($msg) {
	    echo "<script type='text/javascript'>alert('$msg');</script>";
	}

	function moveTo($url) {
    	echo "<script type='text/javascript'>document.location.href='{$url}';</script>";
    }

    function convert_to_normal_text($text) {
	    $normal_characters = "a-zA-Z0-9\s`~!@#$%^&*()_+-={}|:;<>?,.\/\"\'\\\[\]";
	    $normal_text = preg_replace("/[^$normal_characters*]/", ".", $text);
	    
	    return $normal_text;
	}

?>