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
class ModuleModule extends AppModule
{
    public $models = array(
        'module'		=> 'module',
    );
    
    public function getList($params, $page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        // if ( empty($params) ){
            // $res = $this->import('sale')->findAll($r);
            // return $res;
        // }
        $r['order'] = array('date'=>'desc');
        $res = $this->import('module')->findAll($r);
        return $res;
    }
}
?>