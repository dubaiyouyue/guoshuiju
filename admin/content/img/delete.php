<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.resonance.com.cn). All rights reserved. 
$depth='../';
require_once $depth.'../login/login_check.php';
$backurl ="../content/img/index.php?anyid={$anyid}&lang=$lang&class1=$class1";
$backurl.=$cengci==1?'':($cengci==2?'&class2='.$class2:'&class2='.$class2.'&class3='.$class3);
if($gz_htmpagename==2)$folder=$db->get_one("select * from $gz_column where id='$class1'");
if($action=="del"){
	$allidlist=explode(',',$allid);
	foreach($allidlist as $key=>$val){
		if($gz_webhtm!=0 or $metadmin[pagename]){
			$img_list=$db->get_one("select * from $gz_img where id='$val'");
			if($gz_htmpagename==1)$updatetime=date('Ymd',strtotime($img_list[updatetime]));	
			deletepage($folder[foldername],$val,'showimg',$updatetime,$img_list[filename]);
			$declass[$img_list['class2']][$img_list['class3']]=1;
		}
		if(!$gz_recycle){
			$query = "delete from $gz_plist where listid='$val' and module='5'";
			$db->query($query);
		}
		$query = $gz_recycle?"update $gz_img set recycle='5',updatetime='".date('Y-m-d H:i:s')."' where id='$val'":"delete from $gz_img where id='$val'";
		$db->query($query);
		if(!$gz_recycle){if($img_list){delimg($img_list,1,2);}else{delimg($val,3,2);}}
	}
	$htmjs =indexhtm().'$|$';
	if($gz_webhtm==2 or $metadmin[pagename]){
		$htmjs.= classhtm($class1,0,0,0,1,0).'$|$';
		foreach($declass as $key1=>$val1){
			$htmjs.= classhtm($class1,$key1,0,0,2,0).'$|$';
			foreach($val1 as $key2=>$val2){
				$htmjs.= classhtm($class1,$key1,$key2,0,3,0);
			}
		}
	}
	$gent='../../sitemap/index.php?lang='.$lang.'&htmsitemap='.$gz_member_force;
	if($gz_webhtm==2){
		metsave($backurl,'',$depth,$htmjs,$gent);
	}else{
		metsave($backurl,'',$depth,'',$gent);
	}
}elseif($action=="editor"){
	$allidlist=explode(',',$allid);
	foreach($allidlist as $key=>$val){
		$no_order = "no_order_$val";
		$no_order = $$no_order;
		$query = "update $gz_img SET
			no_order   = '$no_order',
			lang       = '$lang'
			where id='$val'";
		$db->query($query);
	}
	$htmjs =indexhtm().'$|$';
	$htmjs.=classhtm($class1,$class2,$class3);
	$gent='../../sitemap/index.php?lang='.$lang.'&htmsitemap='.$gz_member_force;
	metsave($backurl,'',$depth,$htmjs,$gent);
}else{
	$img_list = $db->get_one("SELECT * FROM $gz_img WHERE id='$id'");
	if(!$img_list)metsave('-1',$lang_dataerror,$depth);
	if($gz_webhtm!=0 or $metadmin[pagename]){
		if($gz_htmpagename==1)$updatetime=date('Ymd',strtotime($img_list[updatetime]));
		deletepage($folder[foldername],$id,'showimg',$updatetime,$img_list[filename]);
	}
	if(!$gz_recycle){
		$query = "delete from $gz_plist where listid='$id' and module='5'";
		$db->query($query);
	}
	$query = $gz_recycle?"update $gz_img set recycle='5',updatetime='".date('Y-m-d H:i:s')."' where id='$id'":"delete from $gz_img where id='$id'";
	$db->query($query);
	if(!$gz_recycle){if($img_list){delimg($img_list,1,2);}else{delimg($id,3,2);}}
	$htmjs =indexhtm().'$|$';
	$class1=$img_list[class1];
	$class2=$img_list[class2];
	$class3=$img_list[class3];
	$htmjs.=classhtm($class1,$class2,$class3);
	$gent='../../sitemap/index.php?lang='.$lang.'&htmsitemap='.$gz_member_force;
	metsave($backurl,'',$depth,$htmjs,$gent);
}
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.resonance.com.cn). All rights reserved.
?>
