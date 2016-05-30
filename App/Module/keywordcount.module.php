<?
/**
 * 关键字、搜索地址
 * 
 * 查询、创建
 *
 * @package	Module
 * @author	Xuni
 * @since	2016-04-13
 */
class KeywordCountModule extends AppModule
{
	
    /**
     * 引用业务模型
     */
    public $models = array(
        'kwcount'   => 'keywordCount',
    );

    /**
     * 关键词搜索排行
     * 
     * @author  Far
     * @since   2016-05-23
     *
     * @access  public
     * @param   int         $type    关键词搜索类型
     *
     * @return  string      排行数据
     */
    public function getKeywordList($type,$dateStart,$dateEnd,$page, $limit)
    {
        if ( empty($type) ) return '';
        $r['raw']  = 1;
        $r['eq']    = array('type'=>$type);
        $r['col']   = array('keyword',"count(1)as counts");
        
        if(!empty($dateStart)){
            $r['raw'] .= " and date>=".$dateStart;
        }
        if(!empty($dateEnd)){
            $r['raw'] .= " and date<".$dateEnd;
        }
        $r['group'] = array('keyword' => 'asc');
        $r['order'] = array('counts' => 'desc');
        $r['page']  = $page;
        $r['limit'] = $limit;
        $res = $this->import('kwcount')->findAll($r);
        
        //获取查询总点击数
        $sql = " select sum(t.counts)as counts from (select count(1)as counts from t_keyword_count where ( `type`='{$type}' and {$r['raw']}) group by `keyword` asc) t";
        $counts = $this->fetchAll("trade_new",$sql);
        $res['counts']  = $counts[0]['counts'];
        return $res;
    }

}
?>