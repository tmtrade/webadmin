<?
/**
 * 出售信息
 *
 * 获取出售信息，添加出售信息
 * 
 * @package	Module
 * @author	Xuni
 * @since	2015-09-15
 */
class GuideModule extends AppModule
{
    /**
     * 引用业务模型
     */
    public $models = array(
        'sale'			=> 'sale',
		'saleview'		=> 'saleview',
		'member'		=> 'member',
		'proposer'		=> 'proposer',
        'saletrademark' => 'saletrademark',
		'group'			=> 'group',
        );

	/**
     * 初始化变量值
     * @author	Jeany
     * @since	2015-07-22
     */
    public function __construct()
    {
        $this->source			= C("SOURCE");
        $this->status			= C("DEAL_STATUS");
		$this->area				= C("AREA");
		$this->type				= C("APPROVE_TYPE");
		$this->approveStatus	= C("APPROVE_STATUS");
		$this->category			= C("CATEGORY"); //行业大分类
		$this->categorySmall	= C("CATEGORY_SMALL"); //行业小分类
		$this->types			= C("TYPES"); //商标分类
		$this->prices			= C("SEARCH_PRICE"); //底价查询分类
	
		
		$num = 45 ;
		$class = array();
		for($i=1;$i<=$num;$i++){
			$class[$i] = "第".$i."类";
		}
		$this->class  = $class;
		
    }

	/**
	 * 获取单条出售信息数据
	 * @author	Xuni
	 *
	 * @access	public
	 * @param	int		$id 	出售信息ID
	 *
	 * @return	array
	 */
    public function getInfo($id)
    {
    	return $this->import('sale')->get($id);
    }

	/**
	 * 获取特价商标出售信息列表
	 * @author	Xuni
	 *
	 * @access	public
	 * @param	array 	$id			条件
	 * @param	int		$type 		页码
	 * @param	int		$status 	页数
	 *
	 * @return	array
	 */
	public function getList($params, $page=1, $nums=10)
	{
		$result = array('total'=>0,'rows'=>array());
		if ( empty($params) ) return $result;

		$params['limit'] 	= $nums;
		$params['page']		= $page;

		$res = $this->import('sale')->findAll($params);
		$res['rows'] = $this->getRow($res['rows']);
		return $res;
	}
 

	/**
	 * 获取所有特价商标的分类
	 * @author	Xuni
	 *
	 * @access	public
	 *
	 * @return	array
	 */
	public function getAllClass()
	{
		$r['limit'] = 50;
		$r['eq'] 	= array('status'=>5);
		$r['col']	= array('class');
		$r['group']	= array('class'=>'asc');

		$res = $this->import('sale')->find($r);
		$classes = arrayColumn($res, 'class');
		return $classes;
	}
 
	
	/**
	 * 首页获取人气，特价，精品出售商标数据
	 * @author	Jeany
	 * @since	2015-09-15
	 *
	 * @access	public
	 * @param	array	
	 * @p		int
	 * @num		int
	 * @return	array
	 */
	public function getSaleList($r, $num ,$page=1)
	{
		
		$r['eq']['status']  = 5;//可出售商标
		$r['page']        	= $page;
        $r['limit']         = $num;
		
        $data = $this->import('sale')->findAll($r);
        foreach($data['rows'] as $k => & $item){
			$item['imgurl']		= $this->load('imgurl')->getUrl($item['number']); 
        }
		return $data;
	}
	
	/**
	 * 获取最新出售商标数据
	 * @author	Jeany
	 * @since	2015-09-16
	 *
	 * @access	public
	 * @param	array	
	 * @p		int
	 * @num		int
	 * @return	array
	 */
	public function getSaleNew($num )
	{
		$r['eq']['status'] = 5;//可出售商标
		
        $r['limit']         = $num;
        $r['order']         = array('date' => 'desc','type' => 'asc');
		
        $data = $this->import('sale')->findAll($r);
        foreach($data['rows'] as $k => $item){
			$data['rows'][$k]['imgurl'] = $this->load('saletrademark')->getImg($item['id']); 
			$data['rows'][$k]['url'] = "/trademark/view/?id=".$item['id']."&c=".$item['class']; 
            $data['rows'][$k]['source'] = isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
			$data['rows'][$k]['classes']  = isset( $this->class[$item['class']] ) ? $this->class[$item['class']] : $item['class'];
			$data['rows'][$k]['guideprice']  = $this->SbPrice($item['guideprice']);
			
        }
		return $data['rows'];
	}
	
	
	/**
     * 得到单条出售信息
     * @author	martin
     * @since	2015-07-27
     *
     * @access	public
     * @param	int		$id  出售信息Id
     * @return	array
     */
	public function getView($id)
	{
		if($id == 0){
			MessageBox::halt('参数错误!');
		}
		$saleType			= C("TYPES");
		$saleInfo			= $this->import("saleview")->get($id);
		if(empty($saleInfo)){
			MessageBox::halt('查找信息失败!');
		}
		$saleInfo['group'] = str_replace("<br>"," ",$saleInfo['group']);
		$saleInfo['tView']	= empty( $saleInfo['types'] )? "" : $saleType[ $saleInfo['types'] ];
		return $saleInfo;
	}

}
?>