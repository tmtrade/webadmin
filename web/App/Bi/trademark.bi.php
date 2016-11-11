<?
/**
 * 用户中心接口调用
 *
 * 推送商品价格变化数据、
 *
 * @package    Bi
 * @author    Xuni
 * @since    2015-11-05
 */
class TrademarkBi extends Bi
{
    /**
     * 接口标识
     */
    public $apiId = 5;

    protected $user = 'api1090';
    
    public function getTmAll($number, $isInfo=1, $isProposer=1, $isSecond=1, $isImage=1)
    {
        $data = array(
            'number'        => $number,
            'isInfo'        => $isInfo,
            'isProposer'    => $isProposer,
            'isSecond'      => $isSecond,
            'isImage'       => $isImage,
        );
        $params = array(
            'user'    	=> $this->user,
            'method'    => 'getTmAll',
            'data'  	=> $data,
        );
        
        $params['sign'] = sign($params);
        return $this->request("openapi/request", $params);
    }

    protected function sign($data)
    {
        ksort($data, SORT_STRING);
        $userList = C('OPENAPI_KEYS');
        $apiKey = $userList[$this->user];
        $sign   = md5( md5(serialize($data)).$apiKey );
        return $sign;
    }


}
?>