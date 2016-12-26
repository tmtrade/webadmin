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
//        $month = '201604';
        $dateStart  = strtotime( $month.'01' );
        $dateEnd    = strtotime( '-1 day', strtotime( date('Y-m-01') ) );

        $list = $this->load('dataanalyze')->dataAnalyze($dateStart,$dateEnd);
        $price = array(
            '1w以下'=>0,
            '1-2w'=>0,
            '2-4w'=>0,
            '4-8w'=>0,
            '8w以上'=>0,
        );
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
        $price = array_filter($price);
        $pi = array_sum( $price );
        $sort = 1000;
        foreach ($price as $k=>$v){
            $items[] = array(
                'type'      => 1,
                'sort'      => $sort--,
                'data1'     => $k,
                'data2'     => round(($v/$pi)*100,2),//价格的百分比
            );
        }
        $sort = 2000;
        //包装数据
        $items[] = array(
            'type'      => 2,
            'sort'      => 2000,
            'data1'     => 1,
            'data2'     => 0,//包装的百分比
        );
        $items[] = array(
            'type'      => 2,
            'sort'      => 1999,
            'data1'     => 2,
            'data2'     => 0,//未包装的百分比
        );
        //分类排行 前10
        $res = $this->load('keywordcount')->getKeywordList(4,$dateStart,$dateEnd, 1,20);
        $classList = $this->checkData($res['rows'], 3, 10);
        $sort = 3000;
        foreach ($classList as $k => $v) {
            $items[] = array(
                'type'      => 3,
                'sort'      => $sort--,
                'data1'     => $v['value'],
                'data2'     => $v['counts'],//数量
            );
        }
        //关键字
        $res    = $this->load('keywordcount')->getKeywordList(1,$dateStart,$dateEnd, 1, 10);
        $list   = $this->checkData($res['rows'], 4, 10);
        $sort = 4000;
        foreach ($list as $k => $v) {
            $items[] = array(
                'type'      => 4,
                'sort'      => $sort--,
                'data1'     => $v['keyword'],//关键字
            );
        }

        //组合类型
        $res    = $this->load('keywordcount')->getKeywordList(6,$dateStart,$dateEnd, 1, 20);
        $list   = $this->checkData($res['rows'], 5, 8);
        $count  = array_sum( arrayColumn($list, 'counts') );
        $sort = 5000;
        foreach ($list as $k => $v) {
            $items[] = array(
                'type'      => 5,
                'sort'      => $sort--,
                'data1'     => $v['value'],
                'data2'     => round(($v['counts']/$count)*100,2),//百分比
            );
        }

        //商标字数
        $res    = $this->load('keywordcount')->getKeywordList(7,$dateStart,$dateEnd, 1, 10);
        $list   = $this->checkData($res['rows'], 6, 5);
        $count  = array_sum( arrayColumn($list, 'counts') );
        $sort = 6000;
        foreach ($list as $k => $v) {
            $items[] = array(
                'type'      => 6,
                'sort'      => $sort--,
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
        $r['order'] = array('sort'=>'desc');
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
    public function orderUpDown($id, $aid, $updown, $type=4)
    {
        if ( empty($id) || empty($type)) return false;

        $rl['eq']   = array('id'=>$id,'analyId'=>$aid);
        $rl['col']  = array('sort');
        $res = $this->import('analyItems')->find($rl);
        if ( empty($res) || empty($res['sort']) ) return false;
        $order = $res['sort'];

        $r['eq'] = array(
            'type' => $type,
            'analyId' => $aid,
        );
        $r['raw']   = $updown == 1 ? " `sort` < $order " : " `sort` > $order ";
        $ord        = $updown == 1 ? 'desc' : 'asc';
        $r['order'] = array('sort'=>$ord);
        $res = $this->import('analyItems')->find($r);
        if ( empty($res) ) return false;
        $changeOrder    = $res['sort'];
        $changeId       = $res['id'];

        $update1    = array('sort'=>$changeOrder);//需要交换的
        $update2    = array('sort'=>$order);//被交换的

        $this->begin('saleAnalysis');

        $flag1 = $this->setItems($update1, $id);//需要变更的
        $flag2 = $this->setItems($update2, $changeId);//被变更的

        if ( $flag1 && $flag2 ) {
            return $this->commit('saleAnalysis');
        }
        $this->rollback('saleAnalysis');
        return false;
    }

    public function setItems($data, $id,$analyId)
    {
        $res = explode('-',$id);
        if(count($res)>1){//添加
            $data['type'] = $res[1];
            $data['analyId'] = $analyId;
            if(!$data['data1']) $data['data1'] = $res[0];
            //得到最后的sort
            $sort = $this->getSort($data['analyId'],$data['type']);
            $data['sort'] = $sort-1;
            return $this->import('analyItems')->create($data);
        }else{//编辑
            if ( $id <= 0 ) return false;
            $r['eq'] = array('id'=>$id);
            return $this->import('analyItems')->modify($data, $r);
        }
    }

    public function setAnaly($data, $id)
    {
        if ( $id <= 0 ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('analy')->modify($data, $r);
    }

    //更新items
    public function setAnalyItems($items, $type=2,$other=array(),$analyId=0)
    {
        if ( !is_array($items) || empty($items) ) return false;
        $this->begin('saleAnalysis');
        foreach ($items as $id => $v){
            if($type==3){
                $data = array('data1'=>$v,'data2'=>$other[$id]);//分类的数量
            }else{
                $data   = $type == 2 ? array('data2'=>$v) : array('data1'=>$v);
            }
            $res    = $this->setItems($data, $id,$analyId);
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

    /**
     * 添加关键字
     * @param $id
     * @param $keyword
     * @return bool|int
     */
    public function addKeyword($id, $keyword)
    {
        $r['eq']    = array('analyId'=>$id,'type'=>4);
        $r['order'] = array('sort'=>'asc');
        $res        = $this->import('analyItems')->find($r);
        if ( empty($res) ) return false;
        $sort = $res['sort']-1;
        $data = array(
            'analyId'   => $id,
            'type'      => 4,
            'data1'     => $keyword,
            'sort'     => $sort,
        );
        return $this->addItems($data);
    }

    /**
     * 得到最小的排序数字
     * @param $id
     * @param $type
     * @return bool
     */
    public function getSort($id,$type){
        $r['eq'] = array('analyId'=>$id,'type'=>$type);
        $r['order'] = array('sort'=>'asc');
        $r['col'] = array('sort');
        $res = $this->import('analyItems')->find($r);
        if(!$res) return false;
        return $res['sort'];
    }

    /**
     * 得到item详情
     * @param $id
     * @return array
     */
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

    /**
     * 创建报告图片
     * @param $id
     * @return array
     */
    public function createPic($id){
        if(!$id) return array('code'=>1,'msg'=>'参数错误');
        $url = SELLER_URL.'saleanalysis/report/?id='.$id;
        $file = './Static/upload/analysis/report_'.$id.'.jpg';
        //删除文件
        if(is_file($file)){
            $rst = unlink($file);
            if($rst==false){
                return array('code'=>2,'msg'=>'删除文件失败');
            }
        }
        //创建新文件
        $isPdf	= makePng($url,$file);
        if($isPdf==1){
            return array('code'=>3,'msg'=>'生成图片失败');
        }
        return array('code'=>0);
    }

    /**
     * 发布报告
     * @param $id
     * @param $type int 1发布, 2检测百分比
     * @return bool
     */
    public function fabu($id,$type=1){
        //检测百分比
        $rst = $this->checkBfb($id);
        if($rst!==0){
            $rst = explode(',',$rst);
            $msg = '';
            if(in_array('1',$rst)){
                $msg .= ' 价格区间百分比错误 ';
            }
            if(in_array('2',$rst)){
                $msg .= ' 组合类型百分比错误 ';
            }
            if(in_array('3',$rst)){
                $msg .= ' 商标字数百分比错误 ';
            }
            if(in_array('4',$rst)){
                $msg .= ' 无数据分享详情 ';
            }
            return array('code'=>1,'msg'=>$msg);
        }
        if($type==2){
            return array('code'=>0);//检测百分比成功
        }
        //修改数据
        $r = array(
            'eq'=>array('id'=>$id)
        );
        $data = array('status'=>1);
        $rst = $this->import('analy')->modify($data, $r);
        if($rst){
            return array('code'=>0);
        }else{
            return array('code'=>2,'msg'=>'修改状态失败');
        }
    }

    /**
     * 检测百分比
     * @param int $id
     * @param int $type
     * @return bool
     */
    public function checkBfb($id,$type=0){
        if(empty($id)) return false;
        $r['eq']['analyId'] = $id;
        $r['col'] = array('type','data1','data2');
        $r['limit'] = 1000;
        if($type!=0){
            $r['eq']['type'] = $type;
        }
        $rst = $this->import('analyItems')->find($r);
        if($rst){
            $res = array(0,0,0);
            foreach($rst as $item){
                //计算百分比
                if(($type==1 || $type==0) && $item['type']==1){//检测销售价格
                    $res[0] += $item['data2'];
                }
                if(($type==5 || $type==0) && $item['type']==5){//检测组合类型
                    $res[1] += $item['data2'];
                }
                if(($type==6 || $type==0) && $item['type']==6){//检测商标字数
                    $res[2] += $item['data2'];
                }
            }
            //评价结果
            $return = 0;
            if(($type==1 || $type==0) && !($this->floatEq($res[0],100))){//检测销售价格
                $return .=(','.'1');
            }
            if(($type==5 || $type==0) && !($this->floatEq($res[1],100))){//检测组合类型
                $return .=(','.'2');
            }
            if(($type==6 || $type==0) && !($this->floatEq($res[2],100))){//检测商标字数
                $return .=(','.'3');
            }
            return $return;
        }
        return 4;
    }

    /**
     * 比较两个浮点数
     * @param $f1
     * @param $f2
     * @param int $precision 精确比较的位数
     * @return bool
     */
    private function floatEq($f1,$f2,$precision=3){
        $e = pow(10,$precision);
        $i1 = intval($f1 * $e);
        $i2 = intval($f2 * $e);
        return ($i1 == $i2);
    }

}
?>