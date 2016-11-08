<?
/**
 * 报价单操作(对内)
 * 
 * @access	public
 * @package bi
 * @author	dower
 * @since	2016-09-13
 */
class QuotationBi extends Bi
{
    /**
     * 对外接口域名编号
     */
    public $apiId = 4;

    //删除报价单
    public function removeQuotation($data)
    {
	    $params = array(
		    'user' => '1',
		    'sign' => $this->sign($data),
		    'data' => $data,
		    );
	    return $this->request("quotationapi/removeQuotation/", $params, 3);
    }

    function sign($data)
    {
        ksort($data, SORT_STRING);
        $apiKey = 'wojiusuibianxiexie';
        $sign   = md5( md5(serialize($data)).$apiKey );
        return $sign;
    }


}
?>