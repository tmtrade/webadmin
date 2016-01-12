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

        $res = $this->import('sale')->findAll($r);
        return $res;
    }

    public function getSaleInfo($saleId)
    {
        $r['eq'] = array(
            'id' => $saleId,
            );
        $info = $this->import('sale')->find($r);
        if ( empty($info) ) return array();
        $info['contact'] = $this->getSaleContact($saleId);
        $info['tminfo']  = $this->getSaleTminfo($saleId);
        return $info;
    }


    public function getSaleContact($saleId, $cId=0)
    {
        if ( $cId <= 0 ){
            $r['eq'] = array(
                'saleId' => $saleId,
                );
            $r['limit'] = 20;
        }else{
            $r['eq'] = array(
                'id'        => $cId,
                'saleId'    => $saleId,
                );
            $r['limit'] = 1;
        }

        return $this->import('contact')->find($r);
    }

    public function getSaleTminfo($saleId)
    {
        $r['eq'] = array(
            'saleId' => $saleId,
            );
        return $this->import('tminfo')->find($r);
    }

    //下架商标
    public function saleDown($saleId, $memo='')
    {
        if ( $saleId < 1 ) return false;

        $this->begin('sale');
        $logId = $this->load('log')->addSaleLog($saleId, 2, $memo);//下架日志
        if ( $logId <= 0 ) {
            $this->rollBack('sale');
            return false;
        }
        $r['eq']        = array('id'=>$saleId);
        $data['status'] = 2;

        $res = $this->import('sale')->modify($data, $r);
        if ( $res ) return $this->commit('sale');
        return false;
    }

    //上架商标
    public function saleUp($saleId)
    {
        if ( $saleId < 1 ) return false;

        $this->begin('sale');
        $logId = $this->load('log')->addSaleLog($saleId, 1);//上架日志
        if ( $logId <= 0 ) {
            $this->rollBack('sale');
            return false;
        }
        $r['eq']        = array('id'=>$saleId);
        $data['status'] = 1;
        
        $res = $this->import('sale')->modify($data, $r);
        if ( $res ) return $this->commit('sale');
        return false;
    }

    public function add($data)
    {
        //基础sale
        //add联系人
        //add包装信息
        //add价格信息
        //add其他信息
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

    //判断是否出售基础信息是否存在
    public function existSale($number)
    {
        if ( empty($number) ) return false;
        $r['eq'] = array('number'=>$number);
        $res = $this->import('sale')->find($r);
        if ( empty($res) ) return false;
        return true;
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

    public function countSale()
    {
        return $this->import('sale')->count();
    }

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