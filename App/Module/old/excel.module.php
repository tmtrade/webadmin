<?php 
class ExcelModule extends AppModule
{

    public $source = array();
    public $deal   = array();

    /**
	* 引用业务模型
	*/
	public $models = array(
		'buy'			=> 'buy',
		'sale'			=> 'sale',
        'member'		=> 'member',
		'track'			=> 'track',
		'buydeal'		=> 'buydeal',
		'history'		=> 'history',
		'saletrademark' => 'saletrademark',
		'excel'			=> 'excel',
	);

    /**
     * 初始化变量值
     * @author	martin
     * @since	2015-07-22
     */
    public function __construct()
    {
        $this->source = C("SOURCE");
        $this->deal   = C("DEAL_STATUS");
    }
	
    /**
     * 检查数据
     * @author	martin
     * @since	2015/12/3
     */
	public function dealinfo($r, $page){
		$r['limit'] = 2000;
		$r['page']	= $page;
		$r['eq']	= array('isUser' => 0);
		$output		= $this->import('excel')->findAll($r);
		ob_end_clean();

		foreach($output['rows'] as $k => $item){
			$item['number']		= trim($item['number']);
			$item['class']		= trim($item['class']);
			$item['contract']	= trim($item['contract']);
			$item['phone']		= trim($item['phone']);
			$item['price']		= (float) trim($item['price']);
			$item['offerman']	= trim($item['offerman']);
			$item['department'] = trim($item['department']);
			$item['memo']		= trim($item['memo']);
			if( strstr($item['price'],'-' ) ){
				$price = explode('-',$item['price']);
				if( strstr($item['price'],'W' ) ){
					$item['price'] = $price[0] *10000;
				}else{
					$item['price'] = $price[0];
				}
			}elseif( strstr($item['price'],'.' ) ){
				$price = str_replace('W','',$item['price']);
				if( strstr($item['price'],'万' ) ){
					$item['price'] = $price * 10000;
				}elseif( strstr($item['price'],'W' ) ){
					$item['price'] = $price * 10000;
				}elseif($item['price'] < 1000){
					$item['price'] = $price * 10000;
				}else{
					$item['price'] = $price;
				}
			}elseif( strstr($item['price'],'W' ) ){
				$item['price'] = $item['price'] *10000;
			}elseif( strstr($item['price'],'万' ) ){
				$item['price'] = $item['price'] * 10000;
			}elseif($item['price'] < 1000){
				$item['price'] = $item['price'] * 10000;
			}
			//数据库已经存在数据
			$diff['eq'] = array('number' =>$item['number'],'class'=>$item['class'],'phone'=>$item['phone']);
			$diffnum    = $this->import('sale')->count($diff);
			if($diffnum > 0 ){
				$item['isUser'] = 1;
			}

			if(empty($item['isUser'])){
				//查询无效数据
				$trademark = $this->load('trademark')->details($item['number'],$item['class']);
				if(empty($trademark)){
					$item['isUser'] = 2;
				}
			}
			if(empty($item['isUser'])){
				//重复数据
				$excel['eq'] = array('number'=>$item['number'],'class'=>$item['class'],'phone'=>$item['phone'],'isUser' => 0);
				$count = $this->import('excel')->count($excel);
				if($count > 1){
					$item['isUser'] = 3;
				}
			}
			$modify['eq'] = array("id"=>$item['id']);
			unset($item['id']);
			$this->import("excel")->modify($item, $modify);
		}
		echo "ok";
	}

	public function insertall($page){
		$r['limit'] = 2000;
		$r['page']	= $page;
		$r['eq']	= array('isUser' => 0);
		$r['order']	= array('id' => 'asc');
		$output		= $this->import('excel')->findAll($r);
		ob_end_clean(); 
		$I = 0;
		$shuchu = array();
		$Y = 0;
		foreach($output['rows'] as $k => &$item){
			$item['number']		= trim($item['number']);
			$item['addtime']	= time();
			//$item['vstart']		= trim($item['vstart']);
			$item['contract']	= trim($item['contract']);
			$item['phone']		= trim($item['phone']);
			$item['price']		= (float)($item['price']);
			$item['status']		= 5;
			//$item['memo']		= trim($item['memo']);
			
			/*$trademark = $this->load('trademark')->details($item['number'],$item['class']);

			if(empty($trademark)){
				echo "编号：".$item['id']."，商标号".$item['number']."，类型".$item['class']."，<font color='red'>数据为空</font><br/>";
				ob_flush();
				flush();  
			}*/
			/*
			$q['eq'] = array("number"=>$item['number'] , 'class'=>$item['class']);
			$q['limit'] = 1;
			$count = $this->import("sale")->find($q);
			*/
			/*
			if(empty($count)){
				$I ++;
				echo "编号：".$item['id']."，商标号".$item['number']."，类型".$item['class']."，<font color='red'>数据为空</font><br/>";
				ob_flush();
				flush();  
			}else{
				$modify=array('offerman'=>trim($item['offerman']),'department'=>trim($item['department']));
				$this->import("sale")->modify($modify,$q);
				$Y ++;
			}*/

			/*$q['eq'] = array('number'=> $item['number'],'class'=>trim($item['class']));
			$q['p']  = 1;
			$q['limit'] = 10;
			$count = $this->import('excel')->findAll($q);
			
			if($count['total'] != 2){
				//echo $count['total'] . "|".$item['id']."|".$item['number']."|".$item['class']."<br/>";
				ob_flush();
				flush();
				if(!isset($shuchu[ $item['number'] ])){
					$shuchu[$item['number']] = array(
												'total'		=> $count['total'],
												'number'	=> $item['number'],
												'class'		=> $item['class']
												);
				}
				
			}*/

		
			
			//$item['class']		= str_replace('类','',$item['class']);
			//$item['addtime']	= strtotime(str_replace('.','-',$item['addtime']));

			/*if($item['vstart']!=''){
				$vstart				= explode('-',$item['vstart']);
				$vstart[0]			= str_replace('.','-',$vstart[0]);
				$vstart[1]			= str_replace('.','-',$vstart[1]);
				$item['validEnd']	= date('Y-m-d', strtotime($vstart[1]));
			}*/

			/**处理价格*
			if( strstr($item['price'],'-' ) ){
				$price = explode('-',$item['price']);
				if( strstr($item['price'],'W' ) ){
					$item['price'] = $price[0] *10000;
				}else{
					$item['price'] = $price[0];
				}
			}elseif( strstr($item['price'],'.' ) ){
				$price = str_replace('W','',$item['price']);
				if( strstr($item['price'],'万' ) ){
					$item['price'] = $price * 10000;
				}elseif( strstr($item['price'],'W' ) ){
					$item['price'] = $price * 10000;
				}else{
					$item['price'] = $price;
				}

			}elseif( strstr($item['price'],'W' ) ){
				$item['price'] = $item['price'] *10000;
			}elseif( strstr($item['price'],'万' ) ){
				$item['price'] = $item['price'] * 10000;
			}

			**/
			$trademark = $this->load('trademark')->details($item['number'],$item['class']);
			if($trademark == false) continue ;
			/*
			if(empty($trademark)){
				echo "编号：".$item['id']."，商标号".$item['number']."，类型".$item['class']."，<font color='red'>数据为空</font><br/>";
				ob_flush();
				flush();  
			} else {
				$item['name'] = $trademark['trademark'];
			}
			*/
			$diff['eq'] = array('number' =>$item['number'], 'class'=>$item['class'], 'phone'=>$item['phone']);
			$diffnum    = $this->import('sale')->count($diff);
			if($diffnum > 0 ){
				echo "编号：".$item['id']."，商标号".$item['number']."，类型".$item['class']."，<font color='red'>出售信息已经存在</font><br/>";
				ob_flush();
				flush(); 
				$excel['eq'] = array('id'=>$item['id']);
				$this->import("excel")->modify(array('isUser' => 4),$excel);
				continue;
			}

			/*
			$excel['eq'] = array('number' =>$item['number'],'class'=>$item['class']);
			$excelnum     = $this->import('excel')->count($excel);
			if($excelnum > 1 ){
				echo "编号：".$item['id']."，商标号".$item['number']."，类型".$item['class']."，<font color='red'>excel里面已经存在</font><br/>";
				ob_flush();
				flush();  
			}*/



			/*elseif($trademark['trademark'] != $item['name']){
				echo "编号：".$item['id']."，商标名称错误：【".$item['name']."】【".$trademark['trademark']."】<br/>";
				ob_flush();
				flush();
			}*/
			//if($item['addtime'] ==0){ echo $item['id'];exit;}
			/*
			
			if(!empty($trademark) && $item['validStart'] != $trademark['valid_start']){
				echo "编号：".$item['id']."，商标号".$item['number']."，类型".$item['class']."，开始时间不正确【".$item['validStart']."】【".$trademark['valid_start']."】<br/>";
				ob_flush();
				flush();
			}
			if(!empty($trademark) && $item['validEnd'] != $trademark['valid_end']){
				echo "编号：".$item['id']."，商标号".$item['number']."，类型".$item['class']."，结束时间不正确【".$item['validEnd']."】【".$trademark['valid_end']."】<br/>";
				ob_flush();
				flush();
			}*/
			$sale = array(
					'userId'		=> 0,
					'tid'			=> $trademark['auto'],
					'number'		=> $item['number'],
					'class'			=> $item['class'],
					'name'			=> $trademark['trademark'],
					'proposerId'	=> $trademark['proposer_id'],
					'newId'			=> $trademark['newId'],
					'contact'		=> $item['contract'],
					'phone'			=> $item['phone'],
					'price'			=> $item['price'],
					'status'		=> 5,
					'source'		=> 9,
					'type'			=> 3,
					'approveStatus'	=> 4,
					'date'			=> time(),
					'area'			=> 1,
					'offerman'		=> $item['offerman'],
					'department'	=> $item['department'],
					'memo'			=> $item['memo'],
					'hits'			=> 0,
					'types'			=> $this->load('sale')->getTrademarkType($trademark['trademark']),
					'guideprice'	=> 0,
					'group'			=> $this->load('sale')->emptyreplace($trademark['group']),
					'priceType'		=> 2,
					'sblength'		=> $this->load('sale')->getTrademarkLength($trademark['trademark']),
					'platform'		=> $this->load('sale')->getTrademarkPlatform($trademark['class']),
			);
			$bool  = $saleId = $this->import('sale')->create($sale);
			$trade = array(
					'saleId'		=> $saleId,
					'number'		=> $item['number'],
					'class'			=> $item['class'],
					'name'			=> $trademark['trademark'],
					'imgurl'		=> $trademark['imgUrl'],
					'goods'			=> $trademark['goods'],
					'proposer'		=> $trademark['proposerName'],
					'status'		=> $trademark['newstatus'],
					'validEnd'		=> $trademark['valid_end'],
					'area'			=> 1,
					'intro'			=> '',
					'introTwo'		=> '',
			);
			$bool2 = $this->import('saletrademark')->create($trade);

			
			$excel['eq'] = array('id'=>$item['id']);
			$this->import("excel")->modify(array('isUser' => 5),$excel);
			if($bool==true && $bool2==true){
				$I++;
			}
		}
		//echo "<pre>";
		//print_r($shuchu);
		echo "插入成功条数：".$I.";失败：".$Y;
		//print_r($output);
	}
}
?>