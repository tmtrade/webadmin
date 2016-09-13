<?php
/**
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/9/13 0013
 * Time: 下午 13:48
 */
class QuotationAction extends AppAction
{

    private $_num = 10;

    /**
     * 得到报价单列表
     */
    public function index()
    {
        //参数
        $page 	= $this->input('page', 'int', '1');
        $res 	= $this->load('quotation')->getList($page,$this->_num);
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
    }

    /**
     * 删除报价单
     */
    public function remove()
    {
        $id 	= $this->input('id', 'int');
        $uid 	= $this->input('uid', 'int');
        if(!$id || !$uid) $this->returnAjax(array('code'=>1,'msg'=>'参数错误'));
        $res = $this->load('quotation')->delete($id,$uid);
        if($res===true){
            $this->returnAjax(array('code'=>0,'msg'=>'操作成功'));
        }else{
            $this->returnAjax(array('code'=>1,'msg'=>'删除失败'));
        }
    }

}