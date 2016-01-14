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
        'sale'		=> 'sale',
        'contact'	=> 'saleContact',
        'tminfo'    => 'saleTminfo',
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
            $r['eq']['name'] = $params['tmName'];
        }
        if ( !empty($params['tmNumber']) ){
            $r['eq']['number'] = $params['tmNumber'];
        }
        if ( !empty($params['saleStatus']) ){
            $r['eq']['status'] = $params['saleStatus'];
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
            $r['raw'] .= " salePriceDate = 0 OR salePriceDate >= unix_timestamp(now()) ";
        }
        if ( !empty($params['isTop']) ){
            $r['eq']['isTop'] = 1;
        }
        $r['order'] = array('date'=>'desc');
        $res = $this->import('sale')->findAll($r);
        return $res;
    }

    //获取商品信息（可选包含的所有联系人与包装信息）
    public function getSaleInfo($saleId, $contact=1, $tminfo=1, $bzxx=1)
    {
        $r['eq'] = array(
            'id' => $saleId,
            );
        $info = $this->import('sale')->find($r);
        if ( empty($info) ) return array();
        //$info['isBlack'] = $this->load('blacklist')->isBlack($info['number']);
        if ( $contact ) $info['contact']    = $this->getSaleContact($saleId);
        if ( $tminfo ) $info['tminfo']      = $this->getSaleTminfo($saleId);
        return $info;
    }

    //通过商品ID获取没有在黑名单中的商标号
    // public function getNoBlackById($ids)
    // {
    //     if ( empty($ids) || !is_array($ids) ) return array();
        
    //     $numbers = array();
    //     foreach ($ids as $id) {
    //         $sale = $this->getSaleInfo($id, 0, 0);
    //         if ( $sale['isBlack'] ) continue;
    //         if ( empty($sale) ) continue;
    //         $numbers[$id] = $sale['number'];
    //     }
    //     return $numbers;
    // }

    //加入黑名单（要走事务，因为有自动下架。批量时，成功一条算一条，但返回值必须都成功才返回TRUE）
    // public function setBlack($saleId, $reason='')
    // {
    //     if ( empty($saleId) ) return false;
    //     if ( !is_array($saleId) ){
    //         return $this->_setBlack($saleId, $reason);
    //     }
    //     //过滤掉已经是黑名单的数据
    //     $list = $this->load('internal')->getNoBlackById($saleId);
    //     if ( empty($list) ) return true;

    //     $flag = true;
    //     foreach ($list as $id => $number) {
    //         $res = $this->_setBlack($id, $reason);
    //         if ( !$res ) $flag = false;
    //     }
    //     return $flag;
    // }

    //单条加入黑名单
    // protected function _setBlack($saleId, $reason)
    // {
    //     if ( $saleId <= 0 ) return false;

    //     $sale = $this->getSaleInfo($saleId, 0, 0);
    //     if ( $sale['isblack'] ) return true;//已经在黑名单中，直接返回

    //     $this->begin('sale');
    //     $res = $this->load('blacklist')->setBlack($sale['number'], $reason);        
    //     if ( !$res ) {
    //         $this->rollBack('sale');
    //         return false;
    //     }
    //     $this->load('log')->addSaleLog($saleId, 6, $reason);//添加黑名单 
    //     //如果是已上架，需要下架
    //     if ( $sale['status'] == 1 ) {
    //         $res = $this->saleDown($saleId, '添加黑名单');
    //         if ( $res ){
    //             return $this->commit('sale');
    //         }
    //         $this->rollBack('sale');
    //         return false;
    //     }
    //     return $this->commit('sale');
    // }

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

    //获取商品ID（saleId）下的商标包装信息
    public function getSaleTminfo($saleId)
    {
        $r['eq'] = array(
            'saleId' => $saleId,
            );
        return $this->import('tminfo')->find($r);
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
    public function addDefault($number)
    {
        if ( empty($number) ) return false;
        if ( $this->existSale($number) ) return false;

        $info   = $this->load('trademark')->getTmInfo($number);
        if ( empty($info) ) return false;

        $other  = $this->load('trademark')->getTmOther($number);
        if ( empty($other) ) return false;

        $class      = implode(',', $info['class']);
        $platform   = implode(',', $other['platform']);
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
            'date'          => time(),
            'hits'          => 0,
            'memo'          => '后台创建默认商品',
        );
        $tminfo = array(
            'number'    => $number,
            'memo'      => '后台创建默认商品',
            'intro'     => '',
        );
        $this->begin('sale');
        $saleId = $this->addSale($sale);//创建商品信息
        $tmId   = $this->addTminfo($tminfo, $saleId);//创建商品包装信息
        $black  = $this->load('blacklist')->setBlack($number);
        if ( $saleId && $tmId && $black ){
            $this->load('log')->addSaleLog($saleId, 3, '后台创建默认商品');//创建商品日志
            $this->commit('sale');
            return $saleId;
        }
        $this->rollBack('sale');
        return false;
    }

    //更新
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

    //更新
    public function updateTminfo($data, $saleId)
    {
        $r['eq'] = array('saleId'=>$saleId);
        return $this->import('tminfo')->modify($data, $r);
    }

    //打包新增出售
    public function addAll($data)
    {
        $sale       = $data['sale'];
        $tminfo     = $data['saleTminfo'];
        $contact    = $data['saleContact'];

        $this->begin('sale');
        $saleId = $this->addSale($sale);
        if ( $saleId <= 0 ) {
            $this->rollBack('sale');
            return false;
        }
        $tminfoId = $this->addTminfo($tminfo, $saleId);
        if ( $tminfoId <= 0 ) {
            $this->rollBack('sale');
            return false;
        }
        $contactId = $this->addContact($contact, $saleId);
        if ( $contactId <= 0 ) {
            $this->rollBack('sale');
            return false;
        }
        return $this->commit('sale');
    }

    //添加出售基础信息
    public function addSale($data)
    {
        if ( $this->existSale($data['number']) ) return false;

        return $this->import('sale')->create($data);
    }

    //添加商标基础信息
    public function addTminfo($data, $saleId)
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
        }else{
            $data['saleId'] = $saleId;
            return $this->import('contact')->create($data);
        }
        
    }

    //删除联系人
    public function delContact($id, $saleId, $type='one')
    {
        if ( empty($id) && empty($saleId) ) return false;

        $r['eq'] = array('saleId'=>$saleId);
        if ( $type == 'all' ){
            return $this->import('contact')->remove($r);
        }
        if ( $saleId ){//如果有商品ID，要判断联系人是否至少有一个
            $total = $this->import('contact')->count($r);
            if ( $total < 2 ) return false;
        }
        $role['eq'] = array('id'=>$id);
        return $this->import('contact')->remove($role);
    }

    //审核联系人
    public function setVerify($id, $saleId)
    {
        if ( empty($id) && empty($saleId) ) return false;

        $r['eq']    = array('id'=>$id);
        $data       = array('isVerify'=>1);
        $res = $this->import('contact')->modify($data, $r);
        if ( !$res ) return false;
        if ( $this->isSaleUp($saleId) )  return true;

        $memo = '联系人审核通过并上架商品';
        return $this->saleUp($saleId, $memo);
    }

    //驳回联系人
    public function delVerify($id, $saleId, $reason)
    {
        if ( empty($id) && empty($reason) && empty($saleId) ) return false;
        
        return $this->delContact($id, $saleId, $reason);
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


    //判断是否商标基础信息是否存在
    public function existContact($phone, $source)
    {
        $r['eq'] = array('phone'=>$phone,'source'=>$source);
        $res = $this->import('contact')->find($r);
        if ( $res ) return true;
        return false;
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

}
?>