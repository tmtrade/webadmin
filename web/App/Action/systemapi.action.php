<?
/**
 * 对内接口
 *
 * 商标出售接口、xxx
 *
 * @package     Action
 * @author      Xuni
 * @since       2016-03-01
 */
class SystemApiAction extends RpcServer
{
	protected $msg;//返回信息
	protected $users;//接口用户
    protected $keys;//接口用户
    //来源定义
    protected $sourceList = array(
        '1'     => 1,   //用户中心
        '2'     => 2,   //一只蝉
        '3'     => 3,   //其他
        '4'     => 4,   //出售者平台
        '5'     => 5,   //移动端
        '6'     => 6,   //报价单
        );

	public function index($params)
	{
        
	}

	/**
     * 添加出售信息
     *
     * @author		xuni
     * @since		2016-03-01
     *
     * @access		public
	 * @param		array	$params		接口参数（参考接口文档）
     * @return		array
     */
    public function addSale($params)
    {
        $type   = 1;
        $check  = $this->checkout($params, $type);
        if ( $check !== true ) return $check;

        $data = $this->formattData($params['data'], $type);

        //商标号不能为空
        if ( empty($data['number']) ){
            return $this->getMsg('101', $params, $type);
        }
        //联系电话不能为空
        if ( empty($data['phone']) ){
            return $this->getMsg('102', $params, $type);
        }
        //联系人不能为空
        if ( empty($data['contact']) ){
            return $this->getMsg('103', $params, $type);
        }
        //底价不能小于0（等于0为议价）
        if ( $data['price'] < 0 ){
            return $this->getMsg('104', $params, $type);
        }
        //来源是否正确（1：用户中心，2：一只蝉，3：其他）
        if ( !in_array($data['source'], $this->sourceList) ){
            return $this->getMsg('105', $params, $type);
        }
        //出售状态是否正确
        if ( !in_array($data['type'], array(1,2,3)) ){
            return $this->getMsg('106', $params, $type);
        }

        $flag = $this->load('api')->addSale($data);
        return $this->getMsg($flag, $params, $type);
    }

    /**
     * 修改联系人价格
     *
     * @author      xuni
     * @since       2016-03-02
     *
     * @access      public
     * @param       array   $params     接口参数（参考接口文档）
     * @return      array
     */
    public function updateContactPrice($params)
    {
        $type   = 2;
        $check  = $this->checkout($params, $type);
        if ( $check !== true ) return $check;

        $data = $this->formattData($params['data'], $type);

        //ID不能小于0
        if ( $data['cid'] <= 0 ){
            return $this->getMsg('110', $params, $type);
        }
        //参数不能小于0
        if ( $data['price'] <= 0 ){
            return $this->getMsg('111', $params, $type);
        }

        $flag = $this->load('api')->updateContactPrice($data);
        return $this->getMsg($flag, $params, $type);
    }

    /**
     * 取消联系人出售信息
     *
     * @author      xuni
     * @since       2016-04-01
     *
     * @access      public
     * @param       array   $params     接口参数（参考接口文档）
     * @return      array
     */
    public function cancelContact($params)
    {
        $type   = 3;
        $check  = $this->checkout($params, $type);
        if ( $check !== true ) return $check;

        $data = $this->formattData($params['data'], $type);

        //ID不能小于0
        if ( $data['uid'] <= 0 ){
            return $this->getMsg('108', $params, $type);
        }
        //商标号不能为空
        if ( empty($data['number']) ){
            return $this->getMsg('101', $params, $type);
        }

        $flag = $this->load('api')->cancelContact($data);
        return $this->getMsg($flag, $params, $type);
    }

	/**
     * 判断数据的正确性
     *
     * @author		xuni
     * @since		2016-03-01
     *
     * @access		public
	 * @param		array	$params		接口参数（参考接口文档）
	 * @param		int		$type		数据类型（1添加官文、2删除官文、11添加流程）
     * @return		array
     */
    protected function checkout($params, $type)
    {
    	//TODO 判断调用的IP地址是否为内网IP
        $this->users    = C('API_USERS');
        //TODO 判断调用的IP地址是否为内网IP
        $this->keys     = C('API_KEYS');
        $this->msg 		= $this->messageList();

    	$user = empty($params['user']) ? '' : $params['user'];
    	$sign = empty($params['sign']) ? '' : $params['sign'];
    	$data = empty($params['data']) ? array() : $params['data'];

        //判断用户是否正确
    	if ( empty($user) ) return $this->getMsg('901', $params, $type);
    	if ( !in_array($user, $this->users) )  return $this->getMsg('110', $params, $type);
        //判断签名是否正确
        if ( empty($sign) ) return $this->getMsg('902', $params, $type);
        if ( $sign != $this->sign($data, $user) ) return $this->getMsg('902', $params, $type);
        //判断数据是否正确
        if ( empty($data) ) return $this->getMsg('903', $params, $type);

        return true;
    }

    /**
     * 格式化数据
     *
     * @author      xuni
     * @since       2016-03-01
     *
     * @access      public
     * @param       array   $data       接口参数（参考接口文档）
     * @param       int     $type       数据类型（1添加出售信息、2修改出售价格）
     * @return      array
     */
    protected function formattData($data, $_type)
    {
        $this->input    = $data;
        $cid            = $this->getParam('cid', 'int');//cid，联系人信息ID
        $uid            = $this->getParam('uid', 'int');//userId或者uid （一般情况为uid）
        $number         = $this->getParam('number', 'string');//商标号
        $phone          = $this->getParam('phone', 'string');//联系电话
        $contact        = $this->getParam('contact', 'string');//联系人
        $price          = $this->getParam('price', 'int');//价格，底价
        $source         = $this->getParam('source', 'int');//来源
        $type           = $this->getParam('type', 'int');//出售类型（1：出售，2：许可，3：出售+许可）

        $array = array();
        if ($_type == 1){
            $array = array(
                'uid'           => $uid,
                'number'        => $number,
                'phone'         => $phone,
                'contact'       => $contact,
                'price'         => $price,
                'type'          => $type,
                'source'        => $source,
                );
        }elseif($_type == 2){
            $array = array(
                'cid'           => $cid,
                'price'         => $price,
                );
        }elseif($_type == 3){
            $array = array(
                'uid'           => $uid,
                'number'        => $number,
                );
        }

        return $array;
    }

	/**
     * 获取相应返回信息
     *
     * @author		xuni
     * @since		2016-03-01
     *
     * @access		public
	 * @param		int		$msgNo		信息编号
	 * @param		array	$params		接口参数（参考接口文档）
	 * @param		int		$type		接口类型
     * @return		array
     */
    protected function getMsg($msgNo, $param, $type)
    {
    	$msg = $this->msg[$msgNo];
    	$status = $msgNo == '999' ? 1 : 2;
    	//添加日志记录
    	$this->load('log')->addApiLog($param, $type, $status, $msg['msg'], $msg);
    	return $msg;
    }

    /**
     * sign签名
     *
     * @todo      	对数据进行签名，保证数据完整性
     * @return    	string
     * @author    	Xuni
     * @copyright 	CHOFN
     * @since    	2016-03-01
     */
    protected function sign($data, $user)
    {
        ksort($data, SORT_STRING);
        $apiKey = $this->keys[$user] ? $this->keys[$user] : 'nobody';
        $sign   = md5( md5(serialize($data)).$apiKey );
        return $sign;
    }

    /**
     * 获取所有返回值
     *
     * @author      xuni
     * @since       2016-03-01
     *
     * @access      private
     * @return      array
     */
    private function messageList()
    {
        $list = array( 
            '999' => array(
                        'code'  => '999',
                        'msg'   => 'success',
                        ),
            '901' => array(
                        'code'  => '901',
                        'msg'   => 'user error',
                        ),
            '902' => array(
                        'code'  => '902',
                        'msg'   => 'sign error',
                        ),
            '903' => array(
                        'code'  => '903',
                        'msg'   => 'data error',
                        ),
            '904' => array(
                        'code'  => '904',
                        'msg'   => 'database error',
                        ),

            '101' => array(
                        'code'  => '101',
                        'msg'   => 'number is null',
                        ),
            '102' => array(
                        'code'  => '102',
                        'msg'   => 'phone is null',
                        ),
            '103' => array(
                        'code'  => '103',
                        'msg'   => 'contact is null',
                        ),
            '104' => array(
                        'code'  => '104',
                        'msg'   => 'price error',
                        ),
            '105' => array(
                        'code'  => '105',
                        'msg'   => 'source error',
                        ),
            '106' => array(
                        'code'  => '106',
                        'msg'   => 'sale type error',
                        ),
            '107' => array(
                        'code'  => '107',
                        'msg'   => 'trademark is null',
                        ),
            '108' => array(
                        'code'  => '108',
                        'msg'   => 'uid error',
                        ),
            '109' => array(
                        'code'  => '109',
                        'msg'   => 'contact info exist', 
                        ),

            '110' => array(
                        'code'  => '110',
                        'msg'   => 'cid error',
                        ),
            '111' => array(
                        'code'  => '111',
                        'msg'   => 'price error',
                        ),
            '112' => array(
                        'code'  => '112',
                        'msg'   => 'contact info not exist',
                        ),
	        '113' => array(
                        'code'  => '113',
                        'msg'   => 'trademark invalid',
                        ),
        );
        return $list;
    }

}
?>