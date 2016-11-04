<?
header("Content-type: text/html; charset=utf-8");

/**
 * 广告管理
 *
 * @package    Action
 * @author     Far
 * @since      2016年6月7日10:29:46
 */
class SaleAnalysisAction extends AppAction
{
    private $_num = 10;
    /**
     * 出售数据分析
     */
    public function index()
    {
        $page 	= $this->input('page', 'int', 1);
        $res    = $this->load('analysis')->getSaleAnalysisList($page,$this->_num);
        //得到结果
        $total 	= empty($res['total']) ? 0 : $res['total'];
        $list 	= empty($res['rows']) ? array() : $res['rows'];
        //得到分页条
        $pager 		= $this->pager($total, $this->_num);
        $pageBar 	= empty($list) ? '' : getPageBar($pager);
        $this->set('total', $total);
        $this->set("pageBar",$pageBar);
        $this->set('list', $res['rows']);
        $this->display();
        //debug($res);
    }

    public function edit()
    {
        $id = $this->input('id', 'int', 0);
        if ( $id <= 0 ) {
            exit('参数错误');
        }

        $analy  = $this->load('analysis')->getSaleAnalysisData($id);

        $CLASS  = $this->load('analysis')->getClassGroup(0,0);
        $baoz   = $this->load('analysis')->countEmbellish(true);
        $nobaoz = $this->load('analysis')->countEmbellish(false);
        //debug($CLASS);
        $this->set('CLASS', $CLASS[0]);
        $this->set('info', $analy);
        $this->set('baoz', $baoz);
        $this->set('nobaoz', $nobaoz);
        $this->set('items', $analy['items']);
        $this->display();
    }
       
}

?>