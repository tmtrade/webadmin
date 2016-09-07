<?
/**
* 出售数据分析管理
*
* @package	Module
* @author	Far
* @since	2016-09-07
*/
class DataAnalyzeModule extends AppModule
{
    public $models = array(
       'history'           => 'saleHistory',
    );
    
    /**
     * 获取交易数据分析数据
     * @param type $type 类型
     * @param type $dateStart
     * @param type $dateEnd
     * 
     */
    public function dataAnalyze($dateStart,$dateEnd){
        $r = array();
        $r['limit'] = 1000;
        $r['raw'] = ' 1 ';
        
        $r['eq']['type'] = 1;
        if ( !empty($dateStart) ){
            $r['raw'] .= " AND date >= ".$dateStart;
        }
        if ( !empty($dateEnd) ){
            $r['raw'] .= " AND date <= ".$dateEnd;
        }
        $r['order'] = array('date'=>'asc');
        $res = $this->import('history')->findAll($r);
        $list = $res['rows'];
        
        //组装所需数据
        foreach ($list as $k => &$v) {
            $info = unserialize($v['data']);
            //价格
            $v['price'] = $info['income']['price']?$info['income']['price']:0;
            
            //是否包装
            if(empty($info['tminfo']['embellish'])){
                $v['tminfo'] = 2;
            }else{
                $v['tminfo'] = 1;
            }
            //来源
            foreach ($info['contact'] as $val){
                if($info['income']['cid']==$val['id']){
                    $v['source'] = $val['source'];
                }
            }
            //所属分类
            $v['class'] = $info['class'];
        }
        return $list;
    }
}
?>