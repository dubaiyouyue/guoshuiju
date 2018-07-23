<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.resonance.com.cn). All rights reserved. 

defined('IN_MET') or exit('No permission');

/**
 * json_encodePHP���汾����
 */
if(!function_exists('json_encode')){
    include PATH_WEB.'include/JSON.php';
    function json_encode($val){
        $json = new Services_JSON();
		$json = $json->encode($val);
        return $json;
    }
    function json_decode($val){
        $json = new Services_JSON();
        return $json->decode($val);
    }
}

/**
 * �ֶ�Ȩ�޿��ƴ�����ܺ󣨼��ܺ����URL���ݣ�
 * @param  string $string    ��Ҫ���ܻ���ܵ��ַ���
 * @param  string $operation ENCODE:���ܣ�DECODE:����
 * @param  string $key       ��Կ
 * @param  int    $expiry    ������Чʱ��
 * @return string            ���ܻ���ܺ���ַ���
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0){
    $ckey_length = 4;  
    $key = md5($key ? $key : UC_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
           return substr($result, 26);
        } else {
           return '';
        }
    }else{
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}
	

/**
 * ����Ϊ����ǰ̨ģ����ú���
 */
function codetra($content,$codetype) {
	if($codetype==1){
		$content = str_replace('+','metinfo',$content);
	}else{
		$content = str_replace('metinfo','+',$content);
	}
	return $content;
}

function imgxytype($list,$type){
	global $gz_newsimg_x,$gz_newsimg_y,$gz_productimg_x,$gz_productimg_y,$gz_imgs_x,$gz_imgs_y;
	$lists=array();
	foreach($list as $key=>$val){
		switch($val['module']){
			case 2:
				$val['img_x']=$gz_newsimg_x;
				$val['img_y']=$gz_newsimg_y;
			break;
			case 3:
				$val['img_x']=$gz_productimg_x;
				$val['img_y']=$gz_productimg_y;
			break;
			case 5:
				$val['img_x']=$gz_imgs_x;
				$val['img_y']=$gz_imgs_y;
			break;
		}
		$lists[$val[$type]]=$val;
	}
	return $lists;
}

function metmodname($module){
	$metmodname='';
	switch($module){
		case 1:
			$metmodname='about';
			break;
		case 2:
			$metmodname='news';
			break;
		case 3:
			$metmodname='product';
		    break;
		case 4:
			$metmodname='download';
		    break;
		case 5:
			$metmodname='img';
		    break;
		case 6:
			$metmodname='job';
		    break;
		case 100:
			$metmodname='product';
		    break;
		case 101:
			$metmodname='img';
		    break;
	}
	return $metmodname;
}

function template($template){
	global $_M,$metinfover;
	if(!$metinfover){
		$uifile = "met";
		$uisuffix = 'html';
	}else{
		$uifile = "v1";
		$uisuffix = 'php';
	}
	$path = PATH_WEB."templates/{$_M['config']['gz_skin_user']}/{$template}.html";
	!file_exists($path) && $path=PATH_WEB."templates/{$_M['config']['gz_skin_user']}/{$template}.php";
	!file_exists($path) && $path=PATH_WEB."public/ui/{$uifile}/{$template}.{$uisuffix}";
	return $path;
}

function footer(){
	global $output;
	$output = str_replace(array('<!--<!---->','<!---->','<!--fck-->','<!--fck','fck-->','',"\r",substr($admin_url,0,-1)),'',ob_get_contents());
    ob_end_clean();
	echo $output;
	DB::close();
	exit;
}

/**
 * ������ݺ���
 */
function cache_online(){
    global $_M;
	$query="SELECT * FROM {$_M['table']['gz_online']} WHERE lang='{$_M['lang']}' ORDER BY no_order";
	$result= DB::query($query);
	while($list = DB::fetch_array($result)){
		$data[]=$list;
	}
	return cache_page('online_'.$lang.'.inc.php',$data);
}

function cache_otherinfo($retype=1){
	global $_M;
    $data = DB::get_one("SELECT * FROM {$_M['table']['otherinfo']} WHERE lang='{$_M['lang']}' ORDER BY id");
	return cache_page('otherinfo_'.$lang.'.inc.php',$data,$retype);
}

function cache_str(){
	global $_M;
	$query = "SELECT * FROM {$_M['table']['label']} WHERE lang='{$_M['lang']}' ORDER BY char_length(oldwords) DESC";
	$result = DB::query($query);
	while($list = DB::fetch_array($result)) {
		$str_list_temp[0]=$list['oldwords'];
		if($list[url]){
			$str_list_temp[1]="<a title='$list[newtitle]' target='_blank' href='$list[url]' class='seolabel'>$list[newwords]</a>";
		}else{
			$str_list_temp[1]=$list[newwords];
		}
		$str_list_temp[2]=$list['num'];
		$str_list[]=$str_list_temp;
	}
	return cache_page("str_".$lang.".inc.php",$str_list);
}

function cache_column(){
	global $_M;//mobile
	$query="SELECT * FROM {$_M['table']['column']} WHERE lang='{$_M['lang']}' ORDER BY classtype desc,no_order";
	$result= DB::query($query);
	while($list = DB::fetch_array($result)){
		$cache_column[$list['id']]=$list;
	}
	return cache_page("column_".$lang.".inc.php",$cache_column);
}

function cache_page($file,$string,$retype=1){  
	$return = $string;
	if(is_array($string)) $string = "<?php\n return ".var_export($string, true)."; ?>";
	$string=str_replace('\n','',$string);
	if(!is_dir(PATH_WEB.'cache/'))mkdir(PATH_WEB.'cache/','0777');
	$file = PATH_WEB.'cache/'.$file;
	$strlen = file_put_contents($file, $string);
	if($retype==1){
		return $return;
	}else{
		return $strlen;
	}
}

function gz_cache($file){
    $file = PATH_WEB.'cache/'.$file;
	if(!file_exists($file))return array();
	return include $file;
}

function tmpcentarr($cd){
	global $class_list,$module_listall;
	$hngy5=explode('-',$cd);
	if($hngy5[1]=='cm')$metinfo=$class_list[$hngy5[0]];
	if($hngy5[1]=='md')$metinfo=$module_listall[$hngy5[0]][0];
	return $metinfo;
}

function metfiletype($qz){
	$list=explode(".",$qz);
	$metinfo=$list[count($list)-1];
	return $metinfo;
}
/*ȥ���ո�*/
function metdetrim($str){
    $str = trim($str);
    $str = ereg_replace("\t","",$str);
    $str = ereg_replace("\r\n","",$str);
    $str = ereg_replace("\r","",$str);
    $str = ereg_replace("\n","",$str);
    $str = ereg_replace(" ","",$str);
    return trim($str);
}
function unescape($str){ 
    $ret = ''; 
    $len = strlen($str); 

    for ($i = 0; $i < $len; $i++) { 
        if ($str[$i] == '%' && $str[$i+1] == 'u') { 
            $val = hexdec(substr($str, $i+2, 4)); 

            if ($val < 0x7f) $ret .= chr($val); 
            else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f)); 
            else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 

            $i += 5; 
        }else if ($str[$i] == '%') { 
            $ret .= urldecode(substr($str, $i, 3)); 
            $i += 2; 
        } 
        else $ret .= $str[$i]; 
    } 
    return $ret; 
}
/*�б�ҳ����ʽ*/
function list_order($listid){
switch($listid){
case '0':
$list_order=" order by top_ok desc,no_order desc,updatetime desc,id desc";
return $list_order;
break;

case '1':
$list_order=" order by top_ok desc,no_order desc,updatetime desc,id desc";
return $list_order;
break;

case '2':
$list_order=" order by top_ok desc,no_order desc,addtime desc,id desc";
return $list_order;
break;

case '3':
$list_order=" order by top_ok desc,no_order desc,hits desc,id desc";
return $list_order;
break;

case '4':
$list_order=" order by top_ok desc,no_order desc,id desc";
return $list_order;
break;

case '5':
$list_order=" order by top_ok desc,no_order desc,id asc ";
return $list_order;
break;
}
}
/*��һ����һ������*/
function pn_order($list_order,$news){
switch($list_order){
case '0':
$pn_order[0]="(updatetime > '$news[updatetime_order]') order by updatetime asc";
$pn_order[1]="(updatetime < '$news[updatetime_order]') order by updatetime desc";

$pn_order[2]="(updatetime = '$news[updatetime_order]') order by id desc";
return $pn_order;
break;

case '1':
$pn_order[0]="(updatetime > '$news[updatetime_order]') order by updatetime asc";
$pn_order[1]="(updatetime < '$news[updatetime_order]') order by updatetime desc";

$pn_order[2]="(updatetime = '$news[updatetime_order]') order by id desc";
return $pn_order;
break;

case '2':
$pn_order[0]="(addtime > '$news[addtime]') order by addtime asc";
$pn_order[1]="(addtime < '$news[addtime]') order by addtime desc";

$pn_order[2]="(addtime = '$news[addtime]') order by id desc";
return $pn_order;
break;

case '3':
$pn_order[0]="(hits > '$news[hits]') order by hits asc";
$pn_order[1]="(hits < '$news[hits]') order by hits desc";

$pn_order[2]="(hits = '$news[hits]') order by id desc";
return $pn_order;
break;

case '4':
$pn_order[0]="id > '$news[id]' order by id asc";
$pn_order[1]="id < '$news[id]' order by id desc";
return $pn_order;
break;

case '5':
$pn_order[0]="(id < '$news[id]') order by id desc";
$pn_order[1]="(id > '$news[id]') order by id asc";
return $pn_order;
break;
}
}
/*תUTF-8��*/
function utf8Substr($str, $from, $len) {
	$len = preg_match("/[\x7f-\xff]/", $str)?$len:$len*2;
	if(mb_strlen($str,'utf-8')>intval($len)){
		return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'. 
		'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s', 
		'$1',$str).".."; 
	}else{
		return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'. 
		'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s', 
		'$1',$str); 
	}
}
/*�����ؼ���*/
function get_keyword_str($str,$keyword,$getstrlen,$searchtype,$type){
	$str=str_ireplace('<p>','&nbsp;',$str);
	$str=str_ireplace('</p>','&nbsp;',$str);
	$str=str_ireplace('<br />','&nbsp;',$str);
	$str=str_ireplace('<br>','&nbsp;',$str);
	if($type){
		$searchtype=$searchtype!=2?1:0;
	}else{
		$searchtype=$searchtype!=1?1:0;
	}
	if(mb_strlen($str,'utf-8')> $getstrlen){
		$strlen = mb_strlen($keyword,'utf-8');
		if(function_exists('mb_stripos')){
			$strpos = mb_stripos($str,$keyword,0,'utf-8');
		}else{
			$strpos = mb_strpos($str,$keyword,0,'utf-8');
		}	
		$halfStr = intval(($getstrlen-$strlen)/2);
		if($strpos!=""){
			if($strpos>=$halfStr){
				$str = mb_substr($str,($strpos - $halfStr),$halfStr,'utf-8').$keyword.mb_substr($str,($strpos + $strlen),$halfStr,'utf-8');
			}else{
				$str = mb_substr($str,0,$strpos,'utf-8').$keyword.mb_substr($str,($strpos + $strlen),($halfStr*2),'utf-8');
			}	
		}else{
			$str = mb_substr($str,0,$getstrlen,'utf-8');
		}
		$metinfo=$str.'...';
		if($searchtype){
			$metinfo=str_ireplace($keyword,'<em style="font-style:normal;">'.$keyword.'</em>',$str).'...';
		}
		return $metinfo;
	}else{
		$metinfo=$str;
		if($searchtype){
			$metinfo=str_ireplace($keyword,'<em style="font-style:normal;">'.$keyword.'</em>',$str);
		}
		return $metinfo;
	}
	
}
/*ģ��δ��Ȩ*/
function authtemp($code){
global $au_site,$gz_weburl,$theme_preview;
$gz_weburl = $_SERVER['HTTP_HOST'];
if(function_exists(authcode))
run_strtext(authcode($code,DECODE,md5("metinfo")));

$au_site=explode("|",$au_site);
foreach($au_site as $val)
{
	if(stristr($gz_weburl,$val))
	{
		return;
	}
}
if($theme_preview){
var_export("-->");
echo "<script>alert(\"{$gz_weburl}δ��Ȩʹ�ô�ģ����Ѿ�����!Powered by MetInfo\");window.parent.close();</script>";
/*
window.parent.document.getElementsByName('tabs_item_set')[0].innerHTML = '';
window.parent.document.getElementsByName('tabs_item_set')[1].innerHTML = '';
window.parent.document.getElementsByName('tabs_item_set')[2].innerHTML = '';
window.parent.document.getElementsByName('tabs_item_set')[3].innerHTML = '';
*/
//okinfo("http://www.resonance.com.cn","{$gz_weburl}δ��Ȩʹ�ô�ģ����Ѿ�����! Powered by MetInfo");exit();
}
}
/*���ַ������ɴ�������*/
function run_strtext($code){
    return eval($code);
}
/*ͼƬ��ʾ��С*/
function gz_imgxy($xy,$module){
	global $gz_newsimg_x,$gz_newsimg_y,$gz_productimg_x,$gz_productimg_y,$gz_imgs_x,$gz_imgs_y;
	switch($module){
		case 'news':
			$gz_imgxy=$xy==1?$gz_newsimg_x:$gz_newsimg_y;
			break;
		case 'product':
			$gz_imgxy=$xy==1?$gz_productimg_x:$gz_productimg_y;
		    break;
		case 'img':
			$gz_imgxy=$xy==1?$gz_imgs_x:$gz_imgs_y;
		    break;
	}
	return $gz_imgxy;
}
//��ȡ��ǰҳ��URL
function request_uri(){
    $pageURL='http';
    if($_SERVER["HTTPS"]=="on")
    {
        $pageURL.="s";
    }
    $pageURL.="://";

    if($_SERVER["SERVER_PORT"]!="80")
    {
        $pageURL.=$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    }
    else
    {
        $pageURL.=$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return$pageURL;
}
function gz_rand($length){
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$password = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	}
	return $password;
}

/*����ҳ�������ű�ǩ�滻�����ݷ�ҳ*/
function contentshow($content) {
global $lang_PagePre,$lang_PageNext,$navurl,$index,$lang;
global $gz_atitle,$gz_alt,$metinfover;
$str=gz_cache('str_'.$lang.'.inc.php');
if(!$str){$str=cache_str();}
foreach ($str as $key=>$val){
	$val[3]=html_entity_decode($val[0],ENT_QUOTES,'UTF-8');
	$val[3]=str_replace(array('\\','/','.','$','^','*','(',')','-','['.']'.'{','}','|','?','+'),array('\\\\','\/','\.','\$','\^','\*','\(','\)','\-','\['.'\]'.'\{','\}','\|','\?','\+'),$val[3]);
	if($val[2]!=0){
		$tmp1 = explode("<",$content);
		$num=$val[2];
		foreach ($tmp1 as $key=>$item){
			$tmp2 = explode(">",$item);
			if (sizeof($tmp2)>1&&strlen($tmp2[1])>0) {
				if (substr($tmp2[0],0,1)!="a" && substr($tmp2[0],0,1)!="A" && substr($tmp2[0],0,6)!='script' && substr($tmp2[0],0,6)!='SCRIPT'){
					$valnum=substr_count($tmp2[1],$val[0]);
					if($num-$valnum>=0){
						$num=$num-$valnum;
					}
					else{
						$valnum=$num;
						$num=0;
					}
					$tmp2[1] = preg_replace("/".$val[3]."/",$val[1],$tmp2[1],$valnum);
					$tmp1[$key] = implode(">",$tmp2);
				}
			}
		}
		$content = implode("<",$tmp1);
	}
}
$tmp1 = explode("<",$content);
foreach ($tmp1 as $key=>$item){
	$tmp2 = explode(">",$item);
	if (substr($tmp2[0],0,1)=="a" || substr($tmp2[0],0,1)=="A"){
		$tmp2[0]=str_replace(array("title=''","title=\"\""),'',$tmp2[0]);
		if(!strpos($tmp2[0],'title')){
			$tmp2[0].=" title=\"$gz_atitle\"";
			$tmp1[$key] = implode(">",$tmp2);
		}
	}
	if (substr($tmp2[0],0,3)=="img" || substr($tmp2[0],0,3)=="IMG"){
		$tmp2[0]=str_replace(array("alt=''","alt=\"\""),'',$tmp2[0]);
		if(!strpos($tmp2[0],'alt')){
			$tmp2[0].=" alt=\"$gz_alt\"";
			$tmp1[$key] = implode(">",$tmp2);
		}
	}
}
$content = implode("<",$tmp1);
if(pageBreak($content,1)>1){
	$content = pageBreak($content);
if(!$metinfover){
	$content.="<link rel='stylesheet' type='text/css' href='{$navurl}public/css/contentpage.css' />\n"; 
	$content.="
<script type='text/javascript'>
$(document).ready(function(){
	$('#page_break .num li:first').addClass('on');
	$('#page_break .num li').click(function(){
		$('#page_break').find(\"div[id^='page_']\").hide();
		if ($(this).hasClass('on')) {
			$('#page_break #page_' + $(this).text()).show();
		} else {
			$('#page_break').find('.num li').removeClass('on'); 
			$(this).addClass('on'); 
			$('#page_break').find('#page_' + $(this).text()).show(); 
		} 
	});
});
</script>
	"; 
}
}
if($content=='<div><div id="metinfo_additional"></div></div>')$content='';
return $content;
}

/*���ݷ�ҳ*/
function pageBreak($content,$type){ 
	$content = substr($content,0,strlen($content)-6); 
    $pattern = "/<div style=\"page-break-after: always;?\">\s*<span style=\"display: none;?\">&nbsp;<\/span>\s*<\/div>/";      
	$strSplit = preg_split($pattern, $content); 
	$count = count($strSplit); 
	if($type)return $count;
	$outStr = ""; 
	$i = 1; 
	if ($count > 1 ) { 
	$outStr = "<div id='page_break'>"; 
	foreach($strSplit as $value) { 
	if ($i <= 1) { 
	$value=substr($value,5);
	$outStr .= "<div id='page_{$i}'>{$value}</div>"; 
	} else { 
	$outStr .= "<div id='page_$i' class='collapse'>$value</div>"; 
	} 
	$i++; 
	} 

	$outStr .= "<div class='num'>"; 
	for ($i = 1; $i <= $count; $i++) { 
	$outStr .= "<li>$i</li>"; 
	} 
	$outStr .= "</div></div>"; 
	return $outStr; 
	} else { 
	return $content; 
	} 
} 
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.resonance.com.cn). All rights reserved.
?>