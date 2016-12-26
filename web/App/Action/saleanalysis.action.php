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
        if ( $id <= 0 ) return $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));

        $analy  = $this->load('analysis')->getSaleAnalysisData($id);
//        var_dump($analy);exit;
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

    public function delAnalyItems()
    {
        $id = $this->input('id', 'int', 0);
        if ( $id <= 0 ) return $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));

        $res = $this->load('analysis')->delItems($id);
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'删除失败'));
    }

    public function orderItems()
    {
        $id 	= $this->input('id', 'int', '');
        $aid 	= $this->input('aid', 'int', '');
        $updown = $this->input('updown', 'int', '');
        $type 	= $this->input('type', 'int', '');
        if ( empty($id) || empty($aid) || empty($updown) || empty($type) ){
            $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
        }
        //向上向下
        $res = $this->load('analysis')->orderUpDown($id, $aid, $updown, $type);
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
    }

    /**
     * 设置标题和发布时间
     */
    public function setAnaly()
    {
        $id 	= $this->input('id', 'int', 0);
        $title  = $this->input('title', 'string', '');
        $date   = $this->input('date', 'string', '');

        if ( $id <= 0 ) $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));

        if ( empty($title) ) $this->returnAjax(array('code'=>2,'msg'=>'标题不能为空'));

        if ( strtotime($date) <= 0 ){
            $this->returnAjax(array('code'=>2,'msg'=>'发布日期格式错误'));
        }
        $data = array('title'=>$title,'date'=>strtotime($date));
        $res = $this->load('analysis')->setAnaly($data, $id);
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
    }

    /**
     * 保存列表信息
     */
    public function setAnalyItems()
    {
        $items  = $this->input('items', 'array', '');
        $analyId  = $this->input('analyId', 'int');
        $other  = $this->input('other', 'array', '');
        $type   = $this->input('type', 'int', 2);

        if ( empty($items) || !is_array($items) ) {
            $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
        }

        $res = $this->load('analysis')->setAnalyItems($items, $type,$other,$analyId);
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
    }

    /**
     * 价格弹窗
     */
    public function addPrice()
    {
        $id         = $this->input('id', 'int', 0);
        $analyId    = $this->input('analyId', 'int', 0);
        if ( $analyId <= 0 ) exit('参数错误!!!');
        if ( $id > 0 ){
            $info = $this->load('analysis')->getItems($id);
            $this->set('id', $id);
            $this->set('info', $info);
        }
        $this->set('analyId', $analyId);
        $this->display();
    }

    /**
     * 关键字弹窗
     */
    public function addKeyword()
    {
        $id      = $this->input('id', 'int', 0);
        if ( $id <= 0 ) exit('参数错误!!!');

        $this->set('id', $id);
        $this->display();
    }

    /**
     * 添加/修改关键字
     */
    public function setKeyword()
    {
        $id      = $this->input('id', 'int', 0);
        $keyword = $this->input('data1', 'string', '');

        if ( $id <= 0 ) {
            $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
        }
        if ( empty($keyword) ) {
            $this->returnAjax(array('code'=>2,'msg'=>'关键字不能为空'));
        }

        $res = $this->load('analysis')->addKeyword($id, $keyword);
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
    }

    /**
     * 添加/修改价格区间
     */
    public function setPrice()
    {
        $analyId    = $this->input('analyId', 'int', 0);
        $id         = $this->input('id', 'int', 0);
        $data1      = $this->input('data1', 'string', '');
        $data2      = $this->input('data2', 'float', 0);

        if ( $analyId <= 0 ) {
            $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
        }
        if ( empty($data1) || $data2 <= 0 ) {
            $this->returnAjax(array('code'=>2,'msg'=>'数据不能为空或小于0'));
        }

        if ( $id > 0 ) {
            $data = array(
                'data1'     => $data1,
                'data2'     => $data2,
            );
            $res = $this->load('analysis')->setItems($data, $id);
        }else{
            //得到排序值
            $sort = $this->load('analysis')->getSort($analyId,1);
            if($sort===false) $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
            $sort--;
            $data = array(
                'analyId'   => $analyId,
                'type'      => 1,
                'sort'      => $sort,
                'data1'     => $data1,
                'data2'     => $data2,
            );
            $res = $this->load('analysis')->addItems($data);
        }
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
    }

    /**
     * 创建报告图片
     * @return bool
     */
    Public function createPic(){
        $id = $this->input('id','int');
        if(!$id) $this->returnAjax(array('code'=>1,'msg'=>'参数错误'));
        $array = $this->load('analysis')->createPic($id);
        $this->returnAjax($array);
    }

    /**
     * 查看报告
     */
    public function view(){
        $id = $this->input('id','int');
        if(!$id) exit('参数错误');
        $file = './Static/upload/analysis/report_'.$id.'.jpg';
        if(is_file($file)){
            $this->redirect('', ltrim($file,'.'));
        }else{
            exit('图片不存在');
        }
    }

    /**
     * 发布报告
     */
    public function fabu(){
        $id = $this->input('id','int');
        if(!$id) $this->returnAjax(array('code'=>1,'msg'=>'参数错误'));
        $array = $this->load('analysis')->fabu($id);
        $this->returnAjax($array);
    }

    /**
     * 检测百分比
     */
    public function check(){
        $id = $this->input('id','int');
        if(!$id) $this->returnAjax(array('code'=>1,'msg'=>'参数错误'));
        $array = $this->load('analysis')->fabu($id,2);
        $this->returnAjax($array);
    }

}

?>