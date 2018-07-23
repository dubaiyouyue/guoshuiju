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
if(!defined("GZPHP")) exit("Access Denied"); 
class MembersAction extends BaseAction
{
    public function index()
    {
		$cid=$_GET['cid']+0;
		global $now_cid_first;
		global $namemark;
		global $foldername;
		global $namemark_tpl;
		global $new_config_info;
		global $new_users_list;
		$blist=$this->bannerlists(478);
		$getinfolamu=$this->get_column_info(478);$this->assign('getinfolamu',$getinfolamu);
		//dump($getinfolamu);exit;
		   $lanmu=M('column')->where('bigclass=478')->order('no_order asc,id asc')->select();
		
		
		//获取会员个人信息
		/*$uid=cookie('admin_userid')+0;
		$userlist=M('user')->where('id='.$uid)->limit(1)->find();*/
		//$this->assign('userlist',$new_users_list);
		//dump($new_users_list);exit;
		//end
		$nnn=md5($user_tel.(cookie('admin_userid')+0)).'.jpg';
		$admin_userid=cookie('admin_userid');
		if(!file_exists('tx/'.$nnn)) $user_tx='/tx/0.jpg';
		else $user_tx='/tx/'.$nnn;
		$this->assign('user_tx',$user_tx);
		
		//阅读积分记录
		

		
		
		$low['id']  = array('in',$new_users_list['yue']);
		$order='field(id,'.$new_users_list['yue'].')';
		$p=$_GET['p']+0;
		$m=11;
			$allpage=M('news')->field('id')->where($low)->select();;
			$allpage=ceil(count($allpage)/$m);
			
			if($p<1) $p=1;
			if($p>$allpage) $p=$allpage;
			
			$this->assign('allpage',$allpage);
			$this->assign('p',$p);
			
			$n=($p-1)*$m;
			
		$loidlist=M('news')->where($low)->order($order)->limit($n,$m)->select();
		
		
		$this->assign('loidlist',$loidlist);
		//dump($loidlist);exit;
		
		  /*  $news=M('news')->where("class1=478")->select();
		  
		   
		   foreach($news as $ck=>$cv){
					foreach($lanmu as $pk=>&$pv){
						if($pv['id']==$cv['class2']){
							
							$pv['imgurl']=$cv['imgurl'];
							
						}
					}
				}			 */
		    
		   
		  foreach($lanmu as $kuccc=>&$kuccv){
				$kuccv['foldername']=ucfirst($kuccv['foldername']);
			}  
			
			
			
		    $lan=M('column')->where("id=$cid")->find();

		    $introduce=M('news')->where("class2=$cid")->order('id desc')->find();

		
		
		if($now_cid_first==$cid){
			//一级栏目
			$newlist=$this->new_page_info('131,132,133,134');
			foreach($newlist as $k=>$v){
				$new_list[$v['id']]=$v;
			}
			$this->assign('newlist',$new_list);
		}else{
			$newlist=$this->new_page_info($cid);
			$this->assign('newlist',$newlist);
		}
		
		
		//dump($newlist);exit();
		
		$this->assign('blist',$blist);
		$this->assign('introduce',$introduce);
		$this->assign('lanmu',$lanmu);
		$this->assign('lan',$lan);
		
		if($_GET['mp']=='epass'){
			
			$this->display('./GZphp/Tpl/Home/Default/Members_epass.html');
		}else if($_GET['mp']=='texc'){
			
			
			
			
			
			
			$allpage=M('tnews')->field('id')->where('fend!=0 and uid='.$new_users_list['id'])->select();;
			$allpage=ceil(count($allpage)/$m);
			
			if($p<1) $p=1;
			if($p>$allpage) $p=$allpage;
			

			
			$n=($p-1)*$m;
			$loidlisttxx=M('tnews')->where('fend!=0 and uid='.$new_users_list['id'])->order('id desc')->limit($n,$m)->select();
		
			
			
			$this->assign('allpage',$allpage);
			$this->assign('p',$p);
			
			
			
			
			
			
			
			$this->assign('loidlisttxx',$loidlisttxx);
			$this->display('./GZphp/Tpl/Home/Default/Members_texc.html');
		}else{
			
			$this->display('./GZphp/Tpl/Home/Default/'.$foldername.'_'.$namemark_tpl.'.html');
		}
		
        
    }
	function xepass(){
		global $new_users_list;
		
		$opass=$_POST['opass'];
		$npass=$_POST['npass'];
		
		if($new_users_list['password']!=md5($opass)){
			echo '1'; //旧密码错误
			exit;
			
		}else{
			$add['admin_msn']='';
			$add['password']=md5($npass);
			$admin_userida=M('user')->where('id='.$new_users_list['id'])->limit(1)->save($add);
			if($admin_userida){
				echo 'addok';
				exit;
			}
		}
	}
	
	
	function epass(){
		global $new_users_list;
		$nnn=md5($user_tel.(cookie('admin_userid')+0)).'.jpg';
		$admin_userid=cookie('admin_userid');
		if(!file_exists('tx/'.$nnn)) $user_tx='/tx/0.jpg';
		else $user_tx='/tx/'.$nnn;
		$this->assign('user_tx',$user_tx);
		
		//阅读积分记录
		$low['id']  = array('in',$new_users_list['yue']);
		$order='field(id,'.$new_users_list['yue'].')';
		$loidlist=M('news')->where($low)->order($order)->select();
		$this->assign('loidlist',$loidlist);
		
		$this->display('./GZphp/Tpl/Home/Default/Members_epass.html');
	}
	function Uptx(){
		
		$nnn=md5($user_tel.(cookie('admin_userid')+0));
		if(move_uploaded_file($_FILES["file"]["tmp_name"],'tx/'.$nnn.'.jpg')){
			echo 'upok';
			exit;
		}
	}
	
}
?>