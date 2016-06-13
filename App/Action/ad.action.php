<?
header("Content-type: text/html; charset=utf-8");

/**
 * 广告管理
 *
 * @package    Action
 * @author     Far
 * @since      2016年6月7日10:29:46
 */
class AdAction extends AppAction
{
       //列表页
        public function index()
        {
            $page 	= $this->input('page', 'int', '1');
            $params['pages'] 	= $this->input('pages', 'int', '1');
            $params['module'] 	= $this->input('module', 'int', '0');
            $res        = $this->load('ad')->getAdList($params,$page, $this->rowNum);
            $total 	= empty($res['total']) ? 0 : $res['total'];
            $list 	= empty($res['rows']) ? array() : $res['rows'];
            $pager 	= $this->pager($total, $this->rowNum);
            $pageBar 	= empty($list) ? '' : getPageBar($pager);
            $this->set("pageBar",$pageBar);
            $this->set("list",$list);
//            echo "<pre>";
//            var_dump($params);
            $this->set('s', $params);
            $this->set("module_type",C("MODULE_TYPE"));
            $this->display();

        }
        
        /**
	 * 编辑广告图
	 * 
	 * @author	Far
	 * @since	2016-06-12
	 */
	public function editPic()
	{
		$pages  = $this->input('pages', 'int', '');
                $module = $this->input('module', 'int', '');
                $sort   = $this->input('sort', 'int', '');

		if ( empty($pages) || empty($sort)){
			MessageBox::halt('参数错误');
		}

		$info = $this->load('ad')->getPagesList($pages, $module, $sort);
		$this->set('info', $info['rows']);
                $this->set('total', $info['total']);
		$this->display('ad/ad.pic.html');
	}
        
        /**
	 * 创建或编辑广告图
	 * 
	 * @author	Far
	 * @since	2016-06-12
	 */
	public function setAd()
	{       
                $id1 	= $this->input('id1', 'int', '');
		$pic1 	= $this->input('pic1', 'string', '');
		$link1 	= $this->input('link1', 'string', '');
		$alt1 	= $this->input('alt1', 'string', '');
                
                $id2 	= $this->input('id2', 'int', '');
                $pic2 	= $this->input('pic2', 'string', '');
		$link2 	= $this->input('link2', 'string', '');
		$alt2 	= $this->input('alt2', 'string', '');

                if(!empty($pic1) && !empty($link1) && !empty($alt1)){
                    $data1 = array(
				'pic'        => $pic1,
				'link'       => $link1,
				'alt'        => $alt1,
				'isUse'      => 1,
                    
			);
                    $res1 = $this->load('ad')->setAd($data1, $id1);
                }else{
                    $this->returnAjax(array('code'=>2,'msg'=>'请把数据填写完整!'));
                }
                
                //第二张广告
                $res2 = true;
		if(!empty($pic2) || !empty($link2) || !empty($alt2)){
                    $data2 = array(
				'pic'        => $pic2,
				'link'       => $link2,
				'alt'        => $alt2,
				'isUse'      => 1,
                    
			);
                    $res2 = $this->load('ad')->setAd($data2, $id2);
                }
                
        if ( $res1 || $res2){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}
        
        /**
	 * 删除
	 * 
	 * @author	Jeany
	 * @since	2016-02-18
	 * @access	public
	 * @return	void
	 */
	public function delAd()
	{	
		$id 	= $this->input('id', 'int', '0');
		
		$res  = $this->load('ad')->delAd($id);
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'删除错误'));
		
	}
}

?>