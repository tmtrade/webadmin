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
        }else{
            $r['raw'] .= " AND isUse != 3";
        }
        
        if ( !empty($params['pages']) ){
             $r['eq']['pages']    = $params['pages'];
        }
        if ( !empty($params['phone']) ){
             $r['eq']['phone']    = $params['phone'];
        }
        if ( !empty($params['dateStart']) ){
            $r['raw'] .= " AND unix_timestamp(date) >= ".strtotime($params['dateStart']);
        }
        if ( !empty($params['dateEnd']) ){
            $r['raw'] .= " AND unix_timestamp(date) < ".(strtotime($params['dateEnd'])+24*3600);
        }
        $r['order'] = array('date' => 'desc');
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
        $res = $this->import('exchange')->modify($data, $r);
        if ( !$res ) return false;
        return true;
    }
	
	
    //获取广告的待审核信息
    public function getExchangeInfo($id)
    {    
         $r['eq']    = array('id'=>$id,"isUse"=>3);
         $r['col'] = array("uid","pages","module","sort","phone","date");
         $res = $this->import('exchange')->find($r);
         return $res;
		
    }
    
    //确认通过后添加一条草稿广告，并修改状态为通过审核
    public function addAd($data,$id)
    {   
        $adCount = $this->load('ad')->getPagesCount($data['pages'], $data['module'], $data['sort']);
        if($adCount>=2){//每个位置的广告最多两条
            return false;
        }
        $data['note'] = "客户".$data['phone']."的自动草稿";
        $data['startdate'] = strtotime(date("Y-m-10",strtotime($data['date']."+1 month")));
        $data['enddate'] = strtotime(date("Y-m-10",strtotime($data['date']."+2 month")));
        unset($data['phone']);
        unset($data['uid']);
        unset($data['date']);
        $this->begin('exchange');
        $res = $this->import('ad')->create($data);
        if($res){
            $r['eq']    = array('id'=>$id);
            $datas       = array('isUse'=>1);
            $eres = $this->import('exchange')->modify($datas, $r);
            $this->commit('exchange');
            return $eres;
        }
        $this->rollBack('exchange');
        return false;
    }

}
?>