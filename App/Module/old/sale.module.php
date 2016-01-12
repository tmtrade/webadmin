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
class SaleModule extends AppModule
{
	public $source = array();
    public $status   = array();
	public $area   = array();
	public $type   = array();
	public $class   = array();
	public $saletype   = array();
	public $platform   = array();
	public $label   = array();
	public $sblength   = array();
	
	
	
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'sale'			=> 'Sale',
		'member'		=> 'member',
		'proposer'		=> 'proposer',
        'saletrademark' => 'saletrademark',
    );
	
	
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
		$this->approveStatus   = C('APPROVE_STATUS');
		$this->types = C("TYPES"); //商标分类
		$this->prices = C("SEARCH_PRICE"); //底价查询分类
		$this->saletype = array(1 => '出售',2 => '许可'); //底价查询分类
		$this->platform = C("PLATFORM_IN"); //入驻平台
		$this->label = C("SBLABEL"); //商标标签
		$this->sblength = C("SBNUMBER"); //商标标签
			
		$num = 45 ;
		$class = array();
		for($i=1;$i<=$num;$i++){
			$class[$i] = "第".$i."类";
		}
		$this->class  = $class;
		
    }
	
	
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
		$r			= $this->getWhere($param,1);
		$r['group'] = array("class" => "asc");
		$r['limit'] = 100;
		$r['col']	= array("count(1) as total", 'class');
		$list		= $this->import('sale')->find($r);
		
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
		
        $data = $this->import('sale')->findAll($r);
        foreach($data['rows'] as $k => $item){
            $data['rows'][$k]['source'] = isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
            $data['rows'][$k]['status'] = isset( $this->status[$item['status']] ) ? $this->status[$item['status']] : $item['status'];
			$data['rows'][$k]['type']   = isset( $this->type[$item['type']] ) ? $this->type[$item['type']] : $item['type'];
			$data['rows'][$k]['approveStatus'] = isset( $this->approveStatus[$item['approveStatus']] ) ? $this->approveStatus[$item['approveStatus']] : $item['approveStatus'];
			$data['rows'][$k]['date']   = date('Y-m-d',$item['date']);
			$data['rows'][$k]['class']  = isset( $this->class[$item['class']] ) ? $this->class[$item['class']] : $item['class'];
			$data['rows'][$k]['area']   = isset( $this->area[$item['area']] ) ? $this->area[$item['area']] : $item['status'];
			$data['rows'][$k]['saletypeValue']   = isset( $this->saletype[$item['saleType']] ) ? $this->saletype[$item['saleType']] : $item['saleType'];
			$data['rows'][$k]['bz']  = $this->load('saletrademark')->getBz($item['id']);
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
	public function getWhere($param,$isclass = 0)
	{
		$r = array();
		$arrayZd = array('page','date_start','date_end','prices','group','sblength','label','platform','types');
		foreach($param as $k => $v){
			if(!empty($v) &&  !in_array($k,$arrayZd)){
				if($k == 'class'){
					if($isclass != 1){	
						$r['in'] = array('class' => explode(',',$param['class']));	
					}
				}else if($k == 'name' || $k == 'contact'){
					$r['like'][$k] =  $v;	
				}else{
					$r['eq'][$k] = $v;
				}
			}
		}
		$raw	   = array();
		$raw[]	   = "status not in(4,3)";
		
		//出售时间查询
		if(!empty($param['date_start'])){
			$raw[] = strtotime($param['date_start']." 00:00:00")." <= date";
		}
		if(!empty($param['date_end'])){
			$raw[] = strtotime($param['date_end']." 23:59:59")." > date";
		}
		
		//字数查询
		if(!empty($param['sblength'])){
			$r['ft']['sblength'] = substr($param['sblength'],0,-1);
		}
		
		//群组
		if(!empty($param['group'])){
			$r['ft']['group'] = $param['group'];
		}
		
		//标签
		if(!empty($param['label'])){
			$label = array_filter(explode(',', $param['label']));
			if ( in_array('7',$label) ){
				$raw[] = " salePrice > 0 ";
				unset($label[array_search('7',$label)]);
			}
			$labels = implode(',', $label);
			if($labels){$r['ft']['label'] = $labels;}
		}
		
		//入驻平台
		if(!empty($param['platform'])){
			$r['ft']['platform'] = substr($param['platform'],0,-1);
		}
		
		//商标分类
		if(!empty($param['types'])){
			$r['ft']['types'] = substr($param['types'],0,-1);
			//$r['ft']['types'] = $param['types'];
		}
		
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
		
		$num = $this->import('sale')->count($r);
        $r['limit']         = $num;
        $r['order']         = array('date' => 'desc','id' => 'desc');
        $data = $this->import('sale')->findAll($r);
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
	
	
	/**
	 * 返回分类数据
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function returnClassData()
	{
		
		$data = array();
		$data['class'] = $this->class;
		$data['source'] = $this->source;
		$data['type'] = $this->type;
		$data['area'] = $this->area;
		$data['status'] = $this->status;
		return $data;
	}
	
	
	/**
	 * 返回分类数据
	 * @author	Jeany
	 * @since	2015-07-27
	 *
	 * @access	public
	 * @return	array
	 */
	public function returnClass()
	{
		
		return $this->class;
	}
	
	
	
	/**
	 * 添加出售信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function addSale($param)
	{

		if($param['type']){
			if($param['type'] == 1 || $param['type'] == 2){
				$param['approveStatus'] = 2 ;
			}
			if($param['type'] == 3){
				$param['approveStatus'] = 4 ;
			}
		}
		$param['date'] = time();
		return $this->import('sale')->add($param);
	}
	
	
	/**
	 * 编辑出售信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function editSale($id,$param)
	{
		if(isset($param['type'])){
			if($param['type'] == 1 || $param['type'] == 2){
				$param['approveStatus'] = 2 ;
			}
			if($param['type'] == 3){
				$param['approveStatus'] = 4 ;
			}
		}
		$r['eq'] = array( "id" => $id );
		return $this->import('sale')->modify($param,$r);
	}
	
	/**
	 * 出售详细信息
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function getDetail($id)
	{	
        $data = $this->import("sale")->get($id);
        if(empty($data)) {return array();}
        $data['sourceValue']    = isset( $this->source[$data['source']] ) ? $this->source[$data['source']] : $data['source'];
        $data['statusValue']    = isset( $this->status[$data['status']] ) ? $this->status[$data['status']] : $data['status'];
		$data['typeValue']      = isset( $this->type[$data['type']] ) ? $this->type[$data['type']] : $data['type'];
		$data['classValue']     = isset( $this->class[$data['class']] ) ? $this->class[$data['class']] : $data['class'];
		$data['areaValue']      = isset( $this->area[$data['area']] ) ? $this->area[$data['area']] : $data['area'];
		$data['labelValue']     = $this->getFHValue($data['label'],$this->label);
		$isTop  = $data['isTop'] == 1 ? "置顶" : "";
		$data['labelValue']     .= $data['labelValue'] ? ",".$isTop : $isTop;
		$data['platformValue']  = $this->getFHValue($data['platform'],$this->platform,1);
		$data['typesValue'] 	= $this->getFHValue($data['types'],$this->types);
		$data['sblengthValue'] 	= $this->getFHValue($data['sblength'],$this->sblength);
		$data['saleTypeValue']     = isset( $this->saletype[$data['saleType']] ) ? $this->saletype[$data['saleType']] : $data['saleType'];
		//array_walk($tmp, array($this,"getSmall"));
		$data['date']      = date('Y-m-d',$data['date']);
        return $data;
	}
	
	
	
	/**
	 * 循环分号链接数据，得到对应值
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function getFHValue($value,$object,$type = 0)
	{	
        $str = '';
		if( !empty($value) ){
			$arr = explode(',',$value);
			foreach($arr as $val){
				if($type == 1){ //入驻平台不一样
					
					$str .= $object[$val]['name'].",";
				}else{
					$str .= $object[$val].",";
				}
			}	
		}
        return $str ? substr($str,0,-1) : "";
	}
	

    /**
     * 获取出售信息详情
     * @author	martin
     * @since	2015-07-27
     *
     * @access	public
     * @param	int		$id  出售信息Id
     * @return	array
     */
    public function getSaleById($id)
    {
        $data = $this->import("sale")->get($id);
        if(empty($data)) {return array();}
        if($data['area'] == 1){
            $item = $this->import("proposer")->get($data['proposerId']);
            $data['proposer'] = empty( $item['name'] ) ? "" : $item['name'];
        }else{
            $r['eq']    = array('saleId' => $id);
            $r['limit'] = 1;
            $item = $this->import("saletrademark")->find($r);
            $data['proposer'] = empty( $item['proposer'] ) ? "" : $item['proposer'];

        }
        return $data;
    }
	
	
	
	/**
     * 获取出售信息详情
     * @author	martin
     * @since	2015-07-27
     *
     * @access	public
     * @param	int		$id  出售信息Id
     * @return	array
     */
    public function getSaleDataByWhere($data)
    {
		
		foreach($data as $k => $v){
			if($k != 'date' && $k != 'memo'){
				$r['eq'][$k] = $v;
			}
			
		}
		$r['limit'] = 1;
		$item = $this->import("sale")->find($r);   
        if($item){
			//数据已经存在
			return 0;
		}else{
			//数据不同
			return 1;
		}
       
    }
	
	/**
     * 修改认证状态
     * @author	martin
     * @since	2015-07-27
     *
     * @access	public
     * @param	int		$id  出售信息Id
     * @return	array
     */
	// public function saleupdata($type , $r)
	// {
		// $data = array(
			// 'type' => 2,//代码是资质认证
			// 'approveStatus' => $type
		// );
		// return $this->import('sale')->update($data , $r);
	// }
	
	
	/**
     * 修改认证状态
     * @author	martin
     * @since	2015-07-27
     *
     * @access	public
     * @param	int		$id  出售信息Id
     * @return	array
     */
	public function saleupdata($type , $r)
	{
		$data = array(
			'type' => 2,//代码是资质认证
			'approveStatus' => $type
		);
		
		/**一标多类的认证 条件**/
		$sale = $this->import('sale')->find($r);
		
		if($sale){
			$ids = array();
			$classes['eq']['number'] =  $sale['number'];
			$classes['eq']['memo']   =  '该商标为一标多类，必须捆绑出售';
			$classes['eq']['phone']  =  $sale['phone'];
			$classes['eq']['date']   =  $sale['date'];
			
			$classes['limit'] = 10000;
			$sales = $this->import('sale')->find($classes);
			if($sales){
				foreach($sales as $item){
					$ids[] = $item['id'];
				}
				 $r['in'] = $ids ? array('id'=>$ids) : "";
			}
		}
		/**一标多类的认证 条件**/
		if(isset($r['in']) && isset($r['eq']['id'])){
			unset($r['eq']['id']);
		}
		
		return $this->import('sale')->update($data , $r);
	}
	
	/**
     * 修改认证状态
     * @author	martin
     * @since	2015-07-27
     *
     * @access	public
     * @param	int		$id  出售信息Id
     * @return	array
     */
	public function getApprove($loginUserId , $proposerId)
	{
		$r['eq'] = array('userId' =>$loginUserId, 'proposerId' => $proposerId);
		
		return $this->import('sale')->getRows($r);
	}
	
	
	/**
	 * 已无效商标分页列表
	 * @author	martin
	 * @since	2015-08-06
	 *
	 * @access	public
	 * @param	array	
	 * @p		int
	 * @num		int
	 * @return	array
	 */
	public function getListInvalid($param, $num )
	{
		if(!empty($param['number'])){
			$r['like']['number'] = $param['number'];
		}
		if(!empty($param['name'])){
			$r['like']['name'] = $param['name'];
		}
        $r['limit']         = $num;
        $r['page']          = $param['page'];
        $r['order']         = array('date' => 'desc');
		$r['raw']			= "status = 4";
        $data = $this->import('sale')->findAll($r);
        foreach($data['rows'] as $k => $item){
            $data['rows'][$k]['source'] = isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
			$data['rows'][$k]['type']   = isset( $this->type[$item['type']] ) ? $this->type[$item['type']] : $item['type'];
			
			$data['rows'][$k]['aStatus'] = isset( $this->approveStatus[$item['approveStatus']] ) ? $this->approveStatus[$item['approveStatus']] : $item['approveStatus'];

			$data['rows'][$k]['date']   = date('Y-m-d',$item['date']);
			$data['rows'][$k]['class']  = isset( $this->class[$item['class']] ) ? $this->class[$item['class']] : $item['class'];
			$data['rows'][$k]['area']   = isset( $this->area[$item['area']] ) ? $this->area[$item['area']] : $item['status'];
			$w['eq']					= array('number'=>$item['number'], 'class'=>$item['class'], 'area'=>$item['area'], 'status'=>3);
			$w['limit']					= 1;
			$w['raw']					= "id!=".$item['id'];
			$other						= $this->import("sale")->find($w);
			$data['rows'][$k]['otherId']= empty($other) ? '' : $other['id'];
        }
		return $data;
	}
	
	/**
	 * 商标基础表auto字段存储到出售表
	 * @author	Jeany
	 * @since	2015-11-04
	 *
	 * @access	public
	 * @param	array	
	 * @return	array
	 */
	public function getAllData($param)
	{	
		$num = $this->import('sale')->count($param);
        $r['limit'] = $num;
        $r['order'] = array('date' => 'desc','id' => 'desc');
		$r['col']   = array('id','number' ,'tid','class','group','name');
        $data = $this->import('sale')->findAll($r);
		return $data['rows'];
	}
	
	
	/* 
	* 群组字符串替换处理
	*/ 
	public function emptyreplace($str) 
	{ 
		$str = str_replace('　', ' ', $str); //替换全角空格为半角 
		$str = str_replace('<br>', ' ', $str); //替换BR
		$str = str_replace('&lt;br&gt;', ' ', $str); //替换BR
		$str = str_replace('*', '', $str);  //替换*
		$str = preg_replace('/\(.*?\)/', ' ', $str);//替换括号里面的
		$result = '';
		$strArr = explode(" ",$str);
		$strArr = array_unique(array_filter($strArr)); //去掉空字符串
		$result = implode(',', $strArr);
		return $result; 
	}
	
	
	
	/**
	 * 获取商标名称类型
	 * 
	 * @author	Jeany
	 * @since	2015-11-5
	 * @access	public
	 * @return	void
	 */
	public function getTrademarkType($trademark)
	{
		$trademark = str_replace(" ","",$trademark);
		$pregtx = "图形";//图形验证正则
		$pregZW = "/^[\x7f-\xff]+$/"; //中文验证正则
		$pregYW = "/^[a-zA-Z]+$/";//英文验证正则
		$pregSZ = "/^[0-9]+$/";//数字
		
		$pregZWBH = "/[\x7f-\xff]/"; //包含中文
		$pregYWBH = "/[a-zA-Z]+/";//包含英文验证正则
		$pregSZBH = "/[0-9]+/";//包含数字
		
		if(preg_match($pregZW,$trademark)  && !strstr($trademark,$pregtx)){	$value = 1;}//中文
		if(preg_match($pregYW,$trademark)){$value = 2;}//英文
		if($pregtx == $trademark){$value = 3;}//图形
		if(preg_match($pregZWBH,$trademark) && preg_match($pregYWBH,$trademark) && !strstr($trademark,$pregtx) && !preg_match($pregSZBH,$trademark)){$value = 4;}//中+英
		if(preg_match($pregZWBH,$trademark)  && strstr($trademark,$pregtx) && strlen($trademark) != 6 && !preg_match($pregSZBH,$trademark)){$value = 5;}//中+图
		$str = str_replace("图形","",$trademark);
		if(preg_match($pregYWBH,$trademark)  && strstr($trademark,$pregtx) && !preg_match($pregZWBH,$str)){$value = 6;}//英+图
		if(preg_match($pregZWBH,$trademark) && preg_match($pregYWBH,$trademark) && strstr($trademark,$pregtx)){$value = 7;}//中+英+图
		if(preg_match($pregSZ,$trademark)){$value = 8;}//数字
		
		return $value;
	}

	/**
	 * 获取商标名称字数
	 * 
	 * @author	Jeany
	 * @since	2015-11-5
	 * @access	public
	 * @return	void
	 */
	public function getTrademarkLength($trademark)
	{
		$trademark = str_replace(" ","",$trademark);
		$strlen = 0;
		$pregZWBH = "/[\x{4e00}-\x{9fa5}]+/u"; //包含中文
		$pregSZBH = "/[0-9]/"; //包含数字
		$pregYWBH = "/[a-zA-Z]/u";//包含英文验证 
		//包含中文的，都按照中文计算
		if((preg_match($pregZWBH,$trademark)  && preg_match($pregYWBH,$trademark)) || (preg_match($pregZWBH,$trademark)  && !preg_match($pregYWBH,$trademark))){
			preg_match_all($pregZWBH, $trademark, $match);
			$str = implode(" ",$match[0]);
			$str = str_replace(" ","" ,$str);
			$strlen = strlen($str)/3;
		}
		
		//不包含中文的，按照英文字母计算
		if(!preg_match($pregZWBH,$trademark)  && preg_match($pregYWBH,$trademark)){
			preg_match_all($pregYWBH, $trademark, $matchYW);
			$strlen = count($matchYW[0]);
		}
		
		//不包含中文的，按照英文字母计算
		if(!preg_match($pregZWBH,$trademark)  && !preg_match($pregYWBH,$trademark) && preg_match($pregSZBH,$trademark)){
			preg_match_all($pregSZBH, $trademark, $matchYW);
			$strlen = count($matchYW[0]);
		}
		$strlen = $strlen > 6 ? 6 : $strlen;
		return $strlen;
	}
	
	/**
	 * 获取入驻平台
	 * 
	 * @author	Jeany
	 * @since	2015-11-5
	 * @access	public
	 * @return	void
	 */
	public function getTrademarkPlatform($class)
	{
		$str = '';
		$platform = C("PLATFORM_IN");
		foreach($platform as $key => $val){
			$arrVal = explode(",",$val['value']);
			if(in_array($class,$arrVal)){
				$str .= $key."," ;  
			}
		}
		return $str ? substr($str,0,-1) : '';
	}
	
	
	/**
	 * 指导价格(出售价格) 出售价格系统自动添加，出售价格=底价+浮动比例(不同底价对应不同的浮动比例)
	 * 
	 * @author	Jeany
	 * @since	2015-09-09
	 * @access	public
	 * @return	int
	 */
	 public function SalePrice($price)
	{	
		$ratio = C("FLAOT_RATIO");
		
		switch($price){
			case ($price <= 10000):
				$key = 1;
				break;
			case ($price > 10000 && $price <= 20000):
				$key = 2;
				break;
			case ($price > 20000 && $price <= 30000):
				$key = 3;
				break;
			case ($price > 30000 && $price <= 50000):
				$key = 4;
				break;
			case ($price > 50000 && $price <= 60000):
				$key = 5;
				break;
			case ($price > 60000 && $price <= 100000):
				$key = 6;
				break;
			case ($price > 100000 && $price <= 200000):
				$key = 7;
				break;
			case ($price > 200000):
				$key = 8;
				break;
		}
		$salePrice = $price*(1+$ratio[$key]);
		return $salePrice;
	}


	/**
	 * 指导价格(出售价格) 出售价格系统自动添加，出售价格=底价+浮动比例(不同底价对应不同的浮动比例)
	 * 
	 * @author	在售商标总数
	 * @since	2015/12/4
	 * @access	public
	 * @return	int
	 */
	public function getSaleTotal()
	{
		//$r['group']	= array('number'=>'desc', 'class'=>'desc');
		$r['col'] 	= array('count(distinct `tid`) as total');
		$r['in']	= array('status'=>array(1,2,5));
		$r['limit'] = 1;
		$count		= $this->import("sale")->find($r);
		$num = empty($count) ? 0 : $count['total'];
		return $num;

	}
}
?>