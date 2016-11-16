<?
/**
* 商标相关信息
*
* 查询商标基础信息、图片、状态等
*
* @package	Module
* @author	Xuni
* @since	2015-10-22
*/
class SyncModule extends AppModule
{
    public $models = array(
        'sale'		=> 'sale',
        'log'       => 'systemlog',
    );

    public function getSyncList($page, $limit)
    {
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['col']   = array('number');

        return $this->import('sale')->findAll($r);
    }

    public function getSyncFaild($limit)
    {
        $r['eq']    = array('status'=>2);
        $r['order'] = array('created'=>'desc');
        $r['limit'] = $limit;

        return $this->import('log')->find($r);
    }

    public function updateLog($id)
    {
        if ( empty($id) ) return false;

        $r['eq']    = array('id'=>$id);
        $data       = array('status'=>1);

        return $this->import('log')->modify($data, $r);
    }

}
?>