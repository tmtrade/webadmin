<?
/**
 * API组件
 *
 * 处理API接口方法
 * 
 * @package	Module
 * @author	Xuni
 * @since	2016-03-01
 */
class ApiModule extends AppModule
{   
    /**
     * 来源对应值
     */
    private $sourceList = array(
        '1'     => '11',//用户中心
        '2'     => '10',//一只蝉
        '3'     => '3',//其他
        '4'     => '12',//出售者平台
        );

	/**
	 * 添加出售信息
	 * @author	Xuni
	 * @since	2016-03-01
	 * @param	array		$param		接口数据包
	 * @return	boolean
	 */
	public function addSale($params)
	{
        //获取对应来源值，没有则为3：其他
        $source = $this->sourceList[$params['source']] ? $this->sourceList[$params['source']] : 3;
        $number = $params['number'];
        $memo   = $params['memo']?$params['memo']:'添加出售信息接口默认创建商品';
        //判断商标号是否正确
        $info = $this->load('trademark')->getInfo($number, array('id','auto as `tid`'));
        if ( empty($info['id']) ) return '107';
        //判断是否出售
        $saleId = $this->load('internal')->existSale($number);
        if ( $saleId ){
            //$sale = $this->load('internal')->getSaleInfo($saleId);
        }else{
            $saleId = $this->load('internal')->addDefault($number, $memo);
        }
	
        //如果商标出售状态在“销售中”，直接审核通过
        $isVerify = $this->load('internal')->isSaleUp($saleId) ? 1 : 2;
	
        //如果是用户中心来的数据，需要使用uid判断重复
        if ( $source == 11 || $source == 12 ){
            if ( $params['uid'] <= 0 ) return '108';
            $isHas = $this->load('internal')->existContact($number, '', $params['uid']);
	    $this->load('total')->updatePassCount($params['uid'], 1);//增加通过记录数
        }else{
            $isHas = $this->load('internal')->existContact($number, $params['phone']);
        }
        if ( $isHas ) return '109';//如果已经存在，直接返回正确

        $contact = array(
            'source'        => $source,
            'uid'           => $params['uid'],
            'tid'           => $info['tid'],
            'number'        => $number,
            'phone'         => $params['phone'],
            'name'          => $params['contact'],
            'price'         => $params['price'],
            'saleType'      => $params['type'],
            'isVerify'      => $isVerify,
            'date'          => time(),
            'memo'          => $memo,
            );

        $flag = $this->load('internal')->addContact($contact, $saleId);
        if ( $flag ){
            $_memo = "来自接口，联系人ID:$flag(新增)".($isVerify == 1 ? '(审核通过)' : '(待审核)');
            $this->load('log')->addSaleLog($saleId, 8, $_memo, serialize($contact));//记录日志
            return '999';//成功
        }
        return '904';//添加失败
	}

    /**
     * 修正联系人价格
     * @author  Xuni
     * @since   2016-03-01
     * @param   array       $param      接口数据包
     * @return  boolean
     */
    public function updateContactPrice($params)
    {
        $cid    = $params['cid'];
        $price  = $params['price'];

        $r['eq'] = array('id'=>$cid);
        $contact = $this->load('internal')->findContact($r);
        if ( empty($contact) ){
            return '112';
        }
        if ( $contact['price'] == $price ){
            return '999';
        }

        $data = array(
            'price'     => $price,
            'isVerify'  => 2,
            );        
        $res = $this->load('internal')->updateContact($data, $cid);
        if ( $res ){
            $this->load('log')->addSaleLog($contact['saleId'], 9, "来自接口，联系人ID:$cid(修改价格)", serialize($contact));//记录日志
            return '999';
        }
        return '904';
    }

    /**
     * 取消联系人出售信息
     * @author  Xuni
     * @since   2016-04-01
     * @param   array       $param      接口数据包
     * @return  boolean
     */
    public function cancelContact($params)
    {
        $uid    = $params['uid'];
        $number = $params['number'];

        $r['eq'] = array('uid'=>$uid,'number'=>$number);
        $contact = $this->load('internal')->findContact($r);

        if ( empty($contact) ){
            return '112';
        }

        $id     = $contact['id'];
        $saleId = $contact['saleId'];
        $isUp   = $this->load('internal')->isSaleUp($saleId);
        if ( $isUp ){
            $r['eq']['isVerify']    = 1;
        }
        $r['eq']['saleId']  = $saleId;
        $r['raw']           = " id != $id ";
        $r['col']           = array('id');
        $r['limit']         = 100;
        $list   = $this->load('internal')->findContact($r);
        $ids    = array_filter(arrayColumn($list, 'id'));
        if ( count($ids) < 1 ) {
            //只有一个联系人时，先下架，再删除
            $reason = '前台用户取消出售，系统自动下架商品并删除联系人';
            $memo   = '用户自行取消报价';
            $flag   = $this->load('internal')->saleDown($saleId, $reason);
            if ( !$flag ) return '904';

            $res = $this->load('internal')->_delContact($id, $contact, 4, $memo);
        }else{
            $memo   = '用户自行取消报价';
            //用户中心用户自行取消
            $res = $this->load('internal')->delContact($id, $saleId, 4, $memo);
        }

        if ( $res ){
            $this->load('log')->addSaleLog($saleId, 13, "联系人ID：$id 被删除了（ 来自接口uid:$uid ）", serialize($contact));//删除联系人
            return '999';
        }
        return '904';
    }


}
?>