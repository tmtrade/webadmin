<?
header("Content-type: text/html; charset=utf-8");

/**
 * 兑换信息管理
 *
 * @package    Action
 * @author     Far
 * @since      2016年6月7日10:09:46
 */
class ExchangeAction extends AppAction
{
        //列表页
        public function index()
        {
            $params = $this->getFormData();
            $page 	= $this->input('page', 'int', '1');
            if(empty($params['isUse'])) $params['isUse'] = 3;
            $res        = $this->load('exchange')->getExchangeList($params,$page, $this->rowNum);
            $total 	= empty($res['total']) ? 0 : $res['total'];
            $list 	= empty($res['rows']) ? array() : $res['rows'];
            $pager 	= $this->pager($total, $this->rowNum);
            $pageBar 	= empty($list) ? '' : getPageBar($pager);
            $this->set("pageBar",$pageBar);
            $this->set("list",$list);
//            echo "<pre>";
//            var_dump($params);
            $this->set('s', $params);
            $this->set("module_type",C("MODULE_TYPE"));
            $this->display();

        }
        
     //取消通过
    public function cancel()
    {
            $id 	= $this->input('id', 'int', 0);
            $reason = $this->input('reason', 'text', '');
            if ( $id <= 0 ){
                    $this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
            }
            if ( $reason == '' ){
                    $this->returnAjax(array('code'=>2,'msg'=>'请填写原因')); 
            }
            $res = $this->load('exchange')->setCancel($id, $reason);
            if ( $res ){
                //发送驳回的系统消息
                $info = $this->load('exchange')->getExchangeInfo($id);
                $this->checkMsg($info['uid']);
                $this->returnAjax(array('code'=>1));
            }
            $this->returnAjax(array('code'=>2, 'msg'=>'驳回失败了'));
    }
    
    //审核通过兑换信息
    public function through()
    {
            $id = $this->input('id', 'int', 0);
            if ($id <= 0 ){
                    $this->returnAjax(array('code'=>2)); 
            }
            $info = $this->load('exchange')->getExchangeInfo($id);
            if ( empty($info) ){
                    $this->returnAjax(array('code'=>2,'msg'=>'该条信息不存在')); 
            }
            $res = $this->load('exchange')->addAd($info,$id);
            if ( $res ){
                $this->checkMsg($info['uid']);
                $this->returnAjax(array('code'=>1));
            }
            $this->returnAjax(array('code'=>2));
    }

        
       
}

?>