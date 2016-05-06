<?
/**
* 国内商标
*
* 国内商标商品创建，修改，删除等
*
* @package	Module
* @author	Xuni
* @since	2015-12-30
*/
class InternalModule extends AppModule
{
    public $models = array(
        'sale'		        => 'sale',
        'contact'           => 'saleContact',
        'tminfo'            => 'saleTminfo',
        'history'           => 'saleHistory',
        'userSaleHistory'   => 'userSaleHistory',
    );
    
    public function getList($params, $page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['col']   = array('id');
        if ( empty($params) ){
            $res = $this->import('sale')->findAll($r);
            return $res;
        }
        $r['raw'] = ' 1 ';
        if ( !empty($params['tmNums']) ){
            $r['ft']['length'] = $params['tmNums'];
        }
        if ( !empty($params['tmType']) ){
            $r['ft']['type'] = $params['tmType'];
        }
        if ( !empty($params['tmClass']) ){
            $classArr = explode(",", $params['tmClass']);
            $classCount = count($classArr);
            if($params['jname']==1 && $classCount>1){
                //开始提取多标多类and关系的商标名称
                foreach ($classArr as $k=>$v){
                    $w['raw'] = " `class`={$v} AND (name in (SELECT name from t_sale 
                            where `class` in ({$params['tmClass']})  and `name` != '图形' and `name` != '' group by `name` 
                            HAVING count(id) >= {$classCount}));";
                    $w['col']  = array('name');//商标号
                    $w['limit'] = 10000;
                    $classList[$k] = $this->import('sale')->find($w);
                    if($k==0){
                        $mstr = "MATCH(`class`) AGAINST ('{$v}')";
                    }else{
                        $mstr .= " or MATCH(`class`) AGAINST ('{$v}')";
                    }
                    
                }
                $result_array = $classList[0];
                foreach($classList as $arr2){
                    $result_array = call_user_func_array('array_intersect',array($result_array,$arr2));
                }
                foreach ($result_array as $v){
                    $nameList[] = '"'.$v['name'].'"';
                }
                $nameStr = implode(",", $nameList);
                $r['raw'] .= " AND name in ({$nameStr}) and MATCH class AGAINST ('{$params['tmClass']}') and ({$mstr})";
                
            }else{
                 $r['ft']['class'] = $params['tmClass'];
            }
        }
        
        if ( !empty($params['tmLabel']) ){
            $r['ft']['label'] = $params['tmLabel'];
        }
        if ( !empty($params['salePlat']) ){
            $r['ft']['platform'] = $params['salePlat'];
        }
        if ( !empty($params['tmName']) ){
            $r['like']['name'] = $params['tmName'];
        }
        if ( !empty($params['tmNumber']) ){
            $r['eq']['number'] = $params['tmNumber'];
        }
        if ( !empty($params['saleStatus']) ){
            $r['eq']['status'] = $params['saleStatus'];
        }
        if ( !empty($params['dateStart']) ){
            $r['raw'] .= " AND date >= ".strtotime($params['dateStart']);
        }
        if ( !empty($params['dateEnd']) ){
            $r['raw'] .= " AND date <= ".(strtotime($params['dateEnd'])+24*3600);
        }

        //出售类型（出售、许可）
        if ( $params['saleType'] == 3 ){
            $r['eq']['isSale']      = 1;
            $r['eq']['isLicense']   = 1;
        }elseif ( $params['saleType'] == 2 ){
            $r['eq']['isLicense']   = 1;
        }elseif ( $params['saleType'] == 1 ){
            $r['eq']['isSale']      = 1;
        }
        if ( !empty($params['tmGroup']) ){
            
            //if($params['jname']==1){
            //     $r['ft']['group'] = explode(",", $params['tmGroup']);
            //}else{
                 $r['ft']['group'] = $params['tmGroup'];
            //}
        }
        if ( !empty($params['offprice']) ){
            $r['eq']['priceType']   = 1;
            $r['eq']['isOffprice']  = 1;
            $r['raw'] .= " AND (salePriceDate = 0 OR salePriceDate >= unix_timestamp(now())) ";
        }
        if ( !empty($params['isTop']) ){
            $r['raw'] .= " AND `isTop` > 0 ";
        }
        if ( $params['isVerify'] == 2 ){
            $list = $this->getNoVerifySale();
            $list = empty($list) ? array(0) : $list;
            $r['in'] = array('id'=>$list);
        }
        if ( !empty($params['regDate']) && strtotime($params['regDate']) > 0 ){//处理子表来源
            $startDate  = strtotime($params['regDate']);       
            $r['raw']  .= " AND (regDate <= $startDate )";
        }

        $_child =  '';
        if ( !empty($params['saleSource']) ){//处理子表来源
            $source = $params['saleSource'];
            $_child .= " AND `source` = $source ";
        }
        if ( !empty($params['startPrice']) || !empty($params['endPrice']) ){//处理子表底价
            $_start = ($params['startPrice'] > $params['endPrice']) ? $params['endPrice'] : $params['startPrice'];
            $_end   = $params['startPrice'] + $params['endPrice'] - $_start;
            $_isConfer = '';
            if ( !empty($params['isConfer']) ){
                $_isConfer = ' OR `price` = 0 ';
            }
            $_child = " AND ((`price` >= $_start AND `price` <= $_end) $_isConfer) ";
        }
        if ( !empty($_child) ){
            $r['raw'] .= " AND `id` IN (select distinct(`saleId`) from t_sale_contact where 1 $_child) ";
        }
        //商标Tid查询
        if ( !empty($params['tid']) ){
            $_r['eq']   = array('auto'=>$params['tid']); 
            $_r['col']  = array('id');//商标号
            $_info = $this->load('trademark')->findTm($_r);
            if ( !empty($_info['id']) ){
                $r['eq']['number'] = $_info['id'];
            }elseif( empty($r['eq']['number']) ){
                return array('rows'=>array(),'total'=>0);
            }
        }
        //列表排序
        if ( !empty($params['listSort']) ){
            $r['raw'] .= " AND `listSort` > 0 ";
        }
         if($params['jname']==1){
             $r['order'] = array('name'=>'desc','date'=>'desc');
         }else{
             $r['order'] = array('date'=>'desc');
         }
        $res = $this->import('sale')->findAll($r);
        return $res;
    }
	
	//获取导出EXCEL数据
	public function getExcelList($params)
    {
		$r = $res = array();
		if ( empty($params) ){
            $res = $this->import('sale')->findAll($r);
            return $res;
        }
		
		
        $r['col']   = array('id');
		$r = $this->getWhere($params);
		$num = $this->import('sale')->count($r);
        $r['limit'] = $num;
        $res = $this->import('sale')->findAll($r);
        return $res;
    }
	
	
	//获取导出EXCEL数据
	public function getWhere($params)
    {
        $r = array();
        $r['col']   = array('id');
        $r['raw'] = ' 1 ';
        if ( !empty($params['tmNums']) ){
            $r['ft']['length'] = $params['tmNums'];
        }
        if ( !empty($params['tmType']) ){
            $r['ft']['type'] = $params['tmType'];
        }
        if ( !empty($params['tmClass']) ){
            $r['ft']['class'] = $params['tmClass'];
        }
        if ( !empty($params['tmLabel']) ){
            $r['ft']['label'] = $params['tmLabel'];
        }
        if ( !empty($params['salePlat']) ){
            $r['ft']['platform'] = $params['salePlat'];
        }
        if ( !empty($params['tmName']) ){
            $r['like']['name'] = $params['tmName'];
        }
        if ( !empty($params['tmNumber']) ){
            $r['eq']['number'] = $params['tmNumber'];
        }
        if ( !empty($params['saleStatus']) ){
            $r['eq']['status'] = $params['saleStatus'];
        }
        if ( !empty($params['dateStart']) ){
            $r['raw'] .= " AND date >= ".strtotime($params['dateStart']);
        }
        if ( !empty($params['dateEnd']) ){
            $r['raw'] .= " AND date <= ".(strtotime($params['dateEnd'])+24*3600);
        }

        //出售类型（出售、许可）
        if ( $params['saleType'] == 3 ){
            $r['eq']['isSale']      = 1;
            $r['eq']['isLicense']   = 1;
        }elseif ( $params['saleType'] == 2 ){
            $r['eq']['isLicense']   = 1;
        }elseif ( $params['saleType'] == 1 ){
            $r['eq']['isSale']      = 1;
        }
        if ( !empty($params['tmGroup']) ){
            $r['ft']['group'] = $params['tmGroup'];
        }
        if ( !empty($params['offprice']) ){
            $r['eq']['priceType']   = 1;
            $r['eq']['isOffprice']  = 1;
            $r['raw'] .= " AND (salePriceDate = 0 OR salePriceDate >= unix_timestamp(now())) ";
        }
        if ( !empty($params['isTop']) ){
            $r['eq']['isTop'] = 1;
        }
        if ( $params['isVerify'] == 2 ){
            $list = $this->getNoVerifySale();
            $list = empty($list) ? array(0) : $list;
            $r['in'] = array('id'=>$list);
        }

        $_child =  '';
        if ( !empty($params['saleSource']) ){//处理子表来源
            $source = $params['saleSource'];
            $_child .= " AND `source` = $source ";
        }
        if ( !empty($params['startPrice']) || !empty($params['endPrice']) ){//处理子表底价
            $_start = ($params['startPrice'] > $params['endPrice']) ? $params['endPrice'] : $params['startPrice'];
            $_end   = $params['startPrice'] + $params['endPrice'] - $_start;
            $_isConfer = '';
            if ( !empty($params['isConfer']) ){
                $_isConfer = ' OR `price` = 0 ';
            }
            $_child = " AND ((`price` >= $_start AND `price` <= $_end) $_isConfer) ";
        }
        if ( !empty($_child) ){
            $r['raw'] .= " AND `id` IN (select distinct(`saleId`) from t_sale_contact where 1 $_child) ";
        }

        $r['order'] = array('date'=>'desc');
        return $r;
    }
	
	
    //获取商品信息（可选包含的所有联系人与包装信息）
    public function getSaleInfo($saleId, $contact=1, $tminfo=1)
    {
        $r['eq'] = array(
            'id' => $saleId,
            );
        $info = $this->import('sale')->find($r);
        if ( empty($info) ) return array();
        if ( $contact ) $info['contact']    = $this->getSaleContact($saleId);
        if ( $tminfo ) $info['tminfo']      = $this->getSaleTminfo($saleId);
        return $info;
    }
	
	 //通过商标号获取商品信息
    public function getSaleContactByNum($number, $contact=1, $tminfo=1)
    {
        $r['eq'] = array(
            'number' => $number,
            );
        $info = $this->import('contact')->find($r);
        if ( empty($info) ) return array();
        return $info;
    }
	
	
    //改变商标信息的包装图片
    public function updateContactBZpic($data, $number)
    {
        $r['eq'] = array('number'=>$number);
        return $this->import('contact')->modify($data, $r);
    }
	

    //获取商品ID（saleId）下的所有联系人信息（可选，如果传id即查询当前ID下的联系人信息）
    public function getSaleContact($saleId, $id=0)
    {
        if ( $id <= 0 ){
            $r['eq'] = array(
                'saleId' => $saleId,
                );
            $r['limit'] = 20;
        }else{
            $r['eq'] = array(
                'id'        => $id,
                'saleId'    => $saleId,
                );
            $r['limit'] = 1;
        }

        return $this->import('contact')->find($r);
    }
	
	
	 //获取商品number和联系电话查询改商标下的联系人信息
    public function getSaleContactByPhone($number, $phone)
    {
		$r['eq'] = array(
			'number' => $number,
			'phone' => $phone,
			);
		$r['limit'] = 1;
        return $this->import('contact')->find($r);
    }

    //获取商品ID（saleId）下的商标包装信息
    public function getSaleTminfo($saleId)
    {
        $r['eq'] = array(
            'saleId' => $saleId,
            );
        return $this->import('tminfo')->find($r);
    }
	
	
	//通过商标号获取商标是否存在
    public function getSaleByNumber($number)
    {
        $r['eq'] = array(
            'number' => $number,
            );
        return $this->import('sale')->find($r);
    }

    //下架商标（批量时必须全部成功）
    public function saleDown($saleId, $reason='')
    {
        if ( empty($saleId) ) return false;
        //单条
        if ( !is_array($saleId) ){
            return $this->_saleDown($saleId, $reason);
        }
        //多条
        $flag = true;
        $this->begin('sale');
        foreach ($saleId as $id) {
            $res = $this->_saleDown($id, $reason);
            if ( !$res ){
                $this->rollBack('sale');
                return false;
            }
        }
        return $this->commit('sale');
    }

    //商品单条下架
    protected function _saleDown($saleId, $reason)
    {
        if ( $saleId <= 0 ) return false;
        //判断是否是上架状态
        if ( !$this->isSaleUp($saleId) ) return true;

        $r['eq']        = array('id'=>$saleId);
        $data['status'] = 2;
        $res = $this->import('sale')->modify($data, $r);
        if ( $res ) {
            $this->load('log')->addSaleLog($saleId, 2, $reason);//下架日志
            return true;
        }
        return false;
    }

    //上架商标
    public function saleUp($saleId, $memo="商品上架了")
    {
        if ( $saleId < 1 ) return false;
        if ( $this->isSaleUp($saleId) ) return true;

        $r['eq']        = array('id'=>$saleId);
        $data['status'] = 1;
        $res            = $this->import('sale')->modify($data, $r);
        if ( $res ) {
            $this->load('log')->addSaleLog($saleId, 1, $memo);//上架日志
            return true;
        }
        return false;
    }

    //创建默认的商品信息
    public function addDefault($number, $memo='后台创建默认商品')
    {
        if ( empty($number) ) return false;
        if ( $this->existSale($number) ) return false;

        $info   = $this->load('trademark')->getTmInfo($number);
        if ( empty($info) ) return false;

        $other  = $this->load('trademark')->getTmOther($number);
        if ( empty($other) ) return false;
        
        $class      = implode(',', $info['class']);
        $platform   = implode(',', $other['platform']);
        $viewPhone  = $this->load('phone')->getRandPhone();
        $regDate    = strtotime($info['reg_date']) > 0 ? strtotime($info['reg_date']) : 0;
        $sale = array(
            'tid'           => intval($info['tid']),
            'number'        => $number,
            'class'         => $class,
            'group'         => trim($info['group']),
            'name'          => trim($info['name']),
            'pid'           => intval($info['pid']),
            'price'         => 0,
            'priceType'     => 2,
            'isOffprice'    => 2,
            'salePrice'     => 0,
            'salePriceDate' => 0,
            'status'        => 3, 
            'isSale'        => 1,
            'isLicense'     => 2,
            'isTop'         => 0,
            'type'          => $other['type'],
            'platform'      => $platform,
            'label'         => '',
            'length'        => $other['length'],
            'regDate'       => $regDate,
            'date'          => time(),
            'viewPhone'     => $viewPhone,
            'hits'          => 0,
            'memo'          => $memo,
        );
        $tminfo = array(
            'number'    => $number,
            'memo'      => $memo,
            'intro'     => '',
        );
        $this->begin('sale');
        $saleId = $this->addSale($sale);//创建商品信息
        $tmId   = $this->addTminfo($tminfo, $saleId);//创建商品包装信息
        $black  = $this->load('blacklist')->setBlack($number);
        if ( $saleId && $tmId && $black ){
            $this->load('log')->addSaleLog($saleId, 3, $memo);//创建商品日志
            $this->commit('sale');
            return $saleId;
        }
        $this->rollBack('sale');
        return false;
    }

    //更新商品信息
    public function update($data, $saleId)
    {
        $r['eq'] = array('id'=>$saleId);
        return $this->import('sale')->modify($data, $r);
    }

    //更新联系人
    public function updateContact($data, $scId)
    {
        $r['eq'] = array('id'=>$scId);
        return $this->import('contact')->modify($data, $r);
    }

    //更新包装信息
    public function updateTminfo($data, $saleId)
    {
        $r['eq'] = array('saleId'=>$saleId);
        return $this->import('tminfo')->modify($data, $r);
    }

    //打包新增出售（数据过滤使用）
    public function addAll($data)
    {
        if ( empty($data) || empty($data['sale']) || empty($data['saleTminfo']) || empty($data['saleContact']) ){
            return false;
        }
        $sale       = $data['sale'];
        $tminfo     = $data['saleTminfo'];
        $contact    = $data['saleContact'];

        $this->begin('sale');
        $saleId = $this->addSale($sale);
        if ( $saleId <= 0 ) {
            $this->rollBack('sale');
            return false;
        }
        $tminfoId   = $this->addTminfo($tminfo, $saleId);//添加包装信息
        $contactId  = $this->addContact($contact, $saleId);//添加联系人
        $black      = $this->load('blacklist')->setBlack($sale['number']);//加入黑名单
		
        if ( $tminfoId && $contactId && $black ) {
            return $this->commit('sale');
        } 
        $this->rollBack('sale');
        return false;
    }

    //添加出售基础信息
    protected function addSale($data)
    {
        if ( $this->existSale($data['number']) ) return false;

        return $this->import('sale')->create($data);
    }

    //添加商标基础信息
    protected function addTminfo($data, $saleId)
    {
        if ( $this->existTminfo($data['number']) ) return false;

        $data['saleId'] = $saleId;
        return $this->import('tminfo')->create($data);
    }

    //添加出售联系人信息（可多个）
    public function addContact($data, $saleId)
    {
        //判断是否二维数组
        if ( is_array(current($data)) ){
            foreach ($data as $k => $v) {
                $res = $this->addContact($v, $saleId);
                if ( !$res ) return false;
            }
            return $res;
        }        
        $data['saleId'] = $saleId;
        return $this->import('contact')->create($data);
    }

    //删除商品
    public function deleteSale($ids, $type=2, $memo='', $black=2, $date='')
    {
        if ( empty($ids) || !is_array($ids) ) return false;

        if ( $type == 1 ){
            $saleDate = empty($date) ? 0 : strtotime($date);
        }else{
            $saleDate = 0;
        }
        $this->begin('sale');
        foreach ($ids as $id) {
            $sale = $this->getSaleInfo($id);
            if ( empty($sale) ) continue;
            $number = $sale['number'];
            $saleId = $sale['id'];
            $data = array(
                'saleId'    => $id,
                'number'    => $number,
                'type'      => $type,
                'memberId'  => $this->userId,
                'data'      => serialize($sale),
                'date'      => time(),
                'saleDate'  => $saleDate,
                'memo'      => $memo,
            );
            $hisId = $this->import('history')->create($data);//创建商品历史记录
            if ( $black == 1 ) {
                $isBlack = $this->load('blacklist')->outBlack($number);//剔除黑名单
            }else{
                $isBlack = true;
            }
            //处理用户出售信息历史记录
            foreach ($sale['contact'] as $k => $v) {
                $addUserHistory = $this->addUserHistory($v, $sale, $type);
                if ( !$addUserHistory ){
                    $this->rollBack('sale');
                    return false;
                }
            }

            $r['eq']    = array('id'=>$saleId);
            $delSale    = $this->import('sale')->remove($r);//删除商品

            $rl['eq']   = array('saleId'=>$saleId);
            $delContact = $this->import('contact')->remove($rl);//删除联系人
            $delTminfo  = $this->import('tminfo')->remove($rl);//删除包装信息
            if ( $hisId <= 0 || !$isBlack || !$delSale || !$delContact || !$delTminfo ){
                $this->rollBack('sale');
                return false;
            }
            $this->load('log')->addSaleLog($saleId, 16, "商品：$number,商品ID：$saleId 已删除", serialize($sale));//记录日志
        }
        return $this->commit('sale');
    }

    //删除联系人
    public function delContact($id, $saleId, $type)
    {
        if ( empty($id) || empty($saleId) ) return false;

        //要判断联系人是否至少有一个
        if ( $this->isSaleUp($saleId) ){
            $r['eq']['isVerify']    = 1;
        }
        $r['eq']['saleId']  = $saleId;
        $r['raw']           = " id != $id ";
        $total = $this->import('contact')->count($r);
        if ( $total < 1 ) return false;
        ////////////////////////////////
        $this->begin('sale');
        $role['eq'] = array('id'=>$id);
        $contact = $this->findContact($role);

        //处理联系人为前台提交的数据，保存相应日志记录
        $res    = $this->addUserHistory($contact, '', $type);
        $res2   = $this->import('contact')->remove($role);
        if ( !$res || !$res2 ){
            $this->rollBack('sale');
            return false;
        }
        return $this->commit('sale');
    }

    //记录用户商品历史数据
    public function addUserHistory($contact, $saleInfo='', $type)
    {
        if ( empty($contact) || empty($type) ) return false;
        //不需要保存的数据，直接返回正确
        if ( $contact['uid'] <= 0 && $contact['userId'] <=0 ) return true;

        if ( empty($saleInfo) ){
            $saleInfo = $this->getSaleInfo($contact['saleId']);
        }
        $data = array(
            'uid'       => $contact['uid'],
            'userId'    => $contact['userId'],
            'saleId'    => $contact['saleId'],
            'number'    => $contact['number'],
            'name'      => $saleInfo['name'],
            'type'      => $type,
            'saleDate'  => $contact['date'],
            'price'     => $contact['price'],
            'data'      => serialize($saleInfo),
            'date'      => time(),
            );
        return $this->import('userSaleHistory')->create($data);
    }

    //审核联系人
    public function setVerify($id, $saleId)
    {
        if ( empty($id) && empty($saleId) ) return false;

        $r['eq']    = array('id'=>$id);
        $data       = array('isVerify'=>1,'updated'=>time());
        $res = $this->import('contact')->modify($data, $r);
        if ( !$res ) return false;
        if ( $this->isSaleUp($saleId) )  return true;

        $memo = '联系人审核通过并上架商品';
        return $this->saleUp($saleId, $memo);
    }

    //驳回联系人
    public function delVerify($id, $saleId)
    {
        if ( empty($id) || empty($saleId) ) return false;
        
        return $this->delContact($id, $saleId, 3);
    }

    //判断商品是否上架
    public function isSaleUp($saleId)
    {
        if ( empty($saleId) ) return false;
        $r['eq'] = array('id'=>$saleId,'status'=>1);
        $isUp = $this->import('sale')->count($r);
        if ( $isUp ) return true;
        return false;
    }

    //判断商品联系人是否有审核通过的记录
    public function existVerifyContact($saleId)
    {
        if ( empty($saleId) ) return false;
        $r['eq'] = array('saleId'=>$saleId, 'isVerify'=>1);
        $total = $this->import('contact')->count($r);
        if ( $total > 0 ) return true;
        return false;
    }

    //判断是否出售基础信息是否存在
    public function existSale($number)
    {
        if ( empty($number) ) return false;
        $r['eq']    = array('number'=>$number);
        $r['col']   = array('id');
        $res = $this->import('sale')->find($r);
        if ( empty($res) ) return false;
        return $res['id'];
    }

    //判断是否商标包装信息是否存在
    public function existTminfo($number)
    {
        if ( empty($number) ) return false;
        $r['eq'] = array('number'=>$number);
        $res = $this->import('tminfo')->find($r);
        if ( empty($res) ) return false;
        return true;
    }

    //判断联系人信息是否存在
    public function existContact($number, $phone='', $uid=0, $userId=0)
    {
        if ( empty($number) ) return false;

        $r['eq'] = array('number'=>$number);
        if ( $userId > 0 ){
            $r['eq']['userId'] = $userId;
        }
        if ( $uid > 0 ){
            $r['eq']['uid'] = $uid;
        }
        if ( !empty($phone) ){
            $r['eq']['phone'] = $phone;
        }
        $count = $this->import('contact')->count($r);
        return ($count > 0) ? true : false;
    }

    //通过商品ID判断是否在黑名单中
    public function isBlack($saleId)
    {
        if ( empty($saleId) ) return false;
        $r['eq']    = array('id'=>$saleId);
        $r['col']   = array('number');
        $sale       = $this->import('sale')->find($r);
        if ( empty($sale['number']) ) return false;
        return $this->load('blacklist')->isBlack($sale['number']);
    }

    //统计所有商品数量
    public function countSale()
    {
        return $this->import('sale')->count();
    }

    //更新包装信息
    public function setEmbellish($saleId, $sale, $tminfo)
    {
        if ( $saleId <= 0 ) return fales;
        if ( empty($sale) ) return false;

        $this->begin('sale');
        $res1 = $this->update($sale, $saleId);
        $res2 = $this->updateTminfo($tminfo, $saleId);
        if ( $res1 && $res2 ){
            $this->load('log')->addSaleLog($saleId, 12);//修改商品包装日志
            return $this->commit('sale');
        }
        $this->rollBack();
        return false;
    }

    //更新所有相关的显示手机号码
    public function setAllViewPhone($old, $phone)
    {
        $r['eq'] = array('viewPhone'=>$old);
        $has = $this->import('sale')->count($r);
        if ( $has <= 0 ) return true;
        
        $data = array('viewPhone'=>$phone);
        return $this->import('sale')->modify($data, $r);
    }
    //统计所有商品的状态
    public function countSaleStatus()
    {
        $r['eq'] = array('status'=>1);
        $onSale = $this->import('sale')->count($r);
        $r['eq'] = array('status'=>2);
        $down   = $this->import('sale')->count($r);
        $r['eq'] = array('status'=>3);
        $verify = $this->import('sale')->count($r);

        $total = array(
            '1' => $onSale,
            '2' => $down,
            '3' => $verify,
            );
        return $total;
    }
    //获取未审核的联系人
    public function getNoVerifySale()
    {
        $r['eq'] = array(
            'isVerify' => 2
            );
        $r['group'] = array('saleId'=>'asc');
        $r['limit'] = 1000000;
        $r['col']   = array('saleId');
        $res = $this->import('contact')->find($r);
        $list = arrayColumn($res, 'saleId');
        $list = array_filter($list);
        return $list;
    }
	
	//组装商标数据，写入数据库.用事物做，一个不写入，则都不写入
	public function saleZZ($info,$data,$param)
    {
		//事物开始
		//$start = $this->msectime();
		$number = $data['number'];
		$type = $label = $platform = $length = array();
		$price      = 0;//指导价格
		$priceType  = 2;//价格类型 1定价，2议价
		$isOffprice = 2;//是否特价
		$salePrice  = 0;//特价价格
		$salePriceDate = 0;//特价时间

		$status = 1;//销售中
		$isTop  = 0;//不置项
		$date   = 0;//出售时间
		$hits   = 0;//阅读数
		$class  = implode(',', $info['class']);
		//联系人
		$contact = array(
			'source'        => intval($param['source']),
			'userId'        => 0,
			'tid'           => intval($info['tid']),
			'number'        => $number,
			'name'          => $param['name'] ? $param['name'] : $data['name'],
			'phone'         => $param['phone'] ? $param['phone'] : $data['phone'],
			'price'         => isset($data['price']) ? $data['price'] : 0,
			'saleType'      => 1,
			'isVerify'      => 1,
			'advisor'       => $data['advisor'],
			'department'    => $data['department'],
			'date'          => time(),
			'isVerify'  	=> 1,
		);
		
		//出售数据
		$other  = $this->load('trademark')->getTmOther($number);
		

        if ( empty($other) ) return false;
        $platform   = implode(',', $other['platform']);
		$length	    = $other['length'];
		$type	    = $other['type'];
		$viewPhone  = $this->load('phone')->getRandPhone();
        $regDate    = strtotime($info['reg_date']) > 0 ? strtotime($info['reg_date']) : 0;
		$sale = array(
			'tid'           => intval($info['tid']),
			'number'        => $number,
			'class'         => $class,
			'group'         => trim($info['group']),
			'name'          => trim($info['name']),
			'pid'           => intval($info['pid']),
			'price'         => $price,
			'priceType'     => $priceType,
			'isOffprice'    => $isOffprice,
			'salePrice'     => $salePrice,
			'salePriceDate' => $salePriceDate,
			'status'        => $status, 
			'isSale'        => 1,
			'isLicense'     => 2,
			'isTop'         => $isTop,
			'type'          => $type,
			'platform'      => $platform,
			'label'         => '',
			'length'        => $length,
            'regDate'       => $regDate,
			'date'          => $date,
			'viewPhone'     => $viewPhone,
			'hits'          => intval($hits),
			'memo'          => $data['memo'],
			'date'          => time(),
			);
		$tminfo = array(
            'number'    => $number,
            'memo'      => $data['memo'],
			'intro'     => '',
			
        );
	
		$result = array(
			'sale'          => $sale,
			'saleTminfo'    => $tminfo,
			'saleContact'   => $contact,
			);
		$res = $this->load('internal')->addAll($result);
		return $res;
    }

    public function findContact($r)
    {
        return $this->import('contact')->find($r);
    }

    //无条件删除联系人，慎用！！！
    public function _delContact($id, $contact, $type)
    {
        if ( empty($id) ) return false;

        $this->begin('sale');
        $r['eq'] = array('id'=>$id);
        $res = $this->import('contact')->remove($r);        
        $flag = $this->addUserHistory($contact, '', $type);
        if ( $res && $flag ){
            return $this->commit('sale');
        }
        $this->rollBack('sale');
        return false;
    }
}
?>