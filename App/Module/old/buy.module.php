<?php 
class BuyModule extends AppModule
{

    public $source = array();
    public $deal   = array();
	public $buytype= array();

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
		'remind'		=> 'buyRemind',
	);

    /**
     * 初始化变量值
     * @author	martin
     * @since	2015-07-22
     */
    public function __construct()
    {
        $this->source = C("SOURCE");
        $this->deal   = C("BUY_STATUS");
        $this->buytype= C("BUYTYPE");
    }

	/**
	 * 购买列表
	 * @author	martin
	 * @since	2015-07-22
	 *
	 * @access	public
     * @param	int 	$p	    页码
     * @param	int 	$num	条数
     * @param	array	$param	参数
     * @param	int 	$userId	交易员ID
	 * @return	array
	 */
	public function getList( $p, $num, $param,  $userId = 0)
	{
        if($userId != 0) {
            $r['eq']        = array('attacheId' => $userId);
        }
        $r['limit']         = $num;
        $r['page']          = $p;
        $r['order']         = array('date' => 'desc', 'id'=>'desc');
        $this->paramJudge($r, $param);
        $data               = $this->import('buy')->findAll($r);
        foreach($data['rows'] as & $item){
            $item['sourceValue']    = isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
            $item['statusValue']    = isset( $this->deal[$item['status']] ) ? $this->deal[$item['status']] : $item['status'];
            $username               = $this->load("member")->getName($item['attacheId']);
            $item['attache']        = empty($username) ? "<font color='red'>未分配</font>" : $username;
			$crmMember				= $this->load("member")->getMemberById($item['crmMember']);
			$item['crmMember']		= isset($crmMember['name']) ? $crmMember['name'] : "-";
			$item['buyTypeValue']	= isset( $this->buytype[$item['buyType']] ) ? $this->buytype[$item['buyType']] : '-';
        }
		return $data;
	}

    /**
     * 组合搜索条件
     * @author	martin
     * @since	2015-07-24
     *
     * @access	public
     * @param	int 	$r	    输出组合条件
     * @param	array	$param	参数
     * @return	array
     */
    public  function paramJudge(&$r, $param)
    {

        if(!empty( $param['name']) ){
            $r['like']['name'] = $param['name'];
        }
        if(!empty( $param['class']) ){
            $r['eq']['class'] = $param['class'];
        }
        if(!empty( $param['contact']) ){
            $r['like']['contact'] = $param['contact'];
        }
        if(!empty( $param['phone']) ){
            $r['eq']['phone'] = $param['phone'];
        }
        if(!empty( $param['source']) ){
            $r['eq']['source'] = $param['source'];
        }
        if(!empty( $param['status']) ){
            $r['eq']['status'] = $param['status'];
        }
        if(!empty( $param['attache']) ){
            $r['like']['attacheId'] = $param['attache'];
        }
        if(!empty( $param['offerman']) ){
            $r['like']['offerman'] = $param['offerman'];
        }
		
		$raw	   = array();
		if(!empty($param['date_start'])){
			$raw[] = strtotime($param['date_start']." 00:00:00")." <= date";
		}
		if(!empty($param['date_end'])){
			$raw[] = strtotime($param['date_end']." 23:59:59")." > date";
		}
		if(!empty($param['deal_start'])){
			$raw[] = strtotime($param['deal_start']." 00:00:00")." <= dealDate";
		}
		if(!empty($param['deal_end'])){
			$raw[] = strtotime($param['deal_end']." 23:59:59")." > dealDate";
		}
		if(!empty($param['confirm_start'])){
			$raw[] = strtotime($param['confirm_start']." 00:00:00")." <= confirmDate";
		}
		if(!empty($param['confirm_end'])){
			$raw[] = strtotime($param['confirm_end']." 23:59:59")." > confirmDate";
		}
		$r['raw']  = implode(" and " , $raw);

		//保存搜索参数，返回首页时使用
		moduleParam("buy",'index',$param);
    }


    /**
     * 购买列表
     * @author	martin
     * @since	2015-07-22
     *
     * @access	public
     * @param	int 	$id	    求购编号
     * @return	array
     */
    public function findId( $id )
    {
        $data = $this->import("buy")->get($id);
        if( empty($data) ) { return array(); }
        $data['sourceValue']    = isset( $this->source[ $data['source'] ] ) ? $this->source[$data['source']] : $data['source'];
        $data['statusValue']    = isset( $this->deal[$data['status']] ) ? $this->deal[$data['status']] : $data['status'];
        $username               = $this->load("member")->getName($data['attacheId']);
        $data['attache']        = empty($username) ? "未分配" : $username;
        $crmMember				= $this->load("member")->getMemberById($data['crmMember']);
		$data['crmMemberId']	= $data['crmMember'];
		$data['crmMember']		= isset($crmMember['name']) ? $crmMember['name'] : "无";

		$data['buyTypeValue']	= isset( $this->buytype[$data['buyType']] ) ? $this->buytype[$data['buyType']] : '未选择';
		return $data;
    }
	
	
	/**
     * 通过saleId获取数据
     * @author	Jeany
     * @since	2015-07-27
     *
     * @access	public
     * @param	int 	$saleId	    出售编号
     * @return	array
     */
	 
    public function getDataBySaleId( $saleId )
    {
		$r['limit']         = 1;
		$r['eq']['saleId']         = $saleId;
        $data = $this->import("buy")->find($r);
		if($data){
			$data['sourceValue']    = isset( $this->source[ $data['source'] ] ) ? $this->source[$data['source']] : $data['source'];
			$data['statusValue']    = isset( $this->deal[$data['status']] ) ? $this->deal[$data['status']] : $data['status'];
			$username               = $this->load("member")->getName($data['attacheId']);
			$data['attache']        = empty($username) ? "未分配" : $username;
		}
       
        return $data;
    }
	
	/**
     * 通过saleId获取数据
     * @author	Jeany
     * @since	2015-07-27
     *
     * @access	public
     * @param	int 	$saleId	    出售编号
     * @return	array
     */
    public function getDataBySaleList( $saleId )
    {
		$r['eq']['saleId']  = $saleId;
		$r['limit']         = 100000;
		$r['page']          = 1;
		$r['order']         = array('id' => 'desc');
        $data = $this->import("buy")->findAll($r);
		$number = 0;
		if($data){
			foreach($data['rows'] as $k => &$val){
				$val['attache']		=  $this->load("member")->getName($val['attacheId']);
				$val['statusValue'] = isset( $this->deal[$val['status']] ) ? $this->deal[$val['status']] : $val['status'];
			}	
		}
        return $data['rows'];
    }
	
    /**
     * 保存数据
     * @author	martin
     * @since	2015-07-23
     *
     * @access	public
     * @param	int	    $id	    求购编号
     * @param	array	$data	数据
     * @return	void
     */
    public function edit($id,$data)
    {
        $r['eq'] = array( "id" => $id );
        return  $this->import("buy")->modify($data,$r);
    }
    /**
     * 创建数据
     * @author	martin
     * @since	2015-07-23
     *
     * @access	public
     * @param	array	$data	数据
     * @return	void
     */
    public function create($data)
    {
        return $this->import("buy")->create($data);
    }

    /**
     * 导出求购信息
     * @author	martin
     * @since	2015-07-23
     *
     * @access	public
     * @param	array	$data	数据
     * @return	void
     */
    public function excel($param)
    {
        $r['limit'] = 1000000;
        $r['p']     = 1;
		$r['order'] = array('date' => 'desc', 'id'=>'desc');

        $this->paramJudge($r,$param);
        $data       = $this->import("buy")->find( $r );
        $output     = array();
        foreach($data as $k=> $item){
            $output[ $k ]['source']         = $this->source[ $item['source'] ];
			if(!empty($item['oid'])){
				$output[ $k ]['source']		.= "(拆)";
			}
			$output[ $k ]['buyTypeValue']	= isset( $this->buytype[$item['buyType']] ) ? $this->buytype[$item['buyType']] : '-';
            $output[ $k ]['date']           = date('Y-m-d H:i:s', $item['date']);
            $output[ $k ]['name']           = $item['name'];
			if(empty($item['class'])){
				$output[ $k ]['class']      = "";
			}else{
				$output[ $k ]['class']      = "第".$item['class']."类";
			}
            $output[ $k ]['need']           = $item['need'];
            $output[ $k ]['hopePrice']      = $item['hopePrice'];
            $output[ $k ]['contact']        = $item['contact'];
            $output[ $k ]['phone']          = $item['phone'];
            $output[ $k ]['status']         = $this->deal[ $item['status'] ];
            $output[ $k ]['dealDate']       = empty($item['dealDate']) ? '' : date('Y-m-d H:i:s', $item['dealDate']);
            $output[ $k ]['confirmDate']    = empty($item['confirmDate']) ? '' : date('Y-m-d H:i:s', $item['confirmDate']);
            $output[ $k ]['attacheId']      = $this->load("member")->getName($item['attacheId']);
			
			$crmMember				= $this->load("member")->getMemberById($item['crmMember']);
			$output[ $k ]['crmMember']		= isset($crmMember['name']) ? $crmMember['name'] : "";

            $output[ $k ]['offerman']       = $item['offerman'];
            $output[ $k ]['department']     = $item['department'];

        }

        $header   = array('来源渠道' , '求购类型' , '求购时间', '商标名称', '商标类别', '求购需求', '期望价格', '联系人', '联系电话', '销售状态', '成交时间', '立案时间', '交易专员','第三顾问', '顾问', '顾问部门');

        $fileName = '求购商标列表'.date('Y-m-d', time()).'.xls';
        Excel::expcsv_html($header, $output , $fileName);
    }

    /**
     * 导出跟踪记录
     * @author	martin
     * @since	2015-07-29
     *
     * @access	public
     * @param	array	$data	数据
     * @return	void
     */
    public function trackexcel($param, $user)
    {
        $r['limit'] = 1000000;
        $r['p']     = 1;
		
        if ($user['roleId'] == 2){
            $r['eq']        = array('attacheId' => $user['id']);
        }else{
            $r['raw']       = "attacheId!=0";
		}
        $r['order']         = array('date' => 'desc', 'id'=>'desc');
        $this->paramJudgeTrack($r,$param);
        $data       = $this->import("buy")->find( $r );
        $output     = array();
        foreach($data as $k=> $item){
            $output[ $k ]['source']         = $this->source[ $item['source'] ];
			if(!empty($item['oid'])){
				$output[ $k ]['source']		.= "(拆)";
			}
			
			$output[ $k ]['buyTypeValue']	= isset( $this->buytype[$item['buyType']] ) ? $this->buytype[$item['buyType']] : '-';
            $output[ $k ]['date']           = date('Y-m-d H:i:s', $item['date']);
            $output[ $k ]['name']           = $item['name'];
            $output[ $k ]['class']          = $item['class'];
            $output[ $k ]['need']           = $item['need'];
            $output[ $k ]['hopePrice']      = $item['hopePrice'];
            $output[ $k ]['contact']        = $item['contact'];
            $output[ $k ]['phone']          = $item['phone'];
            $output[ $k ]['dealName']		= $item['dealName'];
            $output[ $k ]['dealNumber']     = $item['dealNumber'];
            $output[ $k ]['dealClass']      = empty($item['dealClass']) ? "" : "第" . $item['dealClass'] . "类";
            $output[ $k ]['status']         = $this->deal[ $item['status'] ];
            $output[ $k ]['memo']			= $item['memo'];
            $output[ $k ]['attacheId']      = $this->load("member")->getName($item['attacheId']);
			
			$crmMember				= $this->load("member")->getMemberById($item['crmMember']);
			$output[ $k ]['crmMember']		= isset($crmMember['name']) ? $crmMember['name'] : "";

            $output[ $k ]['offerman']       = $item['offerman'];
            $output[ $k ]['department']     = $item['department'];
        }

        $header   = array('来源渠道' ,  '求购类型' ,'求购时间', '商标名称', '商标类别', '求购需求', '期望价格', '联系人', '联系电话','成交商标', '成交商标号', '成交类别', '销售状态','备注','交易专员','第三顾问', '顾问', '顾问部门');

        $fileName = '跟踪信息列表'.date('Y-m-d', time()).'.xls';
        Excel::expcsv_html($header, $output , $fileName);
    }


    /**
     * 跟踪列表
     * @author	martin
     * @since	2015-07-28
     *
     * @access	public
     * @param	int 	$p	    页码
     * @param	int 	$num	条数
     * @param	array	$param	参数
     * @param	int 	$userId	交易员ID
     * @return	array
     */
    public function getListTrack( $p, $num, $param,$user)
    {
        if ($user['roleId'] == 2){
            $r['eq']        = array('attacheId' => $user['id']);
        }else{
            $r['raw']       = "attacheId!=0";
		}
        $r['limit']         = $num;
        $r['page']          = $p;
        $r['order']         = array('remindTime'=>'desc', 'date' => 'desc', 'id'=>'desc');
		$param['page']		= $p;
        $this->paramJudgeTrack($r, $param);
        $data               = $this->import('buy')->findAll($r);
        foreach($data['rows'] as & $item){
            $item['sourceValue']    = isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
            $item['statusValue']    = isset( $this->deal[$item['status']] ) ? $this->deal[$item['status']] : $item['status'];
            $username               = $this->load("member")->getName($item['attacheId']);
            $item['attache']        = empty($username) ? "<font color='red'>未分配</font>" : $username;
			
			$crmMember				= $this->load("member")->getMemberById($item['crmMember']);
			$item['crmMember']		= isset($crmMember['name']) ? $crmMember['name'] : "-";
			$item['buyTypeValue']	= isset( $this->buytype[$item['buyType']] ) ? $this->buytype[$item['buyType']] : '-';
			$item['remind']			= $this->load("remind")->getRemindRed($item['id']);
        }
        return $data;
    }

    /**
     * 组合搜索条件
     * @author	martin
     * @since	2015-07-24
     *
     * @access	public
     * @param	int 	$r	    输出组合条件
     * @param	array	$param	参数
     * @return	array
     */
    public  function paramJudgeTrack(&$r, $param)
    {
        if(!empty( $param['name']) ){
            $r['like']['name'] = $param['name'];
        }
        if(!empty( $param['offerman']) ){
            $r['like']['offerman'] = $param['offerman'];
        }
        if(!empty( $param['contact']) ){
            $r['like']['contact'] = $param['contact'];
        }
        if(!empty( $param['status']) ){
            $r['eq']['status'] = $param['status'];
        }
        if(!empty( $param['dealName']) ){
            $r['like']['dealName'] = $param['dealName'];
        }
        if(!empty( $param['phone']) ){
            $r['like']['phone'] = $param['phone'];
        }

		moduleParam("buy",'track',$param);
    }



    /**
     * 保存交易详情
     * @author	martin
     * @since	2015-07-28
     *
     * @access	public
     * @param	array 	$data	    页面获取的数据
     * @param	array	$user		用户信息
     * @return	array
     */
	public function editSaleInfo($data, $user)
	{
		$item = $this->load("buy")->findId($data['id']);
		$sale = $this->load("sale")->getSaleById($data['saleId']);
		if(empty($item)){
			echo "{type:2, mess:'该求购信息不存在！'}";exit;
		}elseif($item['status'] == 3){
			echo "{type:2, mess:'【已立案】，不能进行操作！'}";exit;
		}elseif($item['status'] == 4){
			echo "{type:2, mess:'【交易关闭】，不能进行操作！'}";exit;
		}elseif( empty($sale) && $data['status'] != 4 && $data['status'] != 5){
			echo "{type:2, mess:'该出售信息不存在！'}";exit;
		}elseif($item['attacheId'] != $user['id']){
			echo "{type:2, mess:'该求购未分配给您，不能进行操作！'}";exit;
		}elseif($data['saleId'] != $item['saleId'] && ($sale['status'] == 2 || $sale['status'] ==3)){
			echo "{type:2, mess:'该出售信息已成交，不能进行操作！'}";exit;
		}
		if( $data['status'] == 1 ){//洽谈中
			$bool = $this->editSaleStatus1($data, $user, $sale, $item);
		}elseif ( $data['status'] == 2 ){//已成交
			$bool = $this->editSaleStatus2($data, $user, $sale, $item);
		}elseif ( $data['status'] == 3 ){//已立案
			$bool = $this->editSaleStatus3($data, $user, $sale, $item);
		}elseif ( $data['status'] == 4 ){//交易关闭
			$data['saleId'] = 0;
			$bool = $this->editSaleStatus4($data, $user, $sale, $item);
		}elseif ( $data['status'] == 5 ){//无
			$data['saleId'] = 0;
			$bool = $this->editSaleStatus5($data, $user, $sale, $item);
		}
		$this->editOldSale($data, $item , $user);

		if($bool){
			$this->editUserStatus($data, $item);
			$this->changeSaleNumber($data);
			$this->editStatusTrack($data, $user, $item);
			echo "{type:1, mess:'保存成功！'}";exit;
		}else{
			echo "{type:2, mess:'保存失败！'}";exit;
		}

	}
	
    /**
     * 修改求购信息状态为“洽谈中”
     * @author	martin
     * @since	2015-07-29
     *
     * @access	public
     * @param	array 	$data	    页面提交信息
     * @param	array	$user		登录用户信息
     * @param	array	$sale		出售信息
     * @param	array	$item		求购信息
     * @return	void
     */
	public function editSaleStatus1($data, $user, $sale, $item)
	{
		$count = $this->getSaleStatus($data['saleId'], $data['id'],array(3,4));
		if($count > 0 ){
			$statusValue	= $this->deal[ $sale['status'] ];
			echo "{type:2, mess:'该出售信息状态为【".$statusValue."】，修改失败！'}"; exit;
		}
		$modify		= array(
					'saleId'		=> $data['saleId'],
					'status'		=> $data['status'],
					'memo'			=> $data['memo'],
					'dealNumber'	=> $data['number'],
					'dealName'		=> $data['name'],
					'dealClass'		=> $data['class'],
					'dealDate'		=> 0,
					'confirmDate'	=> 0,
					'confirmDate'	=> 0,

					);
		$r['eq']	= array('id' => $data['id']);
		$bool		= $this->import("buy")->modify($modify,$r);
		$messUser	= $this->getSaleBuy($data['saleId'],$item['id'], array(2) );
		if(!$messUser){
			$r_sa['eq']	= array('id' => $data['saleId']);
			$this->import("sale")->modify(array('status' => $data['status']),$r_sa);
		}

		$deal		= array(
						'attacheId'		=> $user['id'],
						'buyId'			=> $data['id'],
						'number'		=> $sale['number'],
						'class'			=> $sale['class'],
						'name'			=> $sale['name'],
						'status'		=> 1,
						'salePrice'		=> 0,
						'agencyfee'		=> 0,
						'assignor'		=> '',
						'transferee'	=> '',
						'aCounselor'	=> '',
						'tCounselor'	=> '',
						'dealDate'		=> 0,
						'confirmDate'	=> 0,
						'memo'			=> $data['memo'],
					);
		$bool = $this->saveSaleInfo($deal,$data['id']);
		return $bool;
	}
	
    /**
     * 修改求购信息状态为“已成交”
     * @author	martin
     * @since	2015-07-29
     *
     * @access	public
     * @param	array 	$data	    页面提交信息
     * @param	array	$user		登录用户信息
     * @param	array	$sale		出售信息
     * @param	array	$item		求购信息
     * @return	void
     */
	public function editSaleStatus2($data, $user, $sale, $item)
	{
		$count = $this->getSaleStatus($data['saleId'], $data['id']);
		if($count > 0 ){
			$statusValue	= $this->deal[ $sale['status'] ];
			echo "{type:2, mess:'该出售信息状态为【".$statusValue."】，修改失败！'}"; exit;
		} 
		$item['firstDealDate'] = $item['firstDealDate'] == 0 ? time() : $item['firstDealDate'];
		if($data['dealDate'] < $item['firstDealDate']){
			echo "{type:2, mess:'成交时间应该大于第一次处理时间【".date('Y-m-d',$item['firstDealDate'])."】！'}"; exit;
		}
		$modify		= array(
					'saleId'		=> $data['saleId'],
					'status'		=> $data['status'],
					'memo'			=> $data['memo'],
					'dealNumber'	=> $data['number'],
					'dealName'		=> $data['name'],
					'dealClass'		=> $data['class'],
					'dealDate'		=> $data['dealDate'],
					'confirmDate'	=> 0,
					);
		$r['eq']	= array('id' => $data['id']);
		$bool		= $this->import("buy")->modify($modify,$r);
		$r_sa['eq']	= array('id' => $data['saleId']);
		$this->import("sale")->modify(array('status' => $data['status']),$r_sa);
		
		$deal		= array(
						'attacheId'		=> $user['id'],
						'buyId'			=> $data['id'],
						'number'		=> $sale['number'],
						'class'			=> $sale['class'],
						'name'			=> $sale['name'],
						'status'		=> 1,
						'salePrice'		=> $data['salePrice'],
						'agencyfee'		=> $data['agencyfee'],
						'assignor'		=> $data['assignor'],
						'transferee'	=> $data['transferee'],
						'aCounselor'	=> $data['aCounselor'],
						'tCounselor'	=> $data['tCounselor'],
						'dealDate'		=> $data['dealDate'],
						'confirmDate'	=> 0,
						'memo'			=> $data['memo'],
					);
		$bool = $this->saveSaleInfo($deal,$data['id']);
		if($data['status']!= $item['status'] || $data['number'] != $item['dealNumber'] && $data['class'] != $item['dealClass']){
			$messtpl	= "商标：【%s】（商标号：%s），已成交";
			$this->sendNote($messtpl, $sale, $data, $user['id']);
		}
		return $bool;
	}

    /**
     * 修改求购信息状态为“已立案”
     * @author	martin
     * @since	2015-07-29
     *
     * @access	public
     * @param	array 	$data	    页面提交信息
     * @param	array	$user		登录用户信息
     * @param	array	$sale		出售信息
     * @param	array	$item		求购信息
     * @return	void
     */
	public function editSaleStatus3($data, $user, $sale, $item)
	{
		$count = $this->getSaleStatus($data['saleId'], $data['id']);
		if($count > 0 ){
			$statusValue	= $this->deal[ $sale['status'] ];
			echo "{type:2, mess:'该出售信息状态为【".$statusValue."】，修改失败！'}"; exit;
		}
		if($data['confirmDate'] < $item['dealDate']){
			echo "{type:2, mess:'立案时间应大于等于成交时间！'}"; exit;
		}

		$modify				= array(
							'saleId'		=> $data['saleId'],
							'dealNumber'	=> $data['number'],
							'dealName'		=> $data['name'],
							'dealClass'		=> $data['class'],
							'status'		=> $data['status'],
							'memo'			=> $data['memo'],
							'confirmDate'	=> $data['confirmDate'],
							);
		$r['eq']			= array('id' => $data['id']);
		$bool				= $this->import("buy")->modify($modify,$r);

		$r_sa['eq']			= array('id' => $data['saleId']);
		$this->import("sale")->modify(array('status' => $data['status']),$r_sa);

		$r_de['eq']			= array('buyId'=>$data['id'], 'status'=>1);
		$r_de['limit']		= 1;
		$buydeal			= array(
							'number'		=> $sale['number'],
							'class'			=> $sale['class'],
							'name'			=> $sale['name'],
							'salePrice'		=> $data['salePrice'],
							'agencyfee'		=> $data['agencyfee'],
							'assignor'		=> $data['assignor'],
							'transferee'	=> $data['transferee'],
							'aCounselor'	=> $data['aCounselor'],
							'tCounselor'	=> $data['tCounselor'],
							'dealDate'		=> $data['dealDate'],
							'confirmDate'	=> $data['confirmDate'],
							'memo'			=> $data['memo'],
						);
		$this->import("buydeal")->modify($buydeal, $r_de);
		$deal				= $this->import("buydeal")->find($r_de);
		$t['eq']			= array("saleId" => $sale['id']);
		$t['limit']			= 1;
		$trademark			= $this->import("saletrademark")->find( $t );
		

		$history	= array(
					'buyId'			=> $data['id'],
					'dealId'		=> $deal['id'],
					'saleId'		=> $sale['id'],
					'trademark'		=> $trademark['id'],
					'source'		=> $sale['source'],
					'type'			=> $sale['type'],
					'contact'		=> $sale['contact'],
					'phone'			=> $sale['phone'],
					'number'		=> $sale['number'],
					'class'			=> $sale['class'],
					'name'			=> $sale['name'],
					'group'			=> $trademark['group'],
					'goods'			=> $trademark['goods'],
					'validEnd'		=> $trademark['validEnd'],
					'attacheId'		=> $item['attacheId'],
					'price'			=> $sale['price'],
					'agencyfee'		=> $deal['agencyfee'],
					'salePrice'		=> $deal['salePrice'],
					'saleDate'		=> $sale['date'],
					'dealDate'		=> $item['dealDate'],
					'confirmDate'	=> $data['confirmDate'],
					'offerman'		=> $item['offerman'],
					'department'	=> $item['department'],
					'status'		=> $data['status'],
					);
		$bool = $this->import("history")->create( $history );
		
		$messtpl	= "商标：【%s】（商标号：%s），已立案";
		$this->sendNote($messtpl, $sale, $data, $user['id']);
		$this->editOtherSale($data, $data['id']);

		return $bool;
	}

	
    /**
     * 修改求购信息状态为“交易关闭”
     * @author	martin
     * @since	2015-07-29
     *
     * @access	public
     * @param	array 	$data	    页面提交信息
     * @param	array	$user		登录用户信息
     * @param	array	$sale		出售信息
     * @param	array	$item		求购信息
     * @return	void
     */
	public function editSaleStatus4($data, $user, $sale, $item)
	{	
		$modify		= array(
					'saleId'		=> 0,
					'status'		=> $data['status'],
					'memo'			=> $data['memo'],
					'dealNumber'	=> '',
					'dealName'		=> '',
					'dealClass'		=> 0,
					'dealDate'		=> 0,
					'confirmDate'	=> 0,
					'userReasonDate'=> time(),
					);
		$r['eq']	= array('id' => $data['id']);
		$r['limit']	= 1;
		$item		= $this->import("buy")->find($r);
		$bool		= $this->import("buy")->modify($modify,$r);
			
		$r_de['eq']	= array('buyId' => $data['id']);
		$this->import("buydeal")->modify(array('status' => 2), $r_de);

		$this->load("remind")->closeRemind($item['id']);

		return $bool;
		
	}	
    /**
     * 修改求购信息状态为“无”
     * @author	martin
     * @since	2015-07-29
     *
     * @access	public
     * @param	array 	$data	    页面提交信息
     * @param	array	$user		登录用户信息
     * @param	array	$sale		出售信息
     * @param	array	$item		求购信息
     * @return	void
     */
	public function editSaleStatus5($data)
	{	
		$modify		= array(
					'saleId'		=> 0,
					'status'		=> $data['status'],
					'memo'			=> $data['memo'],
					'dealNumber'	=> '',
					'dealName'		=> '',
					'dealClass'		=> 0,
					'dealDate'		=> 0,
					'confirmDate'	=> 0,
					);
		$r['eq']	= array('id' => $data['id']);
		$r['limit']	= 1;
		$item		= $this->import("buy")->find($r);
		$bool		= $this->import("buy")->modify($modify,$r);
			
		$r_de['eq']	= array('buyId' => $data['id']);
		$this->import("buydeal")->modify(array('status' => 2), $r_de);

		return $bool;
		
	}
	/**
     * 保存交易详情
     * @author	martin
     * @since	2015-08-04
     *
     * @access	public
     * @param	array 	$data	    页面提交信息
     * @param	array	$item		求购商标列表
     * @param	array	$user		用户信息
     * @return	void
     */	
	public function saveSaleInfo($deal,$buyId)
	{
		$c['eq']		= array('buyId' => $buyId,'status' => 1);
		$count			= $this->import("buydeal")->count($c);
		if(empty($count)){
			$bool		= $this->import("buydeal")->create($deal);
		}else{
			$d['eq']	= array('buyId' => $buyId, 'status' => 1);
			$bool		= $this->import("buydeal")->modify($deal, $d);
		}
		return $bool;
	}
	/**
     * 出售商标ID发生变化时
     * @author	martin
     * @since	2015-07-30
     *
     * @access	public
     * @param	array 	$data	    页面提交信息
     * @param	array	$item		求购商标列表
     * @param	array	$user		用户信息
     * @return	void
     */	
	public function editOldSale($data, $item , $user)
	{
		if ($data['saleId'] !=  $item['saleId']){
			$messUser	= $this->getSaleBuy($item['saleId'],$item['id'], array(2) );
			$messUser2	= $this->getSaleBuy($item['saleId'],$item['id'], array(1) );
			if($messUser){
				$status = 2;
			}elseif($messUser2){
				$status = 1;
			}else{
				$status = 5;
			}
			$r['eq']	= array('id'=>$item['saleId']);
			$this->import("sale")->modify(array('status'=>$status), $r);
		}
	}
    /**
     * 保存跟踪记录
	 * 记录第一次处理求购信息的时间
     * @author	martin
     * @since	2015-07-29
     *
     * @access	public
     * @param	array 	$data	    页面提交信息
     * @param	array	$user		用户信息
     * @return	void
     */
	public function editStatusTrack($data, $user, $item)
	{
		$dealDate		= time();
		if($item['firstDealDate'] == 0){
			$b['eq']	= array( 'id' => $item['id']);
			$this->import("buy")->modify(array("firstDealDate"=>$dealDate),$b);
		}

		$track = array(
			'attacheId' => $user['id'],
			'buyId'		=> $data['id'],
			'saleId'	=> $data['saleId'],
			'status'	=> $data['status'],
			'info'		=> $data['info'],
			'memo'		=> $data['memo'],
			);
		$r['eq']		= array('buyId' => $data['id']);
		$r['order']		= array('id' =>"desc");
		$r['limit']		= 1;
		$r['col']		= array("saleId","attacheId", "buyId","status", "info", "memo");
		$old			= $this->import("track")->find($r);
		if($old){
			$diff			= array_diff($track, $old);
			if(!empty($diff) || $old['status'] != $data['status']){
				$track['date']	= $dealDate;
				$this->import("track")->create($track);
			}
		}else{
			$track['date']	= $dealDate;
			$this->import("track")->create($track);
		}
	}

	/**
     * 保存出售商标ID变更的次数
     * @author	martin
     * @since	2015-09-11
     *
     * @access	public
     * @param	array 	$data	    页面提交信息
     * @return	void
     */
	public function changeSaleNumber($data)
	{
		$change			= true;
		if($data['saleId'] == 0) {
			$change		= false;
		}else {
			$r['eq']	= array('buyId' => $data['id']);
			$r['order']	= array('id' =>"desc");
			$r['limit']	= 1;
			$old		= $this->import("track")->find($r);
			if($old && $old['saleId'] == $data['saleId']){
				$change = false;
			}
		}
		if($change){
			$r['eq']		= array( 'id' => $data['id']);
			$this->import("buy")->modify(array('changeSale' => array('changeSale',1)),$r);
		}
	}

    /**
     * 获取关联同一条求购商标的交易员
     * @author	martin
     * @since	2015-07-28
     *
     * @access	public
     * @param	int 	$saleId	    出售信息ID
     * @param	int		$userId		用户ID
     * @return	array
     */
	public function getDiffUser($saleId,$userId)
	{
		$r['eq']	= array('saleId' => $saleId);
		$r['raw']	= "attacheId != ".$userId;
		$r['limit'] = 100;
		$data		= $this->import("buy")->find($r);
		$output		= array();
		if( !empty($data) ){
			foreach($data as $item){
				$output[] = $item['attacheId'];
			}
		}
		return $output;
	}

    /**
     * 获取关联同一条出售商标的交易员
     * @author	martin
     * @since	2015-07-28
     *
     * @access	public
     * @param	int 	$saleId	    出售信息ID
     * @param	int		$userId		用户ID
     * @param	array	$status		状态
     * @return	int
     */
	public function getSaleStatus($saleId, $buyId, $status = array(2,3,4))
	{
		$r['id']		= array("id" => $saleId);
		$r['limit']		= 1;
		$r['in']		= array('status' => $status);
		$count			= $this->import("sale")->count($r);
		if($count > 0){
			$w['eq']	= array("saleId"=>$saleId);
			$w['limit'] = 100;
			$w['in']	= array('status' => $status);
			$w['raw']	= "id!=".$buyId ;

			$count		= $this->import("buy")->count($w);
		}
		return $count;

	}

	
    /**
     * 修改用户的状态的字段
     * @author	martin
     * @since	2015/11/24
     *
     * @access	public
     * @param	int 	$saleId	    出售信息ID
     * @param	int		$userId		用户ID
     * @param	array	$status		状态
     * @return	int
     */
	public function editUserStatus($data, $item)
	{
		$buystatus	= C("BUYERSTATUS");
		$userStatus	= 1;
		foreach($buystatus as $k=>$row){
			if( in_array($data['status'], $row['status'])){
				$userStatus = $k;
				break;
			}
		}
		if($userStatus != 4 || $userStatus == 4 && in_array($item['status'],array(2,3)) ){
			$save = array('userStatus' => $userStatus);
			$r['eq'] = array('id'=>$item['id']);
			$this->import('buy')->modify($save, $r);
		}
	
	}


	/**
     * 获取修改之前的出售信息关联的求购信息
     * @author	martin
     * @since	2015-08-05
     *
     * @access	public
     * @param	int 	$saleId	    出售信息ID
     * @param	int		$userId		用户ID
     * @param	array	$status		状态
     * @return	int
     */
	public function getSaleBuy($saleId, $buyId, $status = array(2,3,4))
	{
		$w['eq']	= array("saleId"=>$saleId);
		$w['limit'] = 100;
		$w['in']	= array('status' => $status);
		$w['raw']	= "id!=".$buyId ;
		$count		= $this->import("buy")->count($w);
		return $count;
	}


    /**
     * 判断求购信息是否存在相同
     * @author	martin
     * @since	2015-08-04
     *
     * @access	public
     * @param	array 	$data	   求购信息
     * @return	int
     */
	public function isExist($data){
		$r = array();
		unset($data['attacheId']);
		foreach($data as $k => $item){
			$r['eq'][$k] = $item;
		}
		$r['limit'] = 1;
		return $this->import('buy')->find($r);
	}


	
    /**
     * 给其他关注该出售信息的交易员发送消息
     * @author	martin
     * @since	2015-08-04
     *
     * @access	public
     * @param	string	$messtpl	发送消息模版
     * @param	array 	$data	    页面提交信息
     * @param	array	$user		登录用户信息
     * @param	array	$sale		出售信息
     * @param	array	$item		求购信息
     * @return	void
     */
	public function sendNote($messtpl,$sale, $data, $userId)
	{
		$str		= sprintf($messtpl, $sale['name'], $sale['number']);
		$r['eq']	= array("isUse"=>1);
		$r['raw']	= "id != ".$userId;
		$r['limit']	= 10000;
		$messUser	= $this->import("member")->find($r);
		foreach($messUser as $m){
			//发送站内信
			$note = array(
				'content'   => $str,
				'sendMan'   => $userId,
				'acceptMan' => $m['id'],
			);
			$this->load("note")->setNote($note);
		}
	}

    /**
     * 求购信息立案时，关闭相同商标的出售信息和重置相关求购信息
     * @author	martin
     * @since	2015-08-04
     *
     * @access	public
     * @param	string	$saleId		出售ID
     * @param	array 	$buyId	    求购ID
     * @return	void
     */
	public function editOtherSale($data, $buyId)
	{
		//重置关联的求购信息
		$buy['eq']		= array('saleId'=>$data['saleId']);
		$buy['raw']		= "id != ".$buyId;
		$buy['limit']	= 1000;
		$buyInfo		= $this->import('buy')->find($buy);
		foreach($buyInfo as $item){
			$this->resetBuy($item['id']);
		}
        $oldSale		= $this->import("sale")->get($data['saleId']);

		//关闭出售信息
		$r['eq'] = array(
			'number'	=> $data['number'],
			'class'		=> $data['class'],
			'area'		=> $oldSale['area'],
			);
		$r['raw']		= "id!=".$data['saleId'] ." and status!=3";
		$r['limit']		= 10000;
		$saleInfo		= $this->import('sale')->find($r);
		foreach($saleInfo as $vo){
			
			$z['eq']	= array('saleId'	=> $vo['id']);
			$z['raw']	= "id != ".$buyId;
			$z['limit']	= 1000;
			$buyList	= $this->import('buy')->find($z);
			foreach($buyList as $one){
				$this->resetBuy($one['id']);
			}
			$edit['eq'] = array('id'=>$vo['id']); 
			$memo = array(
				'status'=> 4,
				'memo'	=> '同一商标已出售，出售编号为：'.$data['saleId'],
				);
			$this->import('sale')->modify($memo, $edit);
		}
	}
	
    /**
     * 关闭求购信息
     * @author	martin
     * @since	2015-08-04
     *
     * @access	public
     * @param	array 	$buyId	    求购ID
     * @return	void
     */
	public function resetBuy($buyId)
	{
		$r['eq']  = array('id'=>$buyId);
		$r['raw'] = 'status != 3';
		$update   = array(
				'saleId'		=> 0,
				'status'		=> 5,
				'dealName'		=> '',
				'dealNumber'	=> '',
				'dealClass'		=> 0,
				'dealDate'		=> 0,
				'confirmDate'	=> 0,
				'memo'			=> '',
		);
		$this->import('buy')->modify($update,$r);
		$r['eq']  = array('buyId'=>$buyId);
		$this->import('buydeal')->modify(array('status' => 2),$r);
	}

	

	/**
	 * 求购信息的报表列表
	 * @author	martin
	 * @since	2015-09-11
	 *
	 * @access	public
     * @param	int 	$p	    页码
     * @param	int 	$num	条数
     * @param	array	$param	参数
     * @param	int 	$userId	交易员ID
	 * @return	array
	 */
	public function getReportBuyList( $p, $num, $param)
	{
        $r['limit']         = $num;
        $r['page']          = $p;
        $r['order']         = array('date' => 'desc', 'id'=>'desc');
        $this->paramReportBuy($r, $param);
        $data               = $this->import('buy')->findAll($r);
        foreach($data['rows'] as & $item){
            $item['sourceValue']    = isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
            $item['statusValue']    = isset( $this->deal[$item['status']] ) ? $this->deal[$item['status']] : $item['status'];
            $username               = $this->load("member")->getName($item['attacheId']);
            $item['attache']        = empty($username) ? "<font color='red'>未分配</font>" : $username;
			$t['eq']				= array('buyId' => $item['id'] );
			$item['trackcount']		= $this->import("track")->count($t);
			$t['limit']				= 1;
			$t['order']				= array('id' => 'asc');
			$trackinfo				= $this->import("track")->find($t);
			$item['trackdate']		= empty($trackinfo) ? '0' : $trackinfo['date'];
			
			$z['eq']				= array('buyId' => $item['id'] );
			$z['limit']				= 1;
			$z['order']				= array('id' => 'desc');
			$trackinfo2				= $this->import("track")->find($z);
			$item['trackinfo']		= empty($trackinfo2) ? '' : $trackinfo2['info'];

        }
		return $data;
	}
	
	/**
	 * 求购信息的报表列表
	 * @author	martin
	 * @since	2015-09-11
	 *
	 * @access	public
     * @param	int 	$p	    页码
     * @param	int 	$num	条数
     * @param	array	$param	参数
     * @param	int 	$userId	交易员ID
	 * @return	array
	 */
	 public function getReportBuyExcel($param){
        $r['limit'] = 1000000;
        $r['p']     = 1;
        $r['order'] = array('date' => 'desc', 'id'=>'desc');
        $this->paramJudge($r,$param);
        $this->paramReportBuy($r, $param);
        $data       = $this->import("buy")->find( $r );
        $output     = array();
        foreach($data as $k=> $item){
            $output[ $k ]['sourceValue']    = isset( $this->source[$item['source']] ) ? $this->source[$item['source']] : $item['source'];
			
			if (!empty($item['oid'])){ 
				$output[ $k ]['sourceValue'].= "(拆)";
			}

            $output[ $k ]['phone']          = $item['phone'];
            $output[ $k ]['date']           = date('Y-m-d H:i', $item['date']);

			$t['eq']						= array('buyId' => $item['id'] );
			$item['trackcount']				= $this->import("track")->count($t);

			$t['limit']						= 1;
			$t['order']						= array('id' => 'asc');
			$trackinfo						= $this->import("track")->find($t);
			$output[ $k ]['trackdate']		= empty($trackinfo) ? '' : date('Y-m-d H:i', $trackinfo['date']);
			$output[ $k ]['trackcount']		= $item['trackcount'];
			$output[ $k ]['changeSale']		= $item['changeSale'];
            $output[ $k ]['statusValue']	= isset( $this->deal[$item['status']] ) ? $this->deal[$item['status']] : $item['status'];

			$z['eq']						= array('buyId' => $item['id'] );
			$z['limit']						= 1;
			$z['order']						= array('id' => 'desc');
			$trackinfo2						= $this->import("track")->find($z);
			$output[ $k ]['trackinfo']		= empty($trackinfo2) ? '' : $trackinfo2['info'];

            $output[ $k ]['dealDate']       = empty($item['dealDate']) ? '' : date('Y-m-d', $item['dealDate']);
            $output[ $k ]['confirmDate']    = empty($item['confirmDate']) ? '' : date('Y-m-d', $item['confirmDate']);
            $output[ $k ]['attacheId']      = $this->load("member")->getName($item['attacheId']);
			$output[ $k ]['offerman']		= $item['offerman'];
            $output[ $k ]['department']     = $item['department'];

        }

        $header   = array('来源渠道', '联系电话', '求购时间', '首次处理时间', '跟客次数', '配单数', '状态', '原因', '成交时间', '立案时间', '交易专员', '顾问', '顾问部门');

        $fileName = '求购信息数据统计'.date('Y-m-d', time()).'.xls';
        Excel::expcsv_html($header, $output , $fileName);
	 }




    /**
	 * 求购信息的报表列表组合搜索条件
	 * @author	martin
	 * @since	2015-09-11
	 *
     *
     * @access	public
     * @param	int 	$r	    输出组合条件
     * @param	array	$param	参数
     * @return	array
     */
    public  function paramReportBuy(&$r, $param)
    {
        if(!empty( $param['status']) ){
            $r['eq']['status'] = $param['status'];
        }
        if(!empty( $param['attache']) ){
            $r['like']['attacheId'] = $param['attache'];
        }
        if(!empty( $param['offerman']) ){
            $r['like']['offerman'] = $param['offerman'];
        }
        if(!empty( $param['department']) ){
            $r['like']['department'] = $param['department'];
        }
        if(!empty( $param['source']) ){
            $r['eq']['source'] = $param['source'];
        }
		
		$raw	   = array();
		if(!empty($param['date_start'])){
			$raw[] = strtotime($param['date_start']." 00:00:00")." <= date";
		}
		if(!empty($param['date_end'])){
			$raw[] = strtotime($param['date_end']." 23:59:59")." > date";
		}

		$r['raw']  = implode(" and " , $raw);
    }

	
	
	/**
	 * 求购信息的报表列表
	 * 为避免出错，获取 成交时间 > 第一次处理时间 > 分配时间
	 * @author	martin
	 *	平均处理时间=处理时间-分配时间
	 *	处理件数=跟踪管理总条数-无-交易关闭
	 *	平均配单次数=配单总数/处理件数
	 *	成交时间=成交时间-处理时间
	 *	成交单数=填写了成交时间的单数
	 *
	 * @access	public
	 * @return	array
	 */
	public  function getReportTrackList($search)
	{
        $r['limit'] = 1000000;
        $r['p']     = 1;
        $r['order'] = array('date' => 'desc', 'id'=>'desc');
        $r['group'] = array('attacheId' => 'asc');
        $r['in']    = array('status'=>array(1,2,3,4,5));
        $r['raw']   = "attacheId != 0";

        $q['raw']   = 'firstDealDate > allotDate and allotDate!=0';
        $q['col']	= array('attacheId', 'sum(firstDealDate-allotDate) as firstDate','sum(changeSale) as changeSale','count(1) as num', );
		if($search['date_start'] != ""){
			$q['raw']   .= ' and date>='.strtotime($search['date_start']);
		}
		if($search['date_end'] != ""){
			$q['raw']   .= ' and date < '.strtotime($search['date_end']." 23:59:59");
		}
        $data       = $this->import("buy")->findAll( $r );
		foreach($data['rows'] as $k=>$item){
			$q['eq']			= array('attacheId' => $item['attacheId']);
			$q['in']			= array('status'=>array(1,2,3,5));
			$q['limit']			= 1;
			$rows				= $this->import("buy")->find( $q );

            $output[$k]['username']	= $this->load("member")->getName($item['attacheId']);
			$output[$k]['dealTime']	= ceil( $rows['firstDate'] / $rows['num'] / 60);
			$output[$k]['saleNum']	= $rows['num']; //ceil( $item['changeSale'] / $item['num']);
			$output[$k]['changeSale']	=  number_format($rows['changeSale'] / $rows['num'],1);

			$d['eq']			= array('attacheId' => $item['attacheId']);
			$d['in']			= array('status' => array(2,3));
			$d['group']			= array('attacheId' => 'asc');
			$d['raw']			= 'dealDate > firstDealDate';
			$d['col']			= array('count(1) as total', 'sum( dealDate-firstDealDate ) as dealDate');
			$d['limit']			= 1;
			$deal				= $this->import("buy")->find($d);
			if(!empty($deal['total'])){
				$saleTime				= $deal['dealDate'] / $deal['total'] / 86400;
				$output[$k]['saleTime']	= number_format($saleTime,1);
				$output[$k]['success']	= (int) $deal['total'];
			}else{
				$output[$k]['saleTime']	= 0;
				$output[$k]['success']		= 0;
			}
			
			$z['eq']						= array('attacheId' => $item['attacheId'], 'status' =>4 );
			$count							= $this->import("buy")->count($z);
			$output[$k]['close']			= $count;

		}
		return $output;
	}

	
	/**
	 * 求购信息的报表导出
	 * 为避免出错，获取 成交时间 > 第一次处理时间 > 分配时间
	 * @author	martin
	 * @since	2015-09-15
	 *
	 * @access	public
	 */
	public  function getReportTrackExcel($search)
	{
        $r['limit'] = 1000000;
        $r['p']     = 1;
        $r['order'] = array('date' => 'desc', 'id'=>'desc');
        $r['group'] = array('attacheId' => 'asc');
        $r['in']    = array('status'=>array(1,2,3,4,5));
        $r['raw']   = "attacheId != 0";

        $q['raw']   = 'firstDealDate > allotDate and allotDate!=0';
        $q['col']	= array('attacheId', 'sum(firstDealDate-allotDate) as firstDate','sum(changeSale) as changeSale','count(1) as num', );
		
		if($search['date_start'] != ""){
			$q['raw']   .= ' and date>='.strtotime($search['date_start']);
		}
		if($search['date_end'] != ""){
			$q['raw']   .= ' and date < '.strtotime($search['date_end']." 23:59:59");
		}

        $data       = $this->import("buy")->findAll( $r );
		$output		= array();
		foreach($data['rows'] as $k => $item){
			$q['eq']			= array('attacheId' => $item['attacheId']);
			$q['in']			= array('status'=>array(1,2,3,5));
			$q['limit']			= 1;
			$rows				= $this->import("buy")->find( $q );

            $output[ $k ]['username']	= $this->load("member")->getName($item['attacheId']);
			$output[ $k ]['dealTime']	= ceil( $rows['firstDate'] / $rows['num'] / 60);

			$output[ $k ]['saleNum']	= $rows['num'];
			$output[ $k ]['changeSale']	= number_format($rows['changeSale'] / $rows['num'],1);

			$d['eq']					= array('attacheId' => $item['attacheId']);
			$d['in']					= array('status' => array(2,3));
			$d['group']					= array('attacheId' => 'asc');
			$d['raw']					= 'dealDate > firstDealDate';
			$d['col']					= array('count(1) as total', 'sum( dealDate-firstDealDate ) as dealDate');
			$d['limit']					= 1;
			$deal						= $this->import("buy")->find($d);
			if(!empty($deal['total'])){
				$saleTime				= $deal['dealDate'] / $deal['total'] / 86400;
				$output[$k]['saleTime']	= number_format($saleTime,1);
				$output[$k]['success']	= (int) $deal['total'];
			}else{
				$output[$k]['saleTime']	= 0;
				$output[$k]['success']	= 0;
			}
			
			$z['eq']					= array('attacheId' => $item['attacheId'], 'status' =>4 );
			$count						= $this->import("buy")->count($z);
			$output[$k]['close']		= $count;
		}

        $header   = array('交易专员', '平均处理时间(分)', '处理件数', '平均配单次数', '平均成交时间（天）', '成交单数', '交易关闭件数');

        $fileName = '跟踪信息数据统计'.date('Y-m-d', time()).'.xls';
        Excel::expcsv_html($header, $output , $fileName);
		return $data;
	}

	/**
	 * 管理员关闭求购信息
	 * @author	martin
	 * @since	2015/11/5
	 *
	 * @access	public
	 * @return	bool
     * @param	array	$param	参数
	 */
	public function closeInfo($param){
		
		if(empty($param['id'])){
			echo json_encode("参数错误！");die;
		}
		if(empty($param['memo'])){
			echo json_encode("请填写关闭原因！");die;
		}
		if(mb_strlen($param['memo'],'utf8') > 200){
			echo json_encode("关闭原因字数太长，请精简！");die;
		}
		$info	= $this->import("buy")->get($param['id']);
		if($info['status']==4){
			echo json_encode("求购信息已关闭，不要非法操作！");die;
		}elseif($info['status']==3){
			echo json_encode("求购信息已立案，不要非法操作！");die;
		}
		if(empty($info)){
			echo json_encode("请不要非法提交！");die;
		}
		$r['eq'] = array("id"=>$param['id']);
		$data	 = array(
				'status'			=> 4,
				'reason'			=> $param['memo'],
				'attacheId'			=> 0,
				'userReasonDate'	=> time(),
		);
		$bool    = $this->import("buy")->modify($data, $r);
		return $bool;
	}

	/**
	 * 管理员关闭求购信息
	 * @author	martin
	 * @since	2015/11/5
	 *
	 * @access	public
	 * @return	bool
     * @param	array	$param	参数
	 */
	public function changeCrmMember($item, $memberId, $message){
		if($item['crmMemberId'] != 0){
			echo json_encode("已经转移，不能重复操作！");die;
		}
		if( empty($memberId) ){
			echo json_encode("请选择第三顾问！");die;
		}
		$member = $this->load("member")->getMemberById($memberId);
		$subject= array();
		if(!empty($item['name'])){
			$subject[] = $item['name'];
		}
		if(!empty($item['class'])){
			$subject[] = "第".$item['class']."类";
		}
		if(!empty($item['hopePrice'])){
			$subject[] = "期望价格".$item['hopePrice'];
		}
		$remarks = array();

		if(!empty($item['need'])){
			$remarks[] = '求购需求：'. $item['need'];
		}
		if(!empty($item['offerman'])){
			$remarks[] = "顾问：".$item['offerman'];
		}
		if(!empty($item['offerman'])){
			$remarks[] = "顾问部门：".$item['department'];
		}
		if(!empty($item['attacheId'])){
			$remarks[] = '交易组：'. $this->load("member")->getName($item['attacheId']);
		}
		if(!empty($message)){
			$remarks[] = '专员附言：'. $message;
		}	
		//来源站点（0：后台查标[.COM] 2：在线咨询[.COM]  4：400电话咨询）
		$post['source'] 	= 0;
		$post['username'] 	= $member['username'];//顾问id
		$post['company'] 	= '';//公司名称
		$post['pttype'] 	= 1; //类型（1：求购 2：出售）
		$post['subject'] 	= implode("；", $subject);//注册名称
		$post['remarks']	= implode("；", $remarks);//备注
		$post['name'] 		= $item['contact'];//联系人
		$post['address'] 	= '';//客户联系地址
		$post['postcode'] 	= '';//客户邮编
		$post['tel'] 		= $item['phone'];//电话
		$post['email'] 		= '';//
		$post['area'] 		= '';//
		$post['sid'] 		= '';//
		$post['crmInfoId'] 	= $item['crmInfoId'];

		$json = $this->importBi('CrmPassport')->insertCrmMember($post);//联系人id
		$output				= json_decode($json,1);
		if( !isset($output['code']) || $output['code'] != 1){
			echo json_encode("接口调用失败！");die;
		}
		$crmId				= (array) $output['data'];

		$data				= array(
							'crmMember' => $memberId,
							'crmInfoId' => $crmId['id'],
		);

		$r['eq']			= array("id"=> $item['id']);
		$bool				= $this->import("buy")->modify($data,$r);
		if($bool){
			echo json_encode(1); die;
		}else{
			echo json_encode("保存失败");die;
		}
	}


	/**
	 * 返回电话的存在求购条数
	 * @author	martin
	 * @since	2015/12/9
	 *
	 * @access	public
	 * @return	bool
     * @param	array	$phone	电话号码
	 */
	public function checkphoneCount($phone)
	{
		$r['eq'] = array('phone'=> trim($phone) );
		return $this->import('buy')->count($r);
	}

}
?>
