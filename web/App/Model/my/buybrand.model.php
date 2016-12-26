<?
/**
* 
* 商标购买
* 
* @package	Module
* @author	haydn
* @since	2016-01-18
*/
class BuyBrandModel extends AppModel
{
    /**
    * 记录商标购买
    * 
    * @access  public
    * @author  haydn
    * @since   2016-03-11
    * @param   array		$array  数据包
    * @return  int        	$id
    */
    public function add($array)
    {
        $data['userId']     = $array['userId'];
        $data['mobile']     = $array['mobile'];
        $data['tid']     	= empty($array['tid']) ? '0' : $array['tid'];
        $data['trademark']  = empty($array['trademark']) ? '' : $array['trademark'];
        $data['class']    	= empty($array['class']) ? '0' : $array['class'];
        $data['remarks']    = $array['remarks'];
        $data['name']    	= !empty($array['name']) ? $array['name'] : '';
        $data['aid']    	= $array['aid'];
        $data['subject']    = $array['subject'];
        $data['recorddate'] = TIME;
        $data['recordip']   = getClientIp();
        $id                 = $this->create($data);
        return $id;
    }

    /**
     * 获取用户添加的数量
     * @author  haydn
     * @since   2016-03-11
     * @param   string||int	$account||$uid  手机或用户id
     * @param   int    		$trademark   	商标号
     * @param   int        	$class   		商标类型
     * @param   string      $field   		检查字段
     * @return  bool
     */
    public function getBuyCount($account,$trademark,$class,$field = 'userId')
    {
		$r['eq']	= array($field => $account,'trademark' => $trademark,'class' => $class);
		$data		= $this->findAll($r);
		$count		= $data['total'];
		return $count;
    }
}
?>