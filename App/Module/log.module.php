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
class LogModule extends AppModule
{
    public $models = array(
        'saleLog'   => 'saleLog',
        'api'       => 'apiLog',
    );

    //添加国内商标出售日志
    public function addSaleLog($saleId, $type=0, $memo='')
    {
        if ( empty($saleId) ) return false;
        if ( empty($this->userId) ) $this->userId = 0;

        $member = $this->load('member')->getMemberById($this->userId);
        $desc   = serialize( $this->load('member')->getSaleInfo($saleId) );
        $_data = array(
            'saleId'    => $saleId,
            'roleId'    => $member['roleId'],
            'type'      => $type,
            'name'      => $member['name'],
            'date'      => time(),
            'memo'      => $memo,
            'data'      => '',
            );

        return $this->import('saleLog')->create($_data);
    }

    public function getSaleLog($saleId)
    {
        if ( empty($saleId) ) return false;
        $r['eq']    = array('saleId'=>$saleId);
        $r['limit'] = 100;
        $r['order'] = array('date'=>'desc');
        $list = $this->import('saleLog')->find($r);
        if ( empty($list) ) array();
        $opType = C("OP_TYPE");
        foreach ($list as $k => $v) {
            if ( $v['roleId'] == 0 ) continue;
            $member = $this->load('member')->getMemberById($this->userId);
            $role   = $this->load('role')->getRoleById($member['roleId']);
            $list[$k]['memberName'] = $member['name'];
            $list[$k]['roleName']   = $role['name'];
            $list[$k]['typeName']   = $opType[$v['type']];
        }
        return $list;
    }

    /**
     * 添加API日志
     * @author      Xuni
     * @since       2016-03-01
     * 
     * @access      public
     * @param       array     $params     数据包
     * @return      void
     */
    public function addApiLog($param, $type, $status, $desc='', $memo='')
    {
        $data = array(
            'user'      => $param['user'],
            'type'      => $type,
            'status'    => $status,
            'data'      => $param['data'],
            'desc'      => $desc,
            'memo'      => $memo,
            );

        return $this->_addApiLog($data);
    }

    /**
     * 获取用户信息
     * @author      Xuni
     * @since       2016-03-01
     *
     * @access      public
     * @param       array     $params     数据包
     * @return      void
     */
    protected function _addApiLog($params)
    {
        $user       = empty($params['user']) ? 0 : $params['user'];
        $msg        = empty($params['desc']) ? '' : $params['desc'];
        $memo       = empty($params['memo']) ? '' : $params['memo'];
        $type       = empty($params['type']) ? 0 : $params['type'];
        $status     = empty($params['status']) ? 0 : $params['status'];
        $data       = empty($params['data']) ? 0 : $params['data'];

        if ( is_array($data) ) $data = serialize($data);
        if ( is_array($memo) ) $memo = serialize($memo);

        $log = array(
            'user'      => $user,
            'type'      => $type,
            'status'    => $status,
            'data'      => $data,
            'created'   => time(),
            'desc'      => $msg,
            'memo'      => $memo,
            );

        return $this->import('api')->create($log);
    }

}
?>