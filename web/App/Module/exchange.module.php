<?
/**
* 兑换广告管理
*
* 兑换创建，修改，删除等
*
* @package	Module
* @author	Far
* @since	2016-06-07
*/
class ExchangeModule extends AppModule
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
    public function getExchangeList($params,$page, $limit)
    { 
        $r = array();
        $r['raw']  = 1;
        if($params['isUse']==3){
            $r['eq']['isUse']    = $params['isUse'];
	    $r['order'] = array('date' => 'asc');
        }else{
            $r['raw'] .= " AND isUse != 3";
	    $r['order'] = array('date' => 'desc');
        }
        
        if ( !empty($params['pages']) && $params['isUse']!=3){
             $r['eq']['pages']    = $params['pages'];
        }
        if ( !empty($params['phone']) && $params['isUse']!=3){
             $r['eq']['phone']    = $params['phone'];
        }
        if ( !empty($params['dateStart']) && $params['isUse']!=3){
            $r['raw'] .= " AND unix_timestamp(date) >= ".strtotime($params['dateStart']);
        }
        if ( !empty($params['dateEnd']) && $params['isUse']!=3){
            $r['raw'] .= " AND unix_timestamp(date) < ".(strtotime($params['dateEnd'])+24*3600);
        }
        $r['page']  = $page;
        $r['limit'] = $limit;
        $res = $this->import('exchange')->findAll($r);
        return $res;
    }

    //未通过审核
    public function setCancel($id, $reason)
    {
        if ( empty($id) && empty($reason) ) return false;

        $r['eq']    = array('id'=>$id);
        $data       = array('isUse'=>2,'reason'=>$reason);
	$this->begin('exchange');
        $res = $this->import('exchange')->modify($data, $r);
	if($res){
	    $info = $this->getInfoById($id);
	    $result = $this->load("total")->upTotal($info["uid"],1,$info['amount'],"广告兑换驳回");
	    if($result){
		$this->commit('exchange');
		return $result;
	    }else{
		$this->rollBack('exchange');
	    }
	}
        return $res;
    }	
	
    //获取广告的信息
    public function getExchangeInfo($id)
    {    
         $r['eq']    = array('id'=>$id,"isUse"=>3);
         $r['col'] = array("uid","pages","phone","amount","date");
         $res = $this->import('exchange')->find($r);
         return $res;
    }
    
    //获取广告的待审核信息
    public function getInfoById($id)
    {    
         $r['eq']    = array('id'=>$id);
         $r['col'] = array("uid","pages","phone","amount","date");
         $res = $this->import('exchange')->find($r);
         return $res;
    }
    
    //确认通过后添加一条草稿广告，并修改状态为通过审核
    public function addAd($data,$id)
    {
	if($data['pages']!=4 && $data['pages']!=5){
	    $data['startdate'] = strtotime(date("Y-m-10",strtotime($data['date']."+1 month")));
	    $data['enddate'] = strtotime(date("Y-m-10",strtotime($data['date']."+2 month")));
	    $sort = $this->getMaxSort($data['pages'], $data['startdate'], $data['enddate']);
	    $data['sort'] = $sort+1;
	    if($data['pages']==2){ //通栏菜单根据排序获取属于哪个菜单的广告
		$m = C("AD_MODULE_TYPE");
		foreach ($m as $k=>$v){
		    if(in_array($data['sort'], $v)){
			$data['module'] = $k;
			break;
		    }
		}
	    }

	    $adCount = $this->load('ad')->getPagesCount($data['pages'], $data['module'], $data['sort']);
	    if($adCount>=2){//每个位置的广告最多两条
		return array('code'=>2,'msg'=>'每个位置的广告最多两条');
	    }
	    $data['note'] = "客户".$data['phone']."的自动草稿";
	    unset($data['phone']);
	    unset($data['amount']);
	    unset($data['uid']);
	    unset($data['date']);
	    $this->begin('exchange');

	    $res = $this->import('ad')->create($data);
	    if($res){
		$this->commit('exchange');
	    }
	    $this->rollBack('exchange');
	}
	$r['eq']    = array('id'=>$id);
	$datas       = array('isUse'=>1);
	$result = $this->import('exchange')->modify($datas, $r);
	if($result){
	    return array('code'=>1);
	}else{
	    return array('code'=>2,'msg'=>'操作失败');
	}
    }
    
    
    /**
     * 获取页面广告最大兑换排序值
     * @param array $data
     */
    public function getMaxSort($pages, $start, $end){
	$r['raw'] = " startdate >={$start} and enddate <={$end}";
	$r['col']   = array('max(sort)as sort');
	$r['eq'] = array('pages'=>$pages);
        $res = $this->import('ad')->find($r);
	return $res['sort']?$res['sort']:0;
    }
}
?>