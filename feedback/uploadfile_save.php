<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.resonance.com.cn). All rights reserved.
require_once '../include/common.inc.php';
$gz_file_maxsize=$gz_file_maxsize*1024*1024;
	
function upload($form, $gz_file_format) {
	global $lang_js22,$lang_js23,$lang_fileOK,$lang_fileError1,$lang_fileError2,$lang_fileError3,$lang_fileError4;
    if (is_array($form)) {
      $filear = $form;
    } else {
      $filear = $_FILES[$form];
    }
    if (!is_writable('../upload/file/')) {
	 okinfo('javascript:history.go(-1);',$lang_js22);  
    }
//Get extension
	$ext = explode(".", $filear["name"]);
	$extnum=count($ext)-1;
	$ext = $ext[$extnum];
//Save the settings file name
    $name = gz_rand(32).".".$ext;
	if(strtolower($ext)=='php'||strtolower($ext)=='aspx'||strtolower($ext)=='asp'||strtolower($ext)=='jsp'||strtolower($ext)=='js'||strtolower($ext)=='asa'){
		okinfo('javascript:history.go(-1);',$lang_js23);
	}
    if ($gz_file_format != "" && !in_array(strtolower($ext), explode("|",
        strtolower($gz_file_format)))) { 
		okinfo('javascript:history.go(-1);',$lang_js23);
    }
     
	 if (!copy($filear["tmp_name"],"../upload/file/".$name)) {
     $errors = array(0 => "$lang_fileOK",  1 =>"$lang_fileError1 ", 2 => "$lang_fileError2 ", 3 => "$lang_fileError3 ", 4 => "$lang_fileError4 ");
    } else {
      @unlink($filear["tmp_name"]); //Delete temporary files
    }
    return $name;
}
 
foreach($fd_para as $key=>$val)
{
	$downloadurl=$val['para'];
	
	if($val[type]==5 && isset($_FILES[$downloadurl]) && $_FILES[$downloadurl]['name']!='')
	{	
		$file_size=$_FILES[$downloadurl]['size'];
		if($file_size>$gz_file_maxsize){
		okinfo('javascript:history.go(-1)',$lang_filemaxsize);
		exit;
		} 
		$$downloadurl=upload($downloadurl,$gz_file_format);
	}
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.resonance.com.cn). All rights reserved.
?>