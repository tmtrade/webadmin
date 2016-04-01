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
        $memo   = '添加出售信息接口默认创建商品';
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
        //如果是用户中心来的数据，需要使用uid判断重复
        if ( $source == 11 ){
            if ( $params['uid'] <= 0 ) return '108';
            $isHas = $this->load('internal')->existContact($number, '', $params['uid']);
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
            'isVerify'      => 2,
            'date'          => time(),
            'memo'          => $memo,
            );

        $flag = $this->load('internal')->addContact($contact, $saleId);
        if ( $flag ){
            $this->load('log')->addSaleLog($saleId, 8, "来自用户中心，联系人ID:$flag(新增)", serialize($contact));//记录日志
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
            $this->load('log')->addSaleLog($contact['saleId'], 9, "来自用户中心，联系人ID:$cid(修改价格)", serialize($contact));//记录日志
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

        if ( $this->load('internal')->isSaleUp($saleId) ){
            $r['eq']['isVerify']    = 1;
        }
        $r['eq']['saleId']  = $saleId;
        $r['raw']           = " id != $id ";
        $r['col']           = array('id');
        $r['limit']         = 100;
        $list = $this->load('internal')->findContact($r);
        if ( count($list) < 1 ) {
            //只有一个联系人时，先下架，再删除
            $reason = '前台用户取消出售，下架商品并删除联系人';
            $flag   = $this->load('internal')->saleDown($saleId, $reason);
            if ( !$flag ) return '904';

            $res = $this->load('internal')->_delContact($id, $contact, 4);
        }else{
            //用户中心用户自行取消
            $res = $this->load('internal')->delContact($id, $saleId, 4);
        }

        if ( $res ){
            $this->load('log')->addSaleLog($saleId, 13, "联系人ID：$id 被删除了（ 来自用户中心接口uid:$uid ）", serialize($contact));//删除联系人
            return '999';
        }
        return '904';
    }


}
?>