<?php
# MetInfo Enterprise Content Management System 
# Copyright (C) MetInfo Co.,Ltd (http://www.resonance.com.cn). All rights reserved. 

defined('IN_MET') or exit('No permission');

load::sys_class('admin');
load::sys_func('file');
load::sys_func('array');

class patch extends admin {
	public function dopatch() {
		global $_M;		
		$curl = load::sys_class('curl', 'new');
		$curl->set('file', '?n=platform&c=system&a=dopatch');
		$post_data = array('cmsver' => $_M['config']['metcms_v'], 'patch' => $_M['config']['gz_patch']);
		$difilelist = $curl->curl_post($post_data,10);
		$difilelists = stringto_array($difilelist, '|' , '*', ':');
		if ($difilelists[0][0][0] == 'suc') {
			foreach ($difilelists[1] as $keylist => $vallist) {
				$gz_patch = $vallist[0];
				unset($vallist[0]);
				foreach($vallist as $key => $val){
					$dlfile = load::sys_class('dlfile', 'new');
					$copydir = str_replace(':/admin/', ':/'.$_M['config']['gz_adminfile'].'/', ':/'.$val);
					$copydir = str_replace(':/', '', $copydir);
					$re = $dlfile->dlfile('file/v'.$_M['config']['metcms_v'].'/file/'.$val, PATH_WEB.$copydir, 'metcms');
					if($re != 1){
						break;
					}
				}
				$update_file = PATH_WEB."{$_M['config'][gz_adminfile]}/update/patch/v{$_M['config']['metcms_v']}_{$gz_patch}.class.php";
				if (file_exists($update_file)) {
					require_once $update_file;
				}
				@unlink($update_file);
				$query = "update {$_M['table']['config']} set value='{$gz_patch}' where name='gz_patch'";
				DB::query($query);	
			}
			echo 1;
		}
		else{
			echo 2;
		}
		die();
	}
}

# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.resonance.com.cn). All rights reserved.
?>