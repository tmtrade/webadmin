<?
/**
 * 商品报价单
 * 
 * 查询、创建
 *
 * @package	Module
 * @author	dower
 * @since	2016-09-13
 */
class QuotationModule extends AppModule
{

    /**
     * 引用业务模型
     */
    public $models = array(
        'quotation' => 'quotation',
        'user' => 'user',
    );

    /**
     * 得到报价单分页数据
     * @param $page
     * @param int $limit
     * @return array
     */
    public function getList($page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['col'] = array('id','title','uid','created');
        $r['order'] = array('created'=>'desc');
        $res = $this->import('quotation')->findAll($r);
        //处理数据
        if($res['rows']){
            foreach($res['rows'] as &$item){
                $temp = $this->import('user')->get($item['uid']);
                if($temp){
                    //得到创建人
                    $item['username'] = $temp['nickname']?:($temp['name']?:'未设置');
                    //得到手机号
                    $item['mobile'] = $temp['mobile'];
                }
                //得到预览地址
                $item['url'] = SELLER_URL.'p-'.$item['id'].'-'.$item['uid'].'.html';
            }
            unset($item);
        }
        return $res;
    }

    /**
     * 删除商品单
     * @param $id
     * @param $uid
     * @return bool
     */
    public function delete($id,$uid){
        //掉用接口删除报价单
        $rst = $this->importBi('quotation')->removeQuotation(array('id'=>$id,'uid'=>$uid));
        if($rst['code']==200){
            $rst = array('code'=>0,'msg'=>'操作成功');
        }
        return $rst;
    }

}
?>