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
class NewsshowAction extends BaseAction
{
    public function index()
    {
		$pagesss=$_GET['pagesss'];
		$cid=$_GET['cid']+0;
		$c=$_GET['c']+0;
		$id=$_GET['id']+0;
		global $now_cid_first;
		global $namemark;
		global $foldername;
		global $namemark_tpl;
		global $new_users_list;
		global $new_config_info;
		$namemark_tpl='index';
		if($_GET['new']){
			$namemark_tpl=$_GET['new'];
		}


	$prev= M('news')->where(array('id'=>array('GT',$id),'class2'=>$cid))->order('id asc')->find();
    $next= M('news')->where(array('id'=>array('LT',$id),'class2'=>$cid))->order('id desc')->find();
		//dump($list);exit;

		$now_lanmu=$this->get_column_info($cid);
		$lanmu=M('column')->where('bigclass=140')->select();
		   foreach($lanmu as $kuccc=>&$kuccv){
				$kuccv['foldername']=ucfirst($kuccv['foldername']);
			}
		$lan=M('column')->where("id=$cid")->find();
		if($pagesss) $list=M('column')->where("id=$cid")->find();
		else $list=$this->newinfo($id);
		
		//dump($new_users_list);exit;
		//记录会员积分
		//exhibitionnum
		//记录阅读记录
		$yue_array=explode(',',$new_users_list['yue']);
		if(!in_array($id,$yue_array) && $list){
			if($new_users_list['yue']) $new_users_list['yue']=$id.','.$new_users_list['yue'];
			else $new_users_list['yue']=$id;
			$syue['yue']=$new_users_list['yue'];
			$syue['jifen']=$new_users_list['jifen']+($new_config_info['gz_foottelqq']+0);
			$syuejifen=M('user')->where('id='.$new_users_list['id'])->limit(1)->save($syue);
		}
		
		
		
		
		
		$this->assign('list',$list);
		$getinfolamu=$this->get_column_info($cid);$this->assign('getinfolamu',$getinfolamu);
		
		//print_r($lan);exit;
		$blist=$this->bannerlists($c);
		$this->assign('blist',$blist);
		$this->assign('now_lanmu',$now_lanmu);
          $this->assign('lanmu',$lanmu);
            $this->assign('prev',$prev);
              $this->assign('next',$next);
              $this->assign('lan',$lan);
		//dump($list);exit;
		//$this->assign('ishome','home');namemark_tpl
        $this->display('./GZphp/Tpl/Home/Default/Newsshow_'.$namemark_tpl.'.html');
    }
}
?>