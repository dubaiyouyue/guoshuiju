<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.resonance.com.cn). All rights reserved. 
require_once '../include/common.inc.php';
if(!$id && $class1)$id = $class1;
if(!is_numeric($id))okinfo('../404.html');
$show = $db->get_one("SELECT * FROM $gz_column WHERE id='$id' and module=1");
if(!$show||!$show['isshow']){
okinfo('../404.html');
}


//$show_rrruoonewcc = $db->get_one("SELECT * FROM $gz_column WHERE bigclass='$id' order by no_order");
//dump($s4);exit;

//end

$metaccess=$show[access];
if($show[classtype]==3){
$show3 = $db->get_one("SELECT * FROM $gz_column WHERE id='$show[bigclass]'");
$class1=$show3[bigclass];
$class2=$show[bigclass];
$class3=$show[id];
}else{
$class1=$show[bigclass]?$show[bigclass]:$id;
$class2=$show[bigclass]?$id:"0";
$class3=0;
}

require_once '../include/head.php';
$class1_info=$class_list[$class1];
//$class1_list=$class1_info;
$class2_info=$class_list[$class2];
$class3_info=$class_list[$class3];
$show[content]=contentshow('<div>'.$show[content].'</div>');
$show[description]=$show[description]?$show[description]:$gz_description;
$show[keywords]=$show[keywords]?$show[keywords]:$gz_keywords;
$gz_title=$gz_title?$show['name'].'-'.$gz_title:$show['name'];
if($show['ctitle']!='')$gz_title=$show['ctitle'];
require_once '../public/php/methtml.inc.php';
include template('show');
footer();
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.resonance.com.cn). All rights reserved.
?>