<?
/**
 * 出售接口
 *
 * 获取出售信息，添加出售信息
 * 
 * @package	Module
 * @author	void
 * @since	2015-06-11
 */
class SaleViewModule extends AppModule
{
	public $source = array();
    public $status   = array();
	public $area   = array();
	public $type   = array();
	public $class   = array();
	
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'saleview'			=> 'SaleView',
		'member'		=> 'member',
		'proposer'		=> 'proposer',
        'saletrademark' => 'saletrademark',
    );
	
	/**
     * 分类查询
     * @author	Jeany
     * @since	2015-07-22
     */
	public function getClasses($param)
	{
		$num		= 45 ;
		$classes	= array();
		$output		= array();
		$r			= $this->getWhere($param);
		$r['group'] = array("class" => "asc");
		$r['limit'] = 100;
		$r['col']	= array("count(1) as total", 'class');
		$list		= $this->import('saleview')->find($r);
		
		foreach($list as $item){
			$classes[$item['class']] = "第".$item['class']."类(".$item['total'].")";
		}
		for($i = 1; $i <=45; $i++){
			if( isset($classes[$i])){
				$output[$i] = $classes[$i];
			}else{
				$output[$i] = "<font color='#666666'>第".$i."类(0)</font>";
			}
		}
		return $output;
	}
	
	/**
     * 初始化变量值
     * @author	Jeany
     * @since	2015-07-22
     */
    public function __construct()
    {
        $this->source = C('SOURCE');
        $this->status = C('DEAL_STATUS');
		$this->area   = array( 1=>'国内' ,2=>'国外' );
		$this->type   = C('APPROVE_TYPE');
		$this->approveStatus   = array( 1=>'认证中' ,2=>'认证通过' , 3=> '认证未通过', 4=> '未认证');
		$this->types = C("TYPES"); //商标分类
		$this->prices = C("SEARCH_PRICE"); //底价查询分类
		
		$num = 45 ;
		$class = array();
		for($i=1;$i<=$num;$i++){
			$class[$i] = "第".$i."类";
		}
		$this->class  = $class;
		
    }
	
	
	/**
	 * 获取工作日志分页列表
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array	
	 * @p		int
	 * @num		int
	 * @return	array
	 */
	public function getList($param, $num )
	{
		//获取WHERE条件
		$r = $this->getWhere($param);
		
        $r['limit']         = $num;
        $r['page']          = $param['page'];
        $r['order']         = array('date' => 'desc','id' => 'desc');
		
        $data = $this->import('saleview')->findAll($r);
        foreach($data['rows'] as $k => $item){
            $data['rows'][$k]['source'] = isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
            $data['rows'][$k]['status'] = isset( $this->status[$item['status']] ) ? $this->status[$item['status']] : $item['status'];
			$data['rows'][$k]['type']   = isset( $this->type[$item['type']] ) ? $this->type[$item['type']] : $item['type'];
			$data['rows'][$k]['approveStatus'] = isset( $this->approveStatus[$item['approveStatus']] ) ? $this->approveStatus[$item['approveStatus']] : $item['approveStatus'];
			$data['rows'][$k]['date']   = date('Y-m-d',$item['date']);
			$data['rows'][$k]['class']  = isset( $this->class[$item['class']] ) ? $this->class[$item['class']] : $item['class'];
			$data['rows'][$k]['area']   = isset( $this->area[$item['area']] ) ? $this->area[$item['area']] : $item['status'];
        }
		return $data;
	}
	
	/**
	 * 获取where条件
	 * @author	Jeany
	 * @since	2015-09-11
	 *
	 * @access	public
	 * @param	array	
	 * @return	array
	 */
	public function getWhere($param)
	{
		$r = array();
		foreach($param as $k => $v){
			if(!empty($v) && $k != 'page' && $k != 'date_start' && $k != 'date_end' && $k != 'prices' && $k != 'group' && $k != 'industrycategorySmall'){
				if($k == 'class'){
					$r['in'] = array('class' => explode(',',$param['class']));	
				//}else if($k == 'types'){
				//	$r['in'] = array('types' => explode(',',$param['types']));	
				//}else if($k == 'name' || $k == 'contact'){
				}else if($k == 'name' || $k == 'contact'){
					$r['like'][$k] =  $v;	
				}else{
					$r['eq'][$k] = $v;
				}
			}
		}
		$raw	   = array();
		$raw[]	   = "status not in(4,3)";
		
		if(!empty($param['date_start'])){
			$raw[] = strtotime($param['date_start']." 00:00:00")." <= date";
		}
		if(!empty($param['date_end'])){
			$raw[] = strtotime($param['date_end']." 23:59:59")." > date";
		}
		
		//小分类
		if(!empty($param['industrycategorySmall'])){
			$r['ft']['industrycategorySmall'] = substr($param['industrycategorySmall'],1);
		}
		
		
		//群组
		if(!empty($param['group'])){
			$group = explode(',',$param['group']);
			foreach($group as $val){
				if($val){
					$rawGroup[] = " `group` like '%".$val."%'";
				}
			}	
			$raw[] = "(".implode(" or " , $rawGroup).")";
		}
		
		//底价查询
		// if(!empty($param['prices'])){
			// $pricesArr = $this->prices;
			// $prices = explode(',',$param['prices']);
			// $maxP = 0;
			// $minP = 1000000;
			// $priceOR = '';
			// foreach($prices as $val){
				// if($val){
					// if($val < 8){
			
						// $maxP = $pricesArr[$val][1];
						// if($maxP <= $pricesArr[$val][1]){
							// $maxP = $pricesArr[$val][1];
						// }
						// if($minP > $pricesArr[$val][0]){
							// $minP = $pricesArr[$val][0];
						// }
					// }elseif($val == 8){
						// $priceOR = " or price = ".$pricesArr[$val][0];
					// }
				// }
			// }	
			// $raw[] = "( ".$minP." < price and ".$maxP." > price )".$priceOR;
		// }
		
		if(!empty($param['prices'])){
			$pricesArr = $this->prices;
			$key = $param['prices'];
			if($param['prices'] < 8){
				$raw[] = "( ".$pricesArr[$key][0]." <= price and ".$pricesArr[$key][1]." >= price )";
			}elseif($param['prices'] == 8){
				$raw[] = " price = ".$pricesArr[$key][0];
			}
		}
		moduleParam("sale",'index',$param);
		
		$r['raw']  = implode(" and " , $raw);
		return $r;
	}
	
	/**
	 * 下载的时候查询全部数据
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array	
	 * @p		int
	 * @num		int
	 * @return	array
	 */
	public function getAll($param)
	{
		//获取WHERE条件
		$r = $this->getWhere($param);
		
		$num = $this->import('saleview')->count($r);
        $r['limit']         = $num;
        $r['order']         = array('date' => 'desc','id' => 'desc');
        $data = $this->import('saleview')->findAll($r);
        foreach($data['rows'] as $k => $item){
            $data['rows'][$k]['source'] = isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
            $data['rows'][$k]['status'] = isset( $this->status[$item['status']] ) ? $this->status[$item['status']] : $item['status'];
			$data['rows'][$k]['type']   = isset( $this->type[$item['type']] ) ? $this->type[$item['type']] : $item['type'];
			$data['rows'][$k]['date']   = date('Y-m-d H:i:s',$item['date']);
			$data['rows'][$k]['class']  = isset( $this->class[$item['class']] ) ? $this->class[$item['class']] : $item['class'];
			$data['rows'][$k]['area']   = isset( $this->area[$item['area']] ) ? $this->area[$item['area']] : $item['status'];
        }
		return $data;
	}
	
	
}
?>