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
class ExaminationAction extends BaseAction
{
    public function index()
    {
		$cid=$_GET['cid']+0;
		$tid=$_GET['tid']+0;
		$ans=$_GET['ans'];
		global $now_cid_first;
		global $namemark;
		global $foldername;
		global $namemark_tpl;
		global $new_config_info;
		global $new_users_list;
		$blist=$this->bannerlists(479);
		$getinfolamu=$this->get_column_info(479);$this->assign('getinfolamu',$getinfolamu);
		
		   $lanmu=M('column')->where('bigclass=479')->order('no_order asc,id asc')->select();
		
		
		
		
		
		  /*  $news=M('news')->where("class1=479")->select();
		  
		   
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
		

		
		$mmid=$_GET['mm']+0;
		if($mmid){
			
				$otextlist=M('tnews')->where('id='.$mmid.' and uid='.$new_users_list['id'])->find();
				$otextlist_id=$otextlist['id'];
				
				$otextlist_tid_arr=explode(',',$otextlist['tid']);//测试文章id
				if(!empty($otextlist_tid_arr)) $nniddd=$otextlist_tid_arr['0'];
			
			$this->assign('blist',$blist);
			$this->assign('tid',$tid);
			$this->assign('gtextlist',$gtextlist);
			$this->assign('introduce',$introduce);
			$this->assign('lanmu',$lanmu);
			$this->assign('lan',$lan);
			$this->assign('cj',($otextlist['cj']+0));
			$this->display('./GZphp/Tpl/Home/Default/'.$foldername.'_get.html');
			exit;
		}
		
		
		
		//查询是否有未测试完的题目
		$otextlist=M('tnews')->where('fend=0 and uid='.$new_users_list['id'])->order('id desc')->find();
		$otextlist_id=$otextlist['id'];
		
		$otextlist_tid_arr=explode(',',$otextlist['tid']);//测试文章id
		if(!empty($otextlist_tid_arr)){
			//$nniddd=$otextlist_tid_arr['0'];
			//$nniddd_rand=rand(0,(count($otextlist_tid_arr)-1));
			$nniddd=$otextlist_tid_arr[array_rand($otextlist_tid_arr)];
		}
		
		

		
		
		

		
		
		
		//提交答案
		if($tid && $ans && in_array($tid,$otextlist_tid_arr)){
			$cdaan=M('news')->where('id='.$tid)->find();
			//dump($cdaan);exit;
			$hhyy=explode("\r\n",$cdaan['dezxcsption']);
			
				$nidsarr=str_ireplace($tid,'',$otextlist['tid']);
				$nidsarrarr=explode(',',$nidsarr);
				$nid_srs='';
				foreach($nidsarrarr as $k=>$v){
					if($v){
						if($nid_srs) $nid_srs=$v.','.$nid_srs;
						else $nid_srs=$v;
					}
				}
				//echo $nid_srs;exit;
				$stadd['tid']=$nid_srs;
				$stadd['num']=$otextlist['num']+1;
				if(($otextlist['num']+1)>$otextlist['znum']) $stadd['num']=$otextlist['znum'];
				if($ans==md5($hhyy['0'])) $stadd['cj']=$otextlist['cj']+$otextlist['np']+0;//ceil($new_config_info['gz_footteldianhua']/$new_config_info['gz_headTeless']);
				if(!$nid_srs){
					$stadd['ftime']=time();
					$stadd['fend']=1;
				}
				$atsd=M('tnews')->where('id='.$otextlist_id.' and uid='.$new_users_list['id'])->save($stadd);
			
				if(!$nid_srs && $atsd){
					header('Location:/index.php/Home/Examination/index/cid/479/mm/'.$otextlist_id);
					exit;
				}
				
				
					//查询是否有未测试完的题目
					$otextlist=M('tnews')->where('fend=0 and uid='.$new_users_list['id'])->order('id desc')->find();
					$otextlist_id=$otextlist['id'];
					
					$otextlist_tid_arr=explode(',',$otextlist['tid']);//测试文章id
					if(!empty($otextlist_tid_arr)) $nniddd=$otextlist_tid_arr[array_rand($otextlist_tid_arr)];//$nniddd=$otextlist_tid_arr['0'];
				
		}
		
		
		
		

		if($otextlist_id && $nniddd){
			//有没测试完的题目
			$gtextlist=M('news')->where('id='.$nniddd)->find();
			$tid=$nniddd;
		}else{
			//生成测试
			//随机选题
			if(!$new_config_info['gz_footteldianhua2']){
				
				//$new_config_info['gz_headTeless']
				$textlist=M('news')->where('class1=479')->select();
				shuffle($textlist);
				//$textlist=array_rand($textlist,3);
				foreach($textlist as $k=>$v){
					if($k<$new_config_info['gz_headTeless']){
						$ntextlist[]=$v;
						if($tidlista) $tidlista=$v['id'].','.$tidlista;
						else $tidlista=$v['id'];
					}else{
						break;
					}
				}
				//dump($ntextlist);exit;
			}else{
				$nid_srs='';
				$hhyy=explode("\r\n",$new_config_info['gz_headFaxess']);
				foreach($hhyy as $k=>$v){
					if($v){
						if($nid_srs) $nid_srs=$v.','.$nid_srs;
						else $nid_srs=$v;
					}
				}
				
				
				$low['id']  = array('in',$nid_srs);
				$order='field(id,'.$nid_srs.')';
				$textlist=M('news')->where($low)->order($order)->select();
				
				foreach($textlist as $k=>$v){
					$ntextlist[]=$v;
					if($tidlista) $tidlista=$v['id'].','.$tidlista;
					else $tidlista=$v['id'];
				}
			}
			$ttime=time();
			$tadd['title']=date('Y-m-d H:i:s',$ttime).' '.$ntextlist['0']['title'];
			$tadd['uid']=$new_users_list['id'];
			$tadd['tid']=$tidlista;
			$tadd['ttime']=$ttime;
			
			$tidlista_arr=explode(',',$tidlista);
			$tidlista_arr_num=count($tidlista_arr);
			$tadd['znum']=$tidlista_arr_num;
			$tadd['num']=1;
			$np=ceil($new_config_info['gz_footteldianhua']/$tidlista_arr_num);
			$tadd['np']=$np;
			
			$tnewsid=M('tnews')->add($tadd);
			
			$nidsarr=$tidlista;
			$tid=$ntextlist['0']['id'];
			$gtextlist=$ntextlist['0'];
		}
			//dump($gtextlist);exit;
		
		
		
		
		
		
		
		
		
		//dump($otextlist_tid_arr);exit;
		$this->assign('blist',$blist);
		$this->assign('tid',$tid);
		if(!$otextlist['znum']) $otextlist['znum']=$tidlista_arr_num;
		$this->assign('znum',$otextlist['znum']);
		if(!$otextlist['num']) $otextlist['num']=1;
		$this->assign('num',$otextlist['num']);
		$this->assign('gtextlist',$gtextlist);
		$this->assign('introduce',$introduce);
		$this->assign('lanmu',$lanmu);
		$this->assign('lan',$lan);
		
		
		
		
        $this->display('./GZphp/Tpl/Home/Default/'.$foldername.'_'.$namemark_tpl.'.html');
    }
}
?>