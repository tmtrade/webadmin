<?
/**
* 广告管理
*
* 广告创建，修改，删除等
*
* @package	Module
* @author	Far
* @since	2016-06-07
*/
class AdModule extends AppModule
{
    public $models = array(
        'ad'		=> 'ad',
        'exchange'	=> 'exchange',
    );
    
    /**
     * 广告兑换列表
     * 
     * @author  Far
     * @since   2016-05-23
     * @access  public
     * @return  string      列表数据
     */
    public function getAdList($params,$page, $limit)
    { 
        $r = array();
        $r['raw']  = 1;
        
        if ( !empty($params['pages']) ){
             $r['eq']['pages']    = $params['pages'];
        }
        if ( !empty($params['module']) ){
             $r['eq']['module']    = $params['module'];
        }
        $r['col'] = array("id","pages","module","sort","count(1) as counts","max(enddate) as enddate","max(isUse) as isUse");
        $r['group'] = array('sort' => 'asc');
        $r['order'] = array('sort' => 'asc');
        $r['page']  = $page;
        $r['limit'] = $limit;
        $res = $this->import('ad')->findAll($r);
        return $res;
    }
    
    /**
     * 获取页面位置的所有广告
     * @param int $pages
     * @param int $module
     * @param int $sort
     */
    public function getPagesList($pages, $module, $sort){
        
        $r['eq']['pages']       = $pages;
        $r['eq']['sort']        = $sort;
        if ( !empty($params['module']) ){
             $r['eq']['module']    = $module;
        }
        $r['order'] = array('enddate' => 'asc');
        $r['limit'] = 2;
        $res = $this->import('ad')->findAll($r);
        return $res;
    }
    
    /**
     * 获取页面位置的所有广告的个数
     * @param int $pages
     * @param int $module
     * @param int $sort
     */
    public function getPagesCount($pages, $module, $sort){
        
        $r['eq']['pages']       = $pages;
        $r['eq']['sort']        = $sort;
        if ( !empty($params['module']) ){
             $r['eq']['module']    = $module;
        }
        $res = $this->import('ad')->count($r);
        return $res;
    }
    
    //修改基本设置
    public function setAd($data, $id)
    {
        if ( empty($data) || empty($id) ) return false;

        $r['eq'] = array('id'=>$id);

        return $this->import('ad')->modify($data, $r);
    }
    
    //删除广告
    public function delAd($id)
    {
        $r = array();
        if ( empty($id) ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('ad')->remove($r);
    }
    
    
    //删除过期广告
    public function delPastAd()
    {
	$r = array();
	$r['raw'] = "enddate <=".time();
        $list = $this->import('ad')->findAll($r);
	
	$res = $this->import('ad')->remove($r);
	if($res){
	    Log::write(serialize($list['rows']), date('Y-m-d').'-cronjob-ad.log');
	    return true;
	}else{
	    return false;
	}
	
	
    }
}
?>