<?
header("Content-type: text/html; charset=utf-8"); 
/**
 * 首页模块设置
 *
 * @package	Action
 * @author	dower
 * @since	2016-03-21
 */
class packageAction extends AppAction
{
	//	public $debug = true;
	public $rowNum  = 10;
	/**
	 * 成功案例列表
	 * @throws SpringException
	 */
	public function index(){
		//参数
		$params = array();
		$page 	= $this->input('page', 'int', '1');
        $params['keyword'] = $this->input('title', 'string');
		//得到列表
		$res 	= $this->load('package')->getList($params, $page, $this->rowNum);
		//分页
		$total 	= empty($res['total']) ? 0 : $res['total'];
		$list 	= empty($res['rows']) ? array() : $res['rows'];
		$pager 		= $this->pager($total, $this->rowNum);
		$pageBar 	= empty($list) ? '' : getPageBar($pager);
		//分配数据
		$this->set('total', $total);
		$this->set("pageBar",$pageBar);
		$this->set('s', $params);
		$this->set('list', $res['rows']);
		$this->set('keyword', $params['keyword']);
		$this->display();
	}

	/**
	 * 创建案例的弹窗及处理
	 * @throws SpringException
	 */
	public function add()
	{
        $id = $this->input('id','int');
        $allphone = $this->load('phone')->getAllPhone();
        $this->set('tmTop', C('ISTOP_LIST'));
        $this->set('tmLabel', C("TM_LABEL"));
        $this->set('allphone', $allphone);
		//修改页面
		if (!empty($id)){
            $info = $this->load('package')->getPackageInfo($id);
            $number_list = $this->load('package')->getPackageItemByPid($id);

            $this->set('info',$info);
            $this->set('number_list',json_encode($number_list['rows']));
            $this->set('id',$id);
            
            //获取SEO设置
            $seoInfo = $this->load('seo')->getInfoByType(16,$id);
            $this->set('seoInfo', $seoInfo);
        }
			$this->display();
	}
    

    /**
     * 获取商标信息
     */
    public function getTminfo()
    {
        $number	= $this->input("number","string");
        $pid	= $this->input("pid","int");
        $up	= $this->input("up","int",0);//是否是修改页面获取数据
        
        if ( empty($number) ) $this->returnAjax(array('code'=>1,'msg'=>'商标号不能为空'));
        //判断商标是否存在
        $info   = $this->load('internal')->existSale($number,1);
        if ( !$info) $this->returnAjax(array('code'=>3,'msg'=>$number.'对不起,该商标不在出售中'));
        
        //判断商标是否已打过包
        if(empty($up)){
            $info_count   = $this->load('package')->getNumberCount($number);
            if ($info_count>0) $this->returnAjax(array('code'=>3,'msg'=>$number.'对不起,该商标已打过包啦'));
        }
        
        if(!empty($pid)){
            $itemInfo = $this->load('package')->getPackageItemInfo($pid,$number);
        }
        
        //正常状态结果
        $data['number']     = $number;
        $data['name']       = $info['name'];
        $data['class_str']  = $info['class'];
        $data['thum']       = mbSub($info['class'],0,18);
        
        $img = $this->load('internal')->saleImg($number);
        $data['img']        = $img;
        
        $this->set('info',$data);
        $this->set('itemInfo',$itemInfo);
        $tm = $this->fetch();
        $this->returnAjax(array('code'=>0,'msg'=>$tm));
    }
    
    /**
     * 添加商标--通过商标号
     */
    public function addNumber(){
        $data = $this->getFormData();
        $rst = $this->load('package')->insertPackage($data,45,$data['id']);
        if($rst['code']!=1){
            //设置SEO的信息
            $sid = $this->input('sid', 'int', '0');
            $seo_data['vid']            = $rst['code'];
            $seo_data['seotitle']       = $this->input('seo_title', 'string', '');
            $seo_data['keyword']        = $this->input('seo_keyword', 'string', '');
            $seo_data['description']    = $this->input('seo_description', 'string', '');
            $seo_data['isUse']          = $this->input('seo_isUse', 'int', '1');
            $reArr = $this->load('seo')->viewSetSeo($sid,$seo_data,16);
        }
        if($data['id']){
            $this->deleteCache($data['id']);
        }
        $this->returnAjax($rst);
    }
    
    /**
     *删除报价单
     */
    public function remove(){
        $id = $this->input('id','string','');
        if(!$id) $this->returnAjax(array('code'=>1,'msg'=>'参数异常'));
        
        $id = explode(',', $id);
        foreach ($id as $v){
            if(!$v) $this->returnAjax(array('code'=>1,'msg'=>'参数异常'));
            $rst = $this->load('package')->delete($v);
            if(!$rst){
                $this->returnAjax(array('code'=>1,'msg'=>'删除'.$v.'失败'));
            }
        }
        $this->deleteCache($id);
        $this->returnAjax(array('code'=>0));
    }

    /**
     * 删除对应的缓存
     * @param $id
     */
    private function deleteCache($id){
        $this->com('redisHtml')->remove('pack_detail_'.$id);
    }

}