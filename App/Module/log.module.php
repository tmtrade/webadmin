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
        'patentLog'   => 'patentLog',
        'api'       => 'apiLog',
    );

    //添加国内商标出售日志
    public function addSaleLog($saleId, $type=0, $memo='', $desc='')
    {
        if ( empty($saleId) ) return false;

        if ( empty($this->userId) ){
            $member = array(
                'roleId'    => 0,
                'name'      => '系统（接口）',
                );
        }else{
            $member = $this->load('member')->getMemberById($this->userId);
        }
        if ( empty($desc) ){
            $desc  = serialize( $this->load('internal')->getSaleInfo($saleId) );
        }
        $_data = array(
            'saleId'    => $saleId,
            'roleId'    => $member['roleId'],
            'type'      => $type,
            'name'      => $member['name'],
            'date'      => time(),
            'memo'      => $memo,
            'data'      => $desc,
            );

        return $this->import('saleLog')->create($_data);
    }

    /**
     * 获取出售信息日志列表
     * @author      Xuni
     * @since       2016-03-01
     * 
     * @access      public
     * @param       int     $patentId     出售信息ID
     * @return      array
     */
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
            $list[$k]['typeName']   = $opType[$v['type']];
            if ( $v['roleId'] == 0 ) continue;
            $role   = $this->load('role')->getRoleById($v['roleId']);
            $list[$k]['roleName']   = $role['name'];
        }
        return $list;
    }
    
    //添加专利出售日志
    public function addPatentLog($patentId, $type=0, $memo='', $desc='')
    {
        if ( empty($patentId) ) return false;

        if ( empty($this->userId) ){
            $member = array(
                'roleId'    => 0,
                'name'      => '系统（接口）',
                );
        }else{
            $member = $this->load('member')->getMemberById($this->userId);
        }
        if ( empty($desc) ){
            $desc  = serialize( $this->load('patent')->getPatentInfo($patentId) );
        }
        $_data = array(
            'patentId'    => $patentId,
            'roleId'    => $member['roleId'],
            'type'      => $type,
            'name'      => $member['name'],
            'date'      => time(),
            'memo'      => $memo,
            'data'      => $desc,
            );

        return $this->import('patentLog')->create($_data);
    }

    /**
     * 获取专利出售信息日志列表
     * @author      Far
     * @since       2016-04-28
     * 
     * @access      public
     * @param       int     $patentId     出售信息ID
     * @return      array
     */
    public function getPatentLog($patentId)
    {
        if ( empty($patentId) ) return false;
        $r['eq']    = array('patentId'=>$patentId);
        $r['limit'] = 100;
        $r['order'] = array('date'=>'desc');
        $list = $this->import('patentLog')->find($r);
        if ( empty($list) ) array();
        $opType = C("OP_TYPE");
        foreach ($list as $k => $v) {
            $list[$k]['typeName']   = $opType[$v['type']];
            if ( $v['roleId'] == 0 ) continue;
            $role   = $this->load('role')->getRoleById($v['roleId']);
            $list[$k]['roleName']   = $role['name'];
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