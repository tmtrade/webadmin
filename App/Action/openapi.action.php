<?
/**
 * 公共RPC类
 *
 * @author    Xuni
 * @copyright CHOFN
 */
class OpenApiAction extends RpcServer {

    protected $msg;//返回信息
    protected $users;//接口用户
    protected $key;//接口签名key

    public function index()
    {
        
    }

    /**
     *  对外接口入口
     *
     * @author      xuni
     * @since       2015-08-10
     *
     * @access      public
     * @param       array   $params     接口参数（参考接口文档）
     * @return      array
     */
    public function request($params=array())
    {
        //TODO 判断调用的IP地址是否为内网IP
        $this->keys     = C('OPENAPI_KEYS');
        $this->users    = C('OPENAPI_USERS');
        $this->msg      = $this->msgConfig();

        $this->input    = $params;
        $user           = $this->getParam('user', 'string');
        $sign           = $this->getParam('sign', 'string');
        $data           = $this->getParam('data', 'array');
        $method         = $this->getParam('method', 'string');

        //判断用户是否正确
        if ( empty($user) ) return $this->getMsg('201');
        if ( !in_array($user, $this->users) )  return $this->getMsg('201');
        //判断签名是否正确
        if ( empty($sign) ) return $this->getMsg('202');
        if ( $sign != $this->sign($params, $user) ) return $this->getMsg('202');
        //判断数据是否正确
        if ( empty($data) ) return $this->getMsg('203');
        //判断方法是否正确
        if ( empty($method) ) return $this->getMsg('204');
        if ( !method_exists($this, $method) ) return $this->getMsg('204');

        $res = $this->$method($data);
        return $res;
    }

    /**
     * 获取推荐商标信息
     *
     * @author      xuni
     * @since       2015-08-10
     *
     * @access      public
     * @param       array   $params     接口参数（参考接口文档）
     * @return      array
     */
    protected function getBanner($params)
    {
        $msg    = $this->getMsg(101);
        $class  = $params['class'];
        $limit  = $params['limit'];

        if ( empty($class) || $class <= 0 ) return $msg;
        $limit = ($limit < 1 || $limit > 20) ? 4 : $limit;

        $_data = array(
            'class' => $class,
            'limit' => $limit,
            );
        $data   = $this->load('openapi')->getRandSale( $_data );
        $msg['data'] = $data;
        return $msg;
    }

    /**
     * 获取商品包装图片
     *
     * @author      xuni
     * @since       2016-07-04
     *
     * @access      public
     * @param       array   $params     接口参数（参考接口文档）
     * @return      array
     */
    protected function getSaleImg($params)
    {
        $msg    = $this->getMsg(101);
        $number = $params['number'];

        if ( empty($number) ) return $msg;

        $data   = $this->load('openapi')->getSaleImg( $number );
        $msg['data'] = $data;
        return $msg;
    }


    /**
     * 获取相应返回信息
     *
     * @author      xuni
     * @since       2015-06-20
     *
     * @access      public
     * @param       int     $msgNo      信息编号
     * @return      array
     */
    protected function getMsg($msgNo)
    {
        $msg = $this->msg[$msgNo];
        return $msg;
    }

    /**
     * 返回信息
     *
     * @author      xuni
     * @since       2015-08-10
     * @return      array
     */
    protected function msgConfig()
    {
        $msg = array( 
            '101' => array(
                'code'  => '101',
                'msg'   => 'success',
                'data'  => array(),
                ),
            '201' => array(
                'code'  => '201',
                'msg'   => 'user error',
                'data'  => array(),
                ),
            '202' => array(
                'code'  => '202',
                'msg'   => 'sign error',
                'data'  => array(),
                ),
            '203' => array(
                'code'  => '203',
                'msg'   => 'data error',
                'data'  => array(),
                ),
            '204' => array(
                'code'  => '204',
                'msg'   => 'method error',
                'data'  => array(),
                ),
            '301' => array(
                'code'  => '301',
                'msg'   => 'database error',
                'data'  => array(),
                ),
        );
        return $msg;
    }

    /**
     * sign签名
     *
     * @todo        对数据进行签名，保证数据完整性
     * @return      string
     * @author      Xuni
     * @copyright   CHOFN
     * @since       2015-08-10
     */
    protected function sign($data, $user)
    {
        unset($data['sign']);
        ksort($data, SORT_STRING);
        $apiKey = $this->keys[$user] ? $this->keys[$user] : 'nobody';
        $sign   = md5( md5(serialize($data)).$apiKey );
        return $sign;
    }


}