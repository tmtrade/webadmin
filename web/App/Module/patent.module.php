<?
/**
* 国内商标
*
* 专利商品创建，修改，删除等
*
* @package	Module
* @author	Far
* @since	2016-04-27
*/
class PatentModule extends AppModule
{
    public $models = array(
        'patent'                => 'patent',
        'contact'               => 'patentContact',
        'history'               => 'patentHistory',
        'tminfo'                => 'patentInfo',
        'income'                => 'income',
        'userPatentHistory'     => 'userPatentHistory',
        'ptlist'                => 'patentList',
    );
    
    public function getList($params, $page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['col']   = array('id');
        if ( empty($params) ){
            $res = $this->import('patent')->findAll($r);
            return $res;
        }
        $r['raw'] = ' 1 ';
        //专利分类
        if ( !empty($params['type']) ){
            $r['eq']['type'] = $params['type'];
        }
         //行业分类
         if ( !empty($params['tmClass']) ){
             if($params['type'] !=3 ){
                $classArr = explode(",", $params['tmClass']);
                $_class = array_map('ord', $classArr);
                $r['ft']['class'] = implode(',', $_class);
             }else{
                 $r['ft']['class'] = $params['tmClass'];
             }
        }
        if ( !empty($params['tmName']) ){
            $r['like']['title'] = $params['tmName'];
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
        
        //展示未用开始
        if ( !empty($params['offprice']) ){
            $r['eq']['priceType']   = 1;
            $r['eq']['isOffprice']  = 1;
            $r['raw'] .= " AND (salePriceDate = 0 OR salePriceDate >= unix_timestamp(now())) ";
        }
        if ( !empty($params['isTop']) ){
            $r['raw'] .= " AND `isTop` > 0 ";
        }
        
        $_child =  '';
        if ( !empty($params['saleSource']) ){//处理子表来源
            $source = $params['saleSource'];
            $_child .= " AND `source` = $source ";
        }
        if ( !empty($params['tmType'])){//处理子表底价
            $tmPrice 	= C("SEARCH_PRICE");//出售搜索底价
            $_start = $tmPrice[$params['tmType']][0];
            $_end   = $tmPrice[$params['tmType']][1];
            $_isConfer = '';
            if ($params['tmType']==8){
                $_child .= ' AND `price` = 0 ';
            }else{
                $_child .= " AND (`price` >= $_start AND `price` < $_end) ";
            }
            
        }
        if ( !empty($_child) ){
            $r['raw'] .= " AND `id` IN (select distinct(`patentId`) from t_patent_contact where 1 $_child) ";
        }
        //列表排序
        if ( !empty($params['listSort']) ){
            $r['raw'] .= " AND `listSort` > 0 ";
        }
        $r['order'] = array('date'=>'desc');
        $res = $this->import('patent')->findAll($r);
        return $res;
    }
	
    //获取导出EXCEL数据
    public function getExcelList($params)
    {
        $r = $res = array();
        if ( empty($params) ){
            $res = $this->import('patent')->findAll($r);
            return $res;
        }
        $r['col']   = array('id');
        $r = $this->getWhere($params);
        $num = $this->import('patent')->count($r);
        $r['limit'] = $num;
        $res = $this->import('patent')->findAll($r);
        return $res;
    }
	
	
	//获取导出EXCEL数据
	public function getWhere($params)
    {
        $r = array();
        $r['col']   = array('id');
        $r['raw'] = ' 1 ';
        //专利分类
        if ( !empty($params['type']) ){
            $r['eq']['type'] = $params['type'];
        }
         //行业分类
         if ( !empty($params['tmClass']) ){
            $r['ft']['class'] = $params['tmClass'];
        }
        if ( !empty($params['tmName']) ){
            $r['like']['title'] = $params['tmName'];
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
        
        //展示未用开始
        if ( !empty($params['offprice']) ){
            $r['eq']['priceType']   = 1;
            $r['eq']['isOffprice']  = 1;
            $r['raw'] .= " AND (salePriceDate = 0 OR salePriceDate >= unix_timestamp(now())) ";
        }
        if ( !empty($params['isTop']) ){
            $r['raw'] .= " AND `isTop` > 0 ";
        }
        
        $_child =  '';
        if ( !empty($params['saleSource']) ){//处理子表来源
            $source = $params['saleSource'];
            $_child .= " AND `source` = $source ";
        }
        if ( !empty($params['tmType'])){//处理子表底价
            $tmPrice 	= C("SEARCH_PRICE");//出售搜索底价
            $_start = $tmPrice[$params['tmType']][0];
            $_end   = $tmPrice[$params['tmType']][1];
            $_isConfer = '';
            if ($params['tmType']==8){
                $_child .= ' AND `price` = 0 ';
            }else{
                $_child .= " AND (`price` >= $_start AND `price` < $_end) ";
            }
            
        }
        if ( !empty($_child) ){
            $r['raw'] .= " AND `id` IN (select distinct(`patentId`) from t_patent_contact where 1 $_child) ";
        }
        //列表排序
        if ( !empty($params['listSort']) ){
            $r['raw'] .= " AND `listSort` > 0 ";
        }
        $r['order'] = array('date'=>'desc');
        return $r;
    }
	
	
    //获取商品信息（可选包含的所有联系人与包装信息）
    public function getPatentInfo($patentId, $contact=1, $tminfo=1)
    {
        $r['eq'] = array(
            'id' => $patentId,
            );
        $info = $this->import('patent')->find($r);
        if ( empty($info) ) return array();
        if ( $contact ) $info['contact']    = $this->getSaleContact($patentId);
        if ( $tminfo ) $info['tminfo']      = $this->getSaleTminfo($patentId);
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
	

    //获取商品ID（patentId）下的所有联系人信息（可选，如果传id即查询当前ID下的联系人信息）
    public function getSaleContact($patentId, $id=0)
    {
        if ( $id <= 0 ){
            $r['eq'] = array(
                'patentId' => $patentId,
                );
            $r['limit'] = 20;
        }else{
            $r['eq'] = array(
                'id'        => $id,
                'patentId'    => $patentId,
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

    //获取商品ID（patentId）下的商标包装信息
    public function getSaleTminfo($patentId)
    {
        $r['eq'] = array(
            'patentId' => $patentId,
            );
        return $this->import('tminfo')->find($r);
    }
	
	
	//通过商标号获取商标是否存在
    public function getSaleByNumber($number)
    {
        $r['eq'] = array(
            'number' => $number,
            );
        return $this->import('patent')->find($r);
    }

    //下架商标（批量时必须全部成功）
    public function patentDown($patentId, $reason='')
    {
        if ( empty($patentId) ) return false;
        //单条
        if ( !is_array($patentId) ){
            return $this->_patentDown($patentId, $reason);
        }
        //多条
        $flag = true;
        $this->begin('patent');
        foreach ($patentId as $id) {
            $res = $this->_patentDown($id, $reason);
            if ( !$res ){
                $this->rollBack('patent');
                return false;
            }
        }
        return $this->commit('patent');
    }

    //商品单条下架
    protected function _patentDown($patentId, $reason)
    {
        if ( $patentId <= 0 ) return false;
        //判断是否是上架状态
        if ( !$this->isSaleUp($patentId) ) return true;

        $r['eq']        = array('id'=>$patentId);
        $data['status'] = 2;
        $res = $this->import('patent')->modify($data, $r);
        if ( $res ) {
            $this->load('log')->addPatentLog($patentId, 2, $reason);//下架日志
            return true;
        }
        return false;
    }

    //上架商标
    public function saleUp($patentId, $memo="专利商品上架了")
    {
        if ( $patentId < 1 ) return false;
        if ( $this->isSaleUp($patentId) ) return true;

        $r['eq']        = array('id'=>$patentId);
        $data['status'] = 1;
        $res            = $this->import('patent')->modify($data, $r);
        if ( $res ) {
            $this->load('log')->addPatentLog($patentId, 1, $memo);//上架日志
            return true;
        }
        return false;
    }

    //创建默认的商品信息
    public function addDefault($number, $info, $contact="")
    {
        if ( empty($number) ) return false;
        if ( $this->existSale($number) ) return false;
        $number = strtolower($number);//专利编号 带.
        $code   = $info['id'];

        $_class = array();
        $_group = array();
        foreach ($info['ipcs'] as $ky => $val) {
            array_push($_class, current($val['ancestors']));
            array_push($_group, $val['id']);
            $_group = array_merge($_group, (array)$val['ancestors']);
        }
        $title  = $info['title']['original'];//专利标题
        $type   = 0;//专利类型
        if ( strpos($info['typeName'], '发明') !== false ){
            $type = 1;
        }elseif ( strpos($info['typeName'], '新型') !== false || strpos($info['typeName'], '实用') !== false ){
            $type = 2;
        }elseif ( strpos($info['typeName'], '外观') !== false ){
            $type = 3;
        }
        
        $group = implode(',', array_unique(array_filter($_group)));//专利所有群组
        $_class = array_unique(array_filter($_class));
           if ( $type == 3 ){
               $class  = implode(',', $_class);
           }else{
               if ( empty($_class) ){
                   $_class = array();
                   foreach (explode(',', $group) as $ky => $val) {
                       $_class[] = strtolower( substr($val,0,1) );
                   }
               }
               $_class = array_map('strtolower', $_class);
               $class  = empty($_class) ? '' : implode(',', array_map('ord', $_class));
           }
            
        $applyDate  = (int)strtotime($info['application_date']);//申请日
        $publicDate = (int)strtotime($info['earliest_publication_date']);//最早公开日
        $viewPhone  = $this->load('phone')->getRandPhone();
        $_memo      = empty($info['abstract']['original'])?$info['abstract']['en']:$info['abstract']['original'];
        
        $patent = array(
            'number'        => $number,
            'code'          => $code,
            'class'         => $class,
            'group'         => $group,
            'title'         => $title,
            'type'          => $type,
            'applyDate'     => $applyDate,
            'publicDate'    => $publicDate,
            'date'          => time(),
            'viewPhone'     => $viewPhone,
            'memo'          => $_memo,
            'status'        => 1,
            );

        $ptinfo = array(
            'number'    => $number,
            'code'      => $code,
            'intro'     => '',
            );
        $this->begin('patent');//开始事务
        $patentId  = $this->import('patent')->create($patent);
        $contactId = true;
        if ( $patentId > 0 ){
            $ptinfo['patentId'] = $patentId;
            $flag1      = $this->import('tminfo')->create($ptinfo);
            if(!empty($contact)){
                $contactId  = $this->addContact($contact, $patentId);//添加联系人
            }
            $this->load('log')->addPatentLog($patentId, 3, $_memo);//创建商品日志
            if($flag1 && $contactId){
                $this->commit('patent');
                return $patentId;
            }
        }
        $this->rollBack('patent');
        return false;
        
    }

    //更新商品信息
    public function update($data, $patentId)
    {
        $r['eq'] = array('id'=>$patentId);
        return $this->import('patent')->modify($data, $r);
    }

    //更新联系人
    public function updateContact($data, $scId)
    {
        $r['eq'] = array('id'=>$scId);
        return $this->import('contact')->modify($data, $r);
    }

    //更新包装信息
    public function updateTminfo($data, $patentId)
    {
        $r['eq'] = array('patentId'=>$patentId);
        return $this->import('tminfo')->modify($data, $r);
    }

    //添加出售基础信息
    protected function addSale($data)
    {
        if ( $this->existSale($data['number']) ) return false;

        return $this->import('patent')->create($data);
    }

    //添加商标基础信息
    protected function addTminfo($data, $patentId)
    {
        if ( $this->existTminfo($data['number']) ) return false;

        $data['patentId'] = $patentId;
        return $this->import('tminfo')->create($data);
    }

    //添加出售联系人信息（可多个）
    public function addContact($data, $patentId)
    {
        //判断是否二维数组
        if ( is_array(current($data)) ){
            foreach ($data as $k => $v) {
                $res = $this->addContact($v, $patentId);
                if ( !$res ) return false;
            }
            return $res;
        }        
        $data['patentId'] = $patentId;
        return $this->import('contact')->create($data);
    }

    //删除商品
    public function deleteSale($ids, $type=2, $memo='', $date='')
    {
        if ( empty($ids) || !is_array($ids) ) return false;

        if ( $type == 1 ){
            $patentDate = empty($date) ? 0 : strtotime($date);
        }else{
            $patentDate = 0;
        }
        $this->begin('patent');
        foreach ($ids as $id) {
            $patent = $this->getPatentInfo($id);
            if ( empty($patent) ) continue;
            $number = $patent['number'];
            $patentId = $patent['id'];
            $data = array(
                'patentId'    => $id,
                'number'    => $number,
                'type'      => $type,
                'memberId'  => $this->userId,
                'data'      => serialize($patent),
                'date'      => time(),
                'saleDate'  => $patentDate,
                'memo'      => $memo,
            );
            $hisId = $this->import('history')->create($data);//创建商品历史记录
            //处理用户出售信息历史记录
            foreach ($patent['contact'] as $k => $v) {
                $addUserHistory = $this->addUserHistory($v, $patent, $type);
                if ( !$addUserHistory ){
                    $this->rollBack('patent');
                    return false;
                }
            }

            $r['eq']    = array('id'=>$patentId);
            $delSale    = $this->import('patent')->remove($r);//删除商品

            $rl['eq']   = array('patentId'=>$patentId);
            $delContact = $this->import('contact')->remove($rl);//删除联系人
            $delTminfo  = $this->import('tminfo')->remove($rl);//删除包装信息
            if ( $hisId <= 0 || !$delSale || !$delContact || !$delTminfo ){
                $this->rollBack('patent');
                return false;
            }
            $this->load('log')->addPatentLog($patentId, 16, "专利商品：$number,商品ID：$patentId 已删除", serialize($patent));//记录日志
        }
        return $this->commit('patent');
    }

    //删除联系人
    public function delContact($id, $patentId, $type)
    {
        if ( empty($id) || empty($patentId) ) return false;

        //要判断联系人是否至少有一个
        if ( $this->isSaleUp($patentId) ){
            $r['eq']['isVerify']    = 1;
        }
        $r['eq']['patentId']  = $patentId;
        $r['raw']           = " id != $id ";
        $total = $this->import('contact')->count($r);
        if ( $total < 1 ) return false;
        ////////////////////////////////
        $this->begin('patent');
        $role['eq'] = array('id'=>$id);
        $contact = $this->findContact($role);

        //处理联系人为前台提交的数据，保存相应日志记录
        $res    = $this->addUserHistory($contact, '', $type);
        $res2   = $this->import('contact')->remove($role);
        if ( !$res || !$res2 ){
            $this->rollBack('patent');
            return false;
        }
        return $this->commit('patent');
    }

    //记录用户商品历史数据
    public function addUserHistory($contact, $patentInfo='', $type)
    {
        if ( empty($contact) || empty($type) ) return false;
        //不需要保存的数据，直接返回正确
        if ( $contact['uid'] <= 0 && $contact['userId'] <=0 ) return true;

        if ( empty($patentInfo) ){
            $patentInfo = $this->getPatentInfo($contact['patentId']);
        }
        $data = array(
            'uid'       => $contact['uid'],
            'userId'    => $contact['userId'],
            'patentId'    => $contact['patentId'],
            'number'    => $contact['number'],
            'name'      => $patentInfo['title'],
            'type'      => $type,
            'saleDate'  => $contact['date'],
            'price'     => $contact['price'],
            'data'      => serialize($patentInfo),
            'date'      => time(),
            );
        return $this->import('userPatentHistory')->create($data);
    }

    //审核联系人
    public function setVerify($id, $patentId)
    {
        if ( empty($id) && empty($patentId) ) return false;

        $r['eq']    = array('id'=>$id);
        $data       = array('isVerify'=>1,'updated'=>time());
        $res = $this->import('contact')->modify($data, $r);
        if ( !$res ) return false;
        if ( $this->isSaleUp($patentId) )  return true;

        $memo = '联系人审核通过并上架专利商品';
        return $this->saleUp($patentId, $memo);
    }

    //驳回联系人
    public function delVerify($id, $patentId)
    {
        if ( empty($id) || empty($patentId) ) return false;
        
        return $this->delContact($id, $patentId, 3);
    }

    //判断商品是否上架
    public function isSaleUp($patentId)
    {
        if ( empty($patentId) ) return false;
        $r['eq'] = array('id'=>$patentId,'status'=>1);
        $isUp = $this->import('patent')->count($r);
        if ( $isUp ) return true;
        return false;
    }

    //判断商品联系人是否有审核通过的记录
    public function existVerifyContact($patentId)
    {
        if ( empty($patentId) ) return false;
        $r['eq'] = array('patentId'=>$patentId, 'isVerify'=>1);
        $total = $this->import('contact')->count($r);
        if ( $total > 0 ) return true;
        return false;
    }

    //判断是否出售基础信息是否存在
    public function existSale($number)
    {
        if ( empty($number) ) return false;
        $code   = (strpos($number, '.') !== false) ? strstr($number, '.', true) : $number;
        $r['eq']    = array('code'=>$code);
        $r['col']   = array('id');
        $res = $this->import('patent')->find($r);
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
    public function isBlack($patentId)
    {
        if ( empty($patentId) ) return false;
        $r['eq']    = array('id'=>$patentId);
        $r['col']   = array('number');
        $patent       = $this->import('patent')->find($r);
        if ( empty($patent['number']) ) return false;
        return $this->load('blacklist')->isBlack($patent['number']);
    }

    //统计所有商品数量
    public function countSale()
    {
        return $this->import('patent')->count();
    }

    //更新包装信息
    public function setEmbellish($patentId, $patent, $tminfo)
    {
        if ( $patentId <= 0 ) return fales;
        if ( empty($patent) ) return false;

        $this->begin('patent');
        $res1 = $this->update($patent, $patentId);
        $res2 = $this->updateTminfo($tminfo, $patentId);
        if ( $res1 && $res2 ){
            $this->load('log')->addPatentLog($patentId, 12);//修改商品包装日志
            return $this->commit('patent');
        }
        $this->rollBack();
        return false;
    }

    //更新所有相关的显示手机号码
    public function setAllViewPhone($old, $phone)
    {
        $r['eq'] = array('viewPhone'=>$old);
        $has = $this->import('patent')->count($r);
        if ( $has <= 0 ) return true;
        
        $data = array('viewPhone'=>$phone);
        return $this->import('patent')->modify($data, $r);
    }
    //统计所有商品的状态
    public function countSaleStatus()
    {
        $r['eq'] = array('status'=>1);
        $onSale = $this->import('patent')->count($r);
        $r['eq'] = array('status'=>2);
        $down   = $this->import('patent')->count($r);
        $r['eq'] = array('status'=>3);
        $verify = $this->import('patent')->count($r);

        $total = array(
            '1' => $onSale,
            '2' => $down,
            '3' => $verify,
            );
        return $total;
    }
	

    public function findContact($r)
    {
        return $this->import('contact')->find($r);
    }
    
    //获取单个专利数据
    public function getPatentInfoByWanxiang($number,$addlist=1)
    {
        if ( empty($number) ) return array();

        $eq = (strpos($number, '.') !== false) ? array('number'=>$number) : array('code'=>$number);

        $r['eq']    = $eq;
        $r['limit'] = 1;
        $info = $this->import('ptlist')->find($r);
        if ( !empty($info['data']) ){
            $data = unserialize($info['data']);
            return $data;
        }
        
        $code   = (strpos($number, '.') !== false) ? strstr($number, '.', true) : $number;
        $url    = 'http://wanxiang.chaofan.wang/detail.php?id=%s&t=json';

        $url    = sprintf($url, $code);
        $data   = $this->requests($url);
        if ( !empty($data) && !empty($data['id']) && $addlist==1){            
            $_data = array(
                'code'      => $code,
                'number'    => $number,
                'data'      => serialize($data),
                );
            $this->import('ptlist')->create($_data);
        } 
        return $data;
    }
    
    //请求连接
    public function requests( $url, $type='GET', $params=array() )
    {
        $_type = ($type == 'POST') ? 'POST' : 'GET';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_type);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt(
            $ch, CURLOPT_POSTFIELDS, $params
        );
        $result = curl_exec($ch);
        
        if($result === false) {
            $result = curl_error($ch);
        }
        curl_close($ch);
        $res =  json_decode(trim($result,chr(239).chr(187).chr(191)),true);
        return $res;
    }

    /**
     * 交易完成操作
     * @param $params
     * @param $uid int 登录用户
     * @return int 0成功,1参数错误,2保存收益表错误,3保存用户出售信息出错,4删除相关信息出错
     */
    public function complateSale($params,$uid){
        $this->begin('income');
        $saleId = $params['saleId'];
        $sale = $this->getPatentInfo($saleId);
        if(!$sale){
            $this->rollBack('income');
            return 1;
        }
        if($params['uid']){ //保存到收益表中
            //保存到收益表中
            unset($params['saleId']);
            $params['name'] = $sale['title']?$sale['title']:'暂无名称';
            $params['number'] = $sale['number'];
            $rst = $this->import('income')->create($params);
            if(!$rst){
                $this->rollBack('income');
                return 2;
            }
        }
        //保存操作记录
        $r = array(
            'patentId'    => $saleId,
            'number'    => $sale['number'],
            'type'      => 1,
            'memberId'  => $uid,
            'data'      => serialize($sale),
            'date'      => time(),
            'saleDate'  => $params['date'],
            'memo'      => '交易成功(自动添加)',
        );
        //创建商品历史记录
        $hisId = $this->import('history')->create($r);
        //处理用户出售信息历史记录
        foreach ($sale['contact'] as $k => $v) {
            $addUserHistory = $this->addUserHistory($v, $sale, 1);
            if ( !$addUserHistory ){
                $this->rollBack('income');
                return 3;
            }
        }

        $r = array();
        $r['eq']    = array('id'=>$saleId);
        $delSale    = $this->import('patent')->remove($r);//删除商品
        $r = array();
        $r['eq']   = array('patentId'=>$saleId);
        $delContact = $this->import('contact')->remove($r);//删除联系人
        $delTminfo  = $this->import('tminfo')->remove($r);//删除包装信息
        if ( $hisId <= 0 || !$delSale || !$delContact || !$delTminfo ){
            $this->rollBack('income');
            return 4;
        }
        $this->load('log')->addSaleLog($saleId, 16, "商品：".$sale['number'].",商品ID：".$saleId." 已删除", serialize($sale));//记录日志
        $this->commit('income');
        return 0;
    }

    //获得专利信息
    public function getPatentByNumber($number)
    {
        $r['eq'] = array(
            'number' => $number,
        );
        return $this->import('patent')->find($r);
    }
}
?>