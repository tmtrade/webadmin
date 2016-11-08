<?
/*
 * 数据分析模块
 *
 *autor     Xuni
 *date      2016-11-02
 */
class AnalysisModule extends AppModule
{
    public $models = array(
        'analy'         => 'saleAnalysis',
        'analyItems'    => 'saleAnalysisItems',
        'sale'          => 'sale',
        'tmClass'       => 'tmClass',
    );

    /*
     * 每个月初生成出售商品数据分析报告
     */
    public function createSaleAnalysisReport($type=0)
    {
        $month = date('Ym', strtotime('-1 month'));
        if ( $this->isMonth($month) ) {
            if ( $type == 1 ) exit('data exist');
            return false;
        }

        $dateStart  = strtotime( $month.'01' );
        $dateEnd    = strtotime( '-1 day', strtotime( date('Y-m-01') ) );

        $list = $this->load('dataanalyze')->dataAnalyze($dateStart,$dateEnd);
        $price = array();
        foreach ($list as $v){
            if($v['price']==0) continue;
            if($v['price']<10000){
                $price['1w以下'] +=1;
            }
            else if($v['price']>=10000 && $v['price']<20000){
                $price['1-2w'] +=1;
            }
            else if($v['price']>=20000 && $v['price']<40000){
                $price['2-4w'] +=1;
            }
            else if($v['price']>=40000 && $v['price']<80000){
                $price['4-8w'] +=1;
            }
            else if($v['price']>=80000){
                $price['8w以上'] +=1;
            }
        }
        arsort($price);
        $pi = array_sum( $price );
        foreach ($price as $k=>$v){
            $priceStr = strstr($k, 'w', true);
            $items[] = array(
                'type'      => 1,
                'data1'     => $priceStr,
                'data2'     => round(($v/$pi)*100,2),//包装的百分比
            );
        }
        //debug($items);

        //包装数据
        $items[] = array(
            'type'      => 2,
            'data1'     => 1,
            'data2'     => 0,//包装的百分比
        );
        $items[] = array(
            'type'      => 2,
            'data1'     => 2,
            'data2'     => 0,//未包装的百分比
        );
//        $items[] = array(
//            'type'      => 2,
//            'data1'     => 3,
//            'data2'     => 0,//相差倍数
//        );

        //分类排行 前10
        $res = $this->load('keywordcount')->getKeywordList(4,$dateStart,$dateEnd, 1,20);
        $classList = $this->checkData($res['rows'], 3, 10);
        $count  = 9999;
        foreach ($classList as $k => $v) {
            $items[] = array(
                'type'      => 3,
                'data1'     => $v['value'],
                'data2'     => $count--,//排序
            );
        }

        //关键字
        $res    = $this->load('keywordcount')->getKeywordList(1,$dateStart,$dateEnd, 1, 10);
        $list   = $this->checkData($res['rows'], 4, 10);
        $count  = 9999;
        foreach ($list as $k => $v) {
            $items[] = array(
                'type'      => 4,
                'data1'     => $v['keyword'],//关键字
                'data2'     => $count--,//排序
            );
        }

        //组合类型
        $res    = $this->load('keywordcount')->getKeywordList(6,$dateStart,$dateEnd, 1, 20);
        $list   = $this->checkData($res['rows'], 5, 8);
        $count  = array_sum( arrayColumn($list, 'counts') );
        foreach ($list as $k => $v) {
            $items[] = array(
                'type'      => 5,
                'data1'     => $v['value'],
                'data2'     => round(($v['counts']/$count)*100,2),//百分比
            );
        }

        //商标字数
        $res    = $this->load('keywordcount')->getKeywordList(7,$dateStart,$dateEnd, 1, 10);
        $list   = $this->checkData($res['rows'], 6, 5);
        $count  = array_sum( arrayColumn($list, 'counts') );
        foreach ($list as $k => $v) {
            $items[] = array(
                'type'      => 6,
                'data1'     => $v['value'],
                'data2'     => round(($v['counts']/$count)*100,2),//百分比
            );
        }

        $analy = array(
            'title'     => $month.'自动生成报告',
            'month'     => $month,
            'date'      => strtotime(date('Y-m-d')),
        );

        $this->begin('saleAnalysis');
        $analyId = $this->import('analy')->create($analy);
        if ( $analyId <= 0 ) {
            $this->rollback('saleAnalysis');
            if ( $type == 1 ) exit('analy create faild');
            return false;
        }
        foreach ($items as $item){
            $item['analyId'] = $analyId;
            $flag = $this->import('analyItems')->create($item);
            if ( $flag <= 0 ){
                $this->rollback('saleAnalysis');
                if ( $type == 1 ) exit('analy create faild');
                return false;
            }
        }
        $this->commit('saleAnalysis');
        if ( $type == 1 ) exit('finished');
        return true;
    }

    public function checkData($data, $type, $num)
    {
        if ( empty($data) || !is_array(current($data)) ) return $data;
        $_data = array();
        foreach ($data as $k => $v){
            switch ($type){
                case '3':
                    $str = strstr($v['keyword'], '-', true);
                    if ( $str === false || !in_array($str, range(1,45)) ) continue;
                    break;
                case '5':
                    $str = strstr($v['keyword'], '-', true);
                    if ( $str === false || !in_array($str, range(1,8)) ) continue;
                    break;
                case '6':
                    $str = array_search($v['keyword'], C('COUNT_NUM'));
                    if ( $str === false ) continue;
                    break;
                default:
                    $str = $v['keyword'];
                    break;
            }
            $v['value'] = $str;
            $_data[]    = $v;
            if ( count($_data) == $num ) break;
        }
        return $_data;
    }

    public function isMonth($month)
    {
        if ( empty($month) ) return false;
        $r['eq'] = array('month'=>$month);
        $count = $this->import('analy')->count($r);
        return $count > 0 ? true : false;
    }

    public function countEmbellish($has=true)
    {
        $str = $has ? " embellish != '' " : " embellish = '' " ;
        $r = array(
            'eq'    => array(
                'status' => 1,
            ),
            'raw'   => " id IN (SELECT saleId FROM t_sale_tminfo WHERE {$str}) ",
        );
        return $this->import('sale')->count($r);
    }

    public function getSaleAnalysisList($page=1, $num=10)
    {
        $r['page']  = $page;
        $r['limit'] = $num;
        $res = $this->import('analy')->findAll($r);
        return $res;
    }

    public function getSaleAnalysisData($id)
    {
        $analy = $this->getSaleAnaly($id);
        if ( empty($analy) ) return array();

        $items = $this->getSaleAnalyItems($id);
        if ( empty($analy) ) return array();

        $_items= [];
        foreach ($items as $v){
            if ( in_array($v['type'], array(2,5,6)) ){
                $_items[$v['type']][$v['data1']] = $v;
            }else{
                $_items[$v['type']][] = $v;
            }
        }
        $analy['items'] = $_items;
        return $analy;
    }

    public function getSaleAnaly($id)
    {
        $r['eq']    = array('id'=>$id);
        $r['limit'] = 1;
        return $this->import('analy')->find($r);
    }

    public function getSaleAnalyItems($analyId)
    {
        $r['eq']    = array('analyId'=>$analyId);
        $r['order'] = array('data2'=>'desc');
        $r['limit'] = 200;
        return $this->import('analyItems')->find($r);
    }

    //删除item
    public function delItems($id)
    {
        if ( $id <= 0 ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('analyItems')->remove($r);
    }

    //对某类别中某项进行上下排序
    //$updown 1：向上，2：向下
    public function orderUpDown($id, $updown, $type=4)
    {
        if ( empty($id) || empty($type)) return false;

        $rl['eq']   = array('id'=>$id);
        $rl['col']  = array('data2');
        $res = $this->import('analyItems')->find($rl);
        if ( empty($res) || empty($res['data2']) ) return false;

        $order = $res['data2'];

        $r['eq'] = array(
            'type' => $type,
        );
        $r['raw']   = $updown == 1 ? " `data2` < $order " : " `data2` > $order ";
        $ord        = $updown == 1 ? 'desc' : 'asc';
        $r['order'] = array('data2'=>$ord);
        $res = $this->import('analyItems')->find($r);
        if ( empty($res) ) return false;

        $changeOrder    = $res['data2'];
        $changeId       = $res['id'];

        $update1    = array('data2'=>$changeOrder);//需要交换的
        $update2    = array('data2'=>$order);//被交换的

        $this->begin('saleAnalysis');

        $flag1 = $this->setItems($update1, $id);//需要变更的
        $flag2 = $this->setItems($update2, $changeId);//被变更的

        if ( $flag1 && $flag2 ) {
            return $this->commit('saleAnalysis');
        }
        $this->rollback('saleAnalysis');
        return false;
    }

    public function setItems($data, $id)
    {
        if ( $id <= 0 ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('analyItems')->modify($data, $r);
    }

    public function setAnaly($data, $id)
    {
        if ( $id <= 0 ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('analy')->modify($data, $r);
    }

    //更新items
    public function setAnalyItems($items, $type=2)
    {
        if ( !is_array($items) || empty($items) ) return false;

        $this->begin('saleAnalysis');
        foreach ($items as $id => $v){
            $data   = $type == 2 ? array('data2'=>$v) : array('data1'=>$v);
            $res    = $this->setItems($data, $id);
            if ( !$res ){
                $this->rollback('saleAnalysis');
                return false;
            }
        }
        $this->commit('saleAnalysis');
        return true;
    }

    public function addItems($data)
    {
        return $this->import('analyItems')->create($data);
    }

    public function addKeyword($id, $keyword)
    {
        $r['eq']    = array('analyId'=>$id,'type'=>4);
        $r['order'] = array('data2'=>'asc');
        $res        = $this->import('analyItems')->find($r);
        if ( empty($res) ) return false;

        $sort = $res['data2']--;
        $data = array(
            'analyId'   => $id,
            'type'      => 4,
            'data1'     => $keyword,
            'data2'     => $sort,
        );
        return $this->addItems($data);
    }

    public function getItems($id)
    {
        if ( $id <= 0 ) return array();
        $r['eq'] = array('id'=>$id);
        return $this->import('analyItems')->find($r);
    }

    /**
     * 获取商标分类与群组相关标题
     *
     * 获取商标分类与群组相关标题
     *
     * @author  Xuni
     * @since   2016-03-08
     *
     * @return  array
     */
    public function getClassGroup($class=0, $group=1)
    {
        if ( $class == 0 && $group != 1 ){
            $r['eq'] = array('parent'=>0);
        }elseif ( $class != 0 && $group == 1 ){
            //$r['eq'] = array('parent'=>$class);
            $r['raw'] = " (`parent` = '$class' OR `number` = '$class') ";
        }elseif ( $class != 0 ){
            $r['eq'] = array('number'=>$class);
        }
        //$r['order'] = array('parent'=>'asc','number'=>'asc');
        $r['limit'] = 1000;

        $_class = $_group = array();
        $res    = $this->import('tmClass')->find($r);
        if ( empty($res) ) return array();

        foreach ($res as $k => $v) {
            if ( $v['parent'] == '0' ){
                $_class[$v['number']] = empty($v['title']) ? $v['name'] : $v['title'];
            }elseif ( $v['parent'] != 0 ){
                $_group[$v['parent']][$v['number']] = empty($v['title']) ? $v['name'] : $v['title'];
            }
        }
        ksort($_class);
        return array($_class, $_group);
    }
}
?>