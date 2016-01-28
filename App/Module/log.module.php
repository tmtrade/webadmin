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
        'saleLog'		=> 'saleLog',
    );

    //添加国内商标出售日志
    public function addSaleLog($saleId, $type=0, $memo='')
    {
        if ( empty($saleId) ) return false;
        if ( empty($this->userId) ) $this->userId = 0;

        $member = $this->load('member')->getMemberById($this->userId);
        $_data = array(
            'saleId'    => $saleId,
            'roleId'    => $member['roleId'],
            'type'      => $type,
            'name'      => $member['name'],
            'date'      => time(),
            'memo'      => $memo,
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

}
?>