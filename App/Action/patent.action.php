<?
header("Content-type: text/html; charset=utf-8"); 
/**
 * 专利列表管理
 *
 * @package	Action
 * @author	Far
 * @since	2016-04-27
 */
class PatentAction extends AppAction
{
//	public $debug = true;
	
	/**
	 * 专利管理列表
	 * 
	 * @author	Xuni
	 * @since	2016-01-8
	 * @access	public
	 * @return	void
	 */
	public function index()
	{
		$this->getSetting();
		//参数
		$params = $this->getFormData();
		$page 	= $this->input('page', 'int', '1');
		$res 	= $this->load('patent')->getList($params, $page, $this->rowNum);

		$total 	= empty($res['total']) ? 0 : $res['total'];
		$list 	= empty($res['rows']) ? array() : $res['rows'];

		$pager 		= $this->pager($total, $this->rowNum);
                $pageBar 	= empty($list) ? '' : getPageBar($pager);

		$result = array();
		//获取所有联系人
		foreach ($list as $k => $v) {
			$result[$k] = $this->load('patent')->getPatentInfo($v['id']);
                        if($result[$k]['type']!=3){
                            $classArr = explode(",", $result[$k]['class']);
                            $_class = array_map('chr', $classArr);
                            $result[$k]['class'] = implode(',', $_class);
                        }
		}
                $this->set("allTotal",$this->load('patent')->countSale());
		$this->set('total', $total);
                $this->set("pageBar",$pageBar);
		$this->set('s', $params);
		$this->set('saleList', $result);
		$this->display();
	}
	
	//添加商品
	public function create()
	{
		$this->display();
	}

	//删除商品
	public function delete()
	{
		$id = $this->input('id', 'string', '');

		$this->set('patentId', $id);
		$this->display();
	}

	//删除商品
	public function deleteSale()
	{
		$id = $this->input('patentId', 'string', '');

		if ( empty($id) ) $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));

		$ids 	= array_filter( array_unique( explode(',', $id) ) );
		if ( empty($ids) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}

		$type 	= $this->input('reason', 'int', 0);//删除原因
		$date 	= $this->input('saleDate', 'string', '');//出售日期
		$memo 	= $this->input('memo', 'string', '');//备注（原因）
		if ( empty($type) || empty($memo) || ($type == 1 && empty($date)) ){
			$this->returnAjax(array('code'=>2,'msg'=>'相关数据不能为空'));
		}

		$res = $this->load('patent')->deleteSale($ids, $type, $memo, $date);
		if ( $res ){
			$this->returnAjax(array('code'=>1,'msg'=>'成功'));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}

	//添加/编辑 联系人
	public function contact()
	{
		$sId = $this->input('patentId', 'int', 0);
		$cId = $this->input('cId', 'int', 0);
		if ( $cId > 0 ){
			$contact = $this->load('patent')->getSaleContact($sId, $cId);
		}else{
			$contact = array();
		}
		$this->set('cId', $cId);
		$this->set('patentId', $sId);
		$this->set('contact', $contact);
		$this->getSetting();
		$this->display();
	}

	//（操作）添加/编辑 联系人
	public function setContact()
	{
		//参数
		$params = $this->getFormData();
		if ( $params['patentId'] <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		if ( $params['source'] <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'请选择来源'));
		}
		if ( $params['name'] == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请输入联系人'));
		}
		if ( $params['phone'] == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请输入联系人电话'));
		}
		if ( $params['price'] < 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'请输入正确的底价'));
		}
		if ( $params['date'] == '' || strtotime($params['date']) === false || strtotime($params['date']) < 0 ) {
			$params['date'] = time();
		}else{
			$params['date'] = strtotime($params['date']);
		}
		$cId = $params['cId'];
		unset($params['cId']);
		if ( $cId <= 0 ){
			$type = 8;
			$res = $this->load('patent')->addContact($params, $params['patentId']);
			$cId = $res;
		}else{
			$type = 9;
			$res = $this->load('patent')->updateContact($params, $cId);
		}
		if ( $res ){
			$this->load('log')->addPatentLog($params['patentId'], $type, "专利联系人ID：$cId 被编辑了");//记录日志
			$this->returnAjax(array('code'=>1,'msg'=>'成功'));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}

	//编辑商品
	public function edit()
	{
		$id 	= $this->input('id', 'int', 0);
		if ( $id <= 0 ){
			MessageBox::halt('参数错误');
		}
		$patent         = $this->load('patent')->getPatentInfo($id);
		$log 		= $this->load('log')->getPatentLog($id);
		$allphone 	= $this->load('phone')->getAllPhone();
		$gjUrl 		= WANGXIANG_URL.'detail.php?id='.$patent['code'];
		$referr 	= $this->getReferrUrl('patent_edit');
                
                //转换ascll
                if($patent['type']!=3){
                    $classArr = explode(",", $patent['class']);
                    $_class = array_map('chr', $classArr);
                    $patent['class'] = implode(',', $_class);
                }
                //获取SEO设置
		$seoInfo = $this->load('seo')->getInfoByType(15,$id);
                $this->set('seoInfo', $seoInfo);
                
		$this->getSetting();
		$this->set('log', $log);
		$this->set('patent', $patent);
		$this->set('gjUrl', $gjUrl);
		$this->set('tmTop', C('ISTOP_LIST'));
		$this->set('allphone', $allphone);
		$this->set('referr', $referr);
		$this->display();
	}

	//商品下架(可批量)
	public function doDown()
	{
		$id 	= $this->input('id', 'string', '');
		$reason = $this->input('reason', 'string', '');
		if ( empty($id) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		if ( empty($reason) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请填写下架原因'));
		}
		$ids 	= array_filter( array_unique( explode(',', $id) ) );
		if ( empty($ids) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		$res 	= $this->load('patent')->patentDown($ids, $reason);
		if ( $res ) $this->returnAjax(array('code'=>1));
		$this->returnAjax(array('code'=>2));
	}

	//商品上架
	public function doUp()
	{
		$id 	= $this->input('id', 'int', 0);
		if ( $id <= 0 ){
			$this->returnAjax(array('code'=>2));
		}
		//TODO 判断联系人是否有一个是审核过的
		$isVerify = $this->load('patent')->existVerifyContact($id);
		if ( !$isVerify ) return $this->returnAjax(array('code'=>2,'msg'=>'无联系人或至少有一位联系人未审核！'));
		$res = $this->load('patent')->saleUp($id);
		if ( $res ) $this->returnAjax(array('code'=>1));
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败，请稍后重试！'));
	}

	//提交价格信息
	public function setPrice()
	{
		$patentId 	= $this->input('patentId', 'int', 0);
		$isSale 	= $this->input('isSale', 'int', 0);
		$isLicense 	= $this->input('isLicense', 'int', 0);
		if ( $patentId <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		}
		$data = array();
		if ( $isSale ){
			$priceType 		= $this->input('priceType', 'int', 2);
			$price 	 		= $this->input('price', 'int', 0);
			$isOffprice 	= $this->input('isOffprice', 'int', 2);
			$salePrice 		= $this->input('salePrice', 'int', 0);
			$priceDate 		= $this->input('priceDate', 'int', 2);
			$salePriceDate 	= $this->input('salePriceDate', 'text', '');
			if ( $priceType == 1 ){//定价
				if ( $price <= 0 ){//未填写销售价格
					$this->returnAjax(array('code'=>2,'msg'=>'请输入销售价格')); 
				}
				if ( $isOffprice == 1 && $salePrice > $price ){
					$this->returnAjax(array('code'=>2,'msg'=>'特价价格不能大于销售价格')); 
				}
				if ( $isOffprice == 1 && $salePrice <=0 ){//是特价但未填写特价价格
					$this->returnAjax(array('code'=>2,'msg'=>'请输入特价价格')); 
				}elseif ( $isOffprice == 1 && $priceDate == 1 && $salePriceDate == '' ){//特价且限时未选择时间
					$this->returnAjax(array('code'=>2,'msg'=>'请输入销售价格')); 
				}elseif ( $isOffprice == 1 ){
					$data['isOffprice'] = 1;
					$data['salePrice'] 	= $salePrice;
					$data['salePriceDate'] 	= ($priceDate == 2) ? 0 : strtotime($salePriceDate);
				}else{
					$data['isOffprice'] = 2;
				}
				$data['price'] 	= $price;
				$data['priceType'] = 1;
			}else{//议价
				$data['priceType'] 	= 2;
				$data['isOffprice'] = 2;
			}
			$data['isSale'] 	= 1;
			$data['isLicense'] 	= $isLicense ? 1 : 2;
		}elseif ( $isLicense ){
			$data['isLicense'] 	= 1;
			$data['isSale'] 	= 2;
		}else{
			$this->returnAjax(array('code'=>2,'msg'=>'至少选择 出售与许可 中一项')); 
		}
		$sale = $this->load('patent')->getPatentInfo($patentId);
		if ( empty($sale) ){
			$this->returnAjax(array('code'=>2,'msg'=>'出售信息不存在')); 
		}else{
			if ( $sale['isTop'] < 3 && $data['isOffprice'] == 1 ){
				$data['isTop'] = 3;//特价自动修改置项值
			}elseif ( $sale['isTop'] == 3 && $data['isOffprice'] != 1 ){
				//非特价还原置项值
				if ( empty($sale['tminfo']['embellish']) ){
					$data['isTop'] = 0;
				}else{
					$data['isTop'] = 2;
				}
			}
		}
		$res = $this->load('patent')->update($data, $patentId);
		if ( $res ){
			$this->load('log')->addPatentLog($patentId, 10, '商品价格已修改', serialize($data));//修改价格信息
			$this->load('usercenter')->pushTmPrice($sale['number'], $sale, $data);//推送到用户
			$this->returnAjax(array('code'=>1,'msg'=>'操作成功'));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}

	//提交备注信息
	public function setMemo()
	{
		$patentId = $this->input('patentId', 'int', 0);
		$memo 	= $this->input('memo', 'text', '');
		if ( $patentId <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		}
		if ( $memo == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请输入备注信息')); 
		}
		$data = array(
			'memo' => $memo
		);
		$res = $this->load('patent')->update($data, $patentId);
		if ( $res ){
			$this->load('log')->addPatentLog($patentId, 11, "修改备注为：$memo", 'test');//修改备注信息
			$this->returnAjax(array('code'=>1,'msg'=>'操作成功'));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}

	//更新包装信息
	public function setEmbellish()
	{
		$patentId = $this->input('patentId', 'int', 0);
		if ( $patentId <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		}
		$isTop 		= $this->input('isTop', 'int', 0);
		$listSort 	= $this->input('listSort', 'int', 0);
		$phone 		= $this->input('viewPhone', 'text', '');

		$sale = array(
			'isTop' 	=> $isTop,
			'listSort' 	=> $listSort,
			'viewPhone'     => $phone,
		);
		$bzpic 	= $this->input('bzpic', 'text', '');
		$alt1 	= $this->input('alt1', 'text', '');
		$value 	= $this->input('value', 'text', '');
		$intro 	= $this->input('intro', 'text', '');
		$tminfo = array(
			'embellish'     => $bzpic,
			'value' 	=> $value,
			'intro' 	=> $intro,
			'alt1' 		=> $alt1,
		);
		if ( $isTop < 2 && !empty($tminfo['embellish']) ){
			$sale['isTop'] = 2;//有包装图片的置项值
		}
		//列表排序处理
		if ( $listSort > 0 && $listSort < 222 ){
			$sale['isTop'] = 0;//有列表排序就不用置顶排序
		}
		$res = $this->load('patent')->setEmbellish($patentId, $sale, $tminfo);
		if ( $res ){
                    //设置SEO的信息
                    $sid = $this->input('sid', 'int', '0');
                    $data['vid']            = $patentId;
                    $data['seotitle']       = $this->input('seo_title', 'string', '');
                    $data['keyword']        = $this->input('seo_keyword', 'string', '');
                    $data['description']    = $this->input('seo_description', 'string', '');
                    $data['isUse']          = $this->input('seo_isUse', 'int', '1');
                    $reArr = $this->load('seo')->viewSetSeo($sid,$data,15);
                    $this->returnAjax($reArr);
		}
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}


    //检查商标是否可出售，可直接生成默认商品
    public function checkNumber()
    {
		$number = $this->input('number', 'text', '');
		$isAdd 	= $this->input('add', 'int', 0);
		if ( empty($number) ) $this->returnAjax(array('code'=>6));
                $info = $this->load('run')->getPatentInfo($number);
		if ( !$info['id'] ) $this->returnAjax(array('code'=>4));//无商标信息
		$patentId = $this->load('patent')->existSale($number);
		if ( $patentId ) $this->returnAjax(array('code'=>2,'id'=>$patentId));//在出售中

		if ( $isAdd ){
			//正常商标马上创建默认的出售信息
			$patentId = $this->load('patent')->addDefault($number,$info);
			if ( $patentId ) $this->returnAjax(array('code'=>1,'id'=>$patentId));
			$this->returnAjax(array('code'=>0));
		}
		$this->returnAjax(array('code'=>-1));
    }

    //删除联系人
    public function delContact()
    {
		$patentId = $this->input('patentId', 'int', 0);
		$id 	= $this->input('id', 'int', 0);
		if ( $patentId <= 0 || $id <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		}
		$r['eq'] = array('id'=>$id);
        $contact = $this->load('patent')->findContact($r);
        if ( empty($contact) ){
        	$this->returnAjax(array('code'=>2,'msg'=>'联系人不存在')); 
        }
		$res = $this->load('patent')->delContact($id, $patentId, 2);
		if ( $res ){
			$this->load('log')->addPatentLog($patentId, 13, "联系人ID：$id 被删除了", serialize($contact));//删除联系人
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'请联系人必须至少保留一个！如商品为上架状态，要保留一个审核过的联系人！'));
    }

    //审核联系人
    public function setVerify()
    {
    	$patentId = $this->input('patentId', 'int', 0);
		$id 	= $this->input('id', 'int', 0);
		if ( $patentId <= 0 || $id <= 0 ){
			$this->returnAjax(array('code'=>2)); 
		}
		$r['eq'] = array('id'=>$id);
        $contact = $this->load('patent')->findContact($r);
        if ( empty($contact) ){
        	$this->returnAjax(array('code'=>2,'msg'=>'联系人不存在')); 
        }
		$res = $this->load('patent')->setVerify($id, $patentId);
		if ( $res ){
			$this->load('log')->addPatentLog($patentId, 14, "联系人ID：$id 审核通过", serialize($contact));//联系人审核通过
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2));
    }

    //驳回联系人
    public function delVerify()
    {
		$id 	= $this->input('id', 'int', 0);
		//$patentId = $this->input('patentId', 'int', 0);
    	$reason = $this->input('reason', 'text', '');
		if ( $id <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		}
		if ( $reason == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请填写原因')); 
		}
		$r['eq'] = array('id'=>$id);
        $contact = $this->load('patent')->findContact($r);
        if ( empty($contact) ){
        	$this->returnAjax(array('code'=>2,'msg'=>'联系人不存在')); 
        }
		$res = $this->load('patent')->delVerify($id, $contact['patentId']);
		if ( $res ){
			$this->load('log')->addPatentLog($contact['patentId'], 15, "联系人ID：$id 被驳回并删除了(原因：$reason)", serialize($contact));//联系人审核通过
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2, 'msg'=>'驳回失败了'));
    }

	protected function getSetting()
	{
		$saleStatus     = C("SALE_STATUS");//出售商标状态
                $saleType 	= C("SALE_TYPE");
		$saleSource     = C("SOURCE");//来源渠道
		$tmLabel 	= C("TM_LABEL");//商标标签
		$tmPrice 	= C("SEARCH_PRICE");//出售搜索底价
                $patentType 	= C("PATENT_TYPE");//专利类别
                $patentClassOne	= C("PATENT_ClASS_ONE");//行业分类
                $patentClassTwo	= C("PATENT_ClASS_TWO");//行业分类
                
		$this->set('tmPrice', $tmPrice);
		$this->set('tmLabel', $tmLabel);
                $this->set('saleType', $saleType);
		$this->set('saleSource', $saleSource);
		$this->set('saleStatus', $saleStatus);
                $this->set('patentType', $patentType);
                $this->set('patentClassOne', $patentClassOne);
                $this->set('patentClassTwo', $patentClassTwo);
	}
	
	
	
	//导出数据提交操作
	public function excel()
	{
		$params = $this->getFormData();
		$list = $this->load('patent')->getExcelList($params);

		$result = array();
		
		//获取所有联系人
		foreach ($list['rows'] as $k => $v) {
			$result[$k] = $this->load('patent')->getPatentInfo($v['id']);
		}
		
		$excelTable = $params['excelTable'];
		$data['filepath'] = $this->load('excel')->downloadExcel($result,$excelTable);
	}		
}
?>