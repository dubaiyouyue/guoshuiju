<?php
/**
 * 
 * IndexAction.class.php (前台首页)
 *
 * @package      	GZPHP
 * @author          wen QQ:52009619 <admin@resonance.com.cn>
 * @copyright     	Copyright (c) 2008-2011  (http://www.resonance.com.cn)
 * @license         http://www.resonance.com.cn/license.txt
 * @version        	GzPHP企业网站管理系统 v2.1 2011-03-01 resonance.com.cn $
 */
 




//import ( "@.ORG.SignatureHelper" );
 
if(!defined("GZPHP")) exit("Access Denied"); 



class UsermemberAction extends BaseAction
{
    public function re(){
		/*
		user
		tel //手机号
		admin_qq //盐
		admin_msn //登录验证
		password //密码
		*/
		global $new_config_info;
		
		$retel=$_POST['tel']+0;
		$xm=strip_tags($_POST['xm']);
		$bm=strip_tags($_POST['bm']);
		$jihuo=$new_config_info['gz_fozzyhldianhua2'];
		$repass=$_POST['mm'];
		if(empty($retel) || empty($xm) || empty($repass) || empty($bm)) {
            //$this->error(L('信息填写不完整!'));
            exit('1');
        }

		$add['tel']=$retel;

		
		
		
				$sw['tel']=$retel;
				$admin_userid=M('user')->where($sw)->limit(1)->find();
				if($admin_userid['id']){
					echo 'sjh';
					exit;
				}
		
		
		$add['tel']=$retel;
		$add['bm']=$bm;
		$add['password']=md5($repass);
		$add['xm']=$xm;
		$add['jihuo']=$jihuo;
		
		$admin_msn=md5(time().rand(1000,9999));
		$add['admin_msn']=$admin_msn;
		$add['register_time']=time();//date('Y-m-d H:i:s');
		$admin_userid=M('user')->add($add);
		if($admin_userid){
			if($jihuo){
				cookie('admin_msn', $admin_msn, time()+36000000);
				cookie('admin_userid', $admin_userid, time()+36000000);
			}
			echo 'addok';
		}
    }

	function loginc(){
			/*global $new_config_info;
			$jihuo=$new_config_info['gz_fozzyhldianhua2'];*/
			$tel=$_POST['tel']+0;
			$recode=$_POST['mm'];

				$sw['tel']=$tel;
				$admin_userid=M('user')->where($sw)->limit(1)->find();
				if($admin_userid['id']){
					$cpassword=$admin_userid['password'];
					if($cpassword!=md5($recode)){
						echo 'sjh';
						exit;
					}
					if(!$admin_userid['jihuo']){
						echo 'jihuo';
						exit;
					}
					
					
					$admin_msn=md5(time().rand(1000,9999));
					
						
						$add['admin_msn']=$admin_msn;
						$admin_userida=M('user')->where('tel='.$tel)->limit(1)->save($add);
						if($admin_userida){
							cookie('admin_msn', $admin_msn, time()+36000000);
							cookie('admin_userid', $admin_userid['id'], time()+36000000);
							echo 'addok';
						}
				}
		
		
	}
	
	//修改密码
	function epass(){
		//$opass=$_POST['opass'];
		
		
		global $user_tel;
		
		if(!$user_tel){
			echo '未登录';
			header('Location:/');
			exit;
		}
		
		
		$npass=$_POST['npass'];
		if(!$npass){
			echo '信息填写不完整';
			exit;
		}
			
			/*$w['id']=$admin_userid;
				$admin_userid=M('user')->where($w)->limit(1)->find();
				
				
				$opass=md5(md5($opass).$admin_userid['admin_qq']);
				if(!$opass!=$admin_userid['password']){
					echo '旧密码错误';
					exit;
				}*/
				
				$admin_qq=rand(100000,999999);
				$se['admin_qq']=$admin_qq;
				$se['password']=md5(md5($npass).$admin_qq);
				$se['admin_msn']='';
				$se['admin_taobao']='';
				
				$admin_useridea=M('user')->where('tel='.$user_tel)->limit(1)->save($se);
				if($admin_useridea){
					echo 'addok';
					exit;
				}else{
					echo '修改密码失败！';
					exit;
				}

	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>