<?
header("Content-type: text/html; charset=utf-8");

/**
 * 国内商标
 *
 * @package Action
 * @author  Xuni
 * @since   2016-01-8
 */
class InternalAction extends AppAction {
//  public $debug = true;
    public $rowNum = 30;
    /**
     * 国内商标列表
     * 
     * @author  Xuni
     * @since   2016-01-8
     * @access  public
     * @return  void
     */
    public function index() {
        $this->getSetting();
        //参数
        $params = $this->getFormData();
        $page = $this->input('page', 'int', '1');

        $res = $this->load('internal')->getList($params, $page, $this->rowNum);

        $total = empty($res['total']) ? 0 : $res['total'];
        $list = empty($res['rows']) ? array() : $res['rows'];

        $pager = $this->pager($total, $this->rowNum);
        $pageBar = empty($list) ? '' : getPageBar($pager);
        $result = array();
        //获取所有联系人
        foreach ($list as $k => $v) {
            $result[$k] = $this->load('internal')->getSaleInfo($v['id']);
            $result[$k]['imgUrl'] = $this->load('internal')->saleImg($result[$k]['number']);
            $result[$k]['qitem'] = $this->load('internal')->getQuotationItemList($result[$k]['number']);
        }
        $this->set("allTotal", $this->load('internal')->countSale());
        $this->set('total', $total);
        $this->set("pageBar", $pageBar);
        $this->set('s', $params);
        $this->set('saleList', $result);
        $this->display();

    }

    /**
     * 群组
     * 
     * @author  Jeany
     * @since   2015-09-10
     *
     * @access  public
     * @return  void
     */
    public function groups() {
        $class = trim($this->input("class"));
        $groups = $this->load("group")->getGroups($class);
        $this->set("groups", $groups['rows']);
        $this->display();
    }

    //添加商品
    public function create() {
        $this->display();
    }

    /**
     * 完成交易的弹出页面
     */
    public function complate() {
        $id = $this->input('id', 'int', 0);
        //得到id对应的联系人信息
        $contact = $this->load('internal')->getSaleContact($id);
        $this->set('contact', $contact);

        $this->set('saleId', $id);
        $this->set('type', 0);
        $this->display();
    }

    /**
     * 完成交易操作
     */
    public function complateSale() {
        //得到相关参数
        $saleId = $this->input('saleId', 'int', 0);
        if ($saleId == 0)
            $this->returnAjax(array('code' => 1, 'msg' => '参数错误'));

        $price = $this->input('price', 'string', 0);
        $type = $this->input('type', 'int', 0);
        $uids = $this->input('uid', 'string', '');
        $date = $this->input('date', 'string', '');
        if ($date) {
            $date = strtotime($date);
        } else {
            $date = strtotime(date('Y-m-d'));//当天零时
        }
        $uids = explode("|", $uids);
        //保存到数据库中
        $data = array(
            'saleId' => $saleId,
            'price' => $price,
            'type' => $type,
            'uid' => $uids[1],
                'cid' => $uids[0],
            'date' => $date,
        );
        $rst = $this->load('internal')->complateSale($data, $this->userId);
        //返回结果
        if ($rst == 0) {
            $uid = $uids[1];
            if($uid){ //有账户的用户才操作
                $this->checkMsg($uid);
                $this->load('total')->upTotal($uid,1,10,"一条商品交易完成"); //完成交易加10豆豆
            }
            $this->returnAjax(array('code' => 0));
        } else if ($rst == 1) {
            $this->returnAjax(array('code' => 1, 'msg' => '参数错误'));
        } else if ($rst == 2) {
            $this->returnAjax(array('code' => 2, 'msg' => '保存收益表错误'));
        } else if ($rst == 3) {
            $this->returnAjax(array('code' => 3, 'msg' => '保存用户出售信息出错'));
        } else if ($rst == 4) {
            $this->returnAjax(array('code' => 4, 'msg' => '删除相关信息出错'));
        }
    }

    //删除商品
    public function delete() {
        $id = $this->input('id', 'string', '');

        $this->set('saleId', $id);
        $this->display();
    }

    //删除商品
    public function deleteSale() {
        $id = $this->input('saleId', 'string', '');

        if (empty($id))
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));

        $ids = array_filter(array_unique(explode(',', $id)));
        if (empty($ids)) {
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
        }

        $type = $this->input('reason', 'int', 0); //删除原因
        $black = $this->input('isBlack', 'int', 0); //是否剔除黑名单
        $date = $this->input('saleDate', 'string', ''); //出售日期
        $memo = $this->input('memo', 'string', ''); //备注（原因）
        if (empty($type) || empty($black) || empty($memo) || ($type == 1 && empty($date))) {
            $this->returnAjax(array('code' => 2, 'msg' => '相关数据不能为空'));
        }

        $res = $this->load('internal')->deleteSale($ids, $type, $memo, $black, $date);
        if ($res) {
            $this->returnAjax(array('code' => 1, 'msg' => '成功'));
        }
        $this->returnAjax(array('code' => 2, 'msg' => '操作失败'));
    }

    //添加/编辑 联系人
    public function contact() {
        $sId = $this->input('saleId', 'int', 0);
        $cId = $this->input('cId', 'int', 0);
        if ($cId > 0) {
            $contact = $this->load('internal')->getSaleContact($sId, $cId);
        } else {
            $contact = array();
        }
        $this->set('cId', $cId);
        $this->set('saleId', $sId);
        $this->set('contact', $contact);
        $this->getSetting();
        $this->display();
    }

    //（操作）添加/编辑 联系人
    public function setContact() {
        //参数
        $params = $this->getFormData();
        if ($params['saleId'] <= 0) {
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
        }
        if ($params['source'] <= 0) {
            $this->returnAjax(array('code' => 2, 'msg' => '请选择来源'));
        }
        if ($params['name'] == '') {
            $this->returnAjax(array('code' => 2, 'msg' => '请输入联系人'));
        }
        if ($params['phone'] == '') {
            $this->returnAjax(array('code' => 2, 'msg' => '请输入联系人电话'));
        }
        if ($params['price'] < 0) {
            $this->returnAjax(array('code' => 2, 'msg' => '请输入正确的底价'));
        }
        if ($params['date'] == '' || strtotime($params['date']) === false || strtotime($params['date']) < 0) {
            $params['date'] = time();
        } else {
            $params['date'] = strtotime($params['date']);
        }
        $cId = $params['cId'];
        unset($params['cId']);
        $sale = $this->load('internal')->getSaleInfo($params['saleId']);
        $params['number']   = $sale['number'];
        $params['tid']      = $sale['tid'];
        if ($cId <= 0) {
            $type = 8;
            $res = $this->load('internal')->addContact($params, $params['saleId']);
            $cId = $res;
        } else {
            $type = 9;
            $res = $this->load('internal')->updateContact($params, $cId);
        }
        if ($res) {
            $this->load('log')->addSaleLog($params['saleId'], $type, "联系人ID：$cId 被编辑了"); //记录日志
            $this->returnAjax(array('code' => 1, 'msg' => '成功'));
        }
        $this->returnAjax(array('code' => 2, 'msg' => '操作失败'));
    }

    //编辑商品
    public function edit() {
        $id = $this->input('id', 'int', 0);
        if ($id <= 0) {
            MessageBox::halt('参数错误');
        }
        $sale       = $this->load('internal')->getSaleInfo($id);
        $tminfo     = $this->load('trademark')->getTminfo($sale['number']);
        $log        = $this->load('log')->getSaleLog($id);
        $allphone   = $this->load('phone')->getAllPhone();
        $gjUrl      = SEARCH_URL . 'trademark/detail/?id=' . $sale['tid'];
        $referr     = $this->getReferrUrl('internal_edit');

        //获取SEO设置
        $seoInfo    = $this->load('seo')->getInfoByType(8, $sale['tid']);
        $this->set('seoInfo', $seoInfo);

        $this->getSetting();
        $this->set('log', $log);
        $this->set('sale', $sale);
        $this->set('gjUrl', $gjUrl);
        $this->set('tminfo', $tminfo);
        $this->set('tmTop', C('ISTOP_LIST'));
        $this->set('allphone', $allphone);
        $this->set('referr', $referr);
        $this->display();
    }

    //商品下架(可批量)
    public function doDown() {
        $id     = $this->input('id', 'string', '');
        $reason = $this->input('reason', 'string', '');
        if (empty($id)) {
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
        }
        if (empty($reason)) {
            $this->returnAjax(array('code' => 2, 'msg' => '请填写下架原因'));
        }
        $ids = array_filter(array_unique(explode(',', $id)));
        if (empty($ids)) {
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
        }
        $res = $this->load('internal')->saleDown($ids, $reason);
        if ($res)
            $this->returnAjax(array('code' => 1));
        $this->returnAjax(array('code' => 2));
    }

    //商品上架
    public function doUp() {
        $id = $this->input('id', 'int', 0);
        if ($id <= 0) {
            $this->returnAjax(array('code' => 2));
        }
        //TODO 判断联系人是否有一个是审核过的
        $isVerify = $this->load('internal')->existVerifyContact($id);
        if (!$isVerify)
            return $this->returnAjax(array('code' => 2, 'msg' => '无联系人或至少有一位联系人未审核！'));

        //$isBlack = $this->load('internal')->isBlack($id);
        //if ( $isBlack ) return $this->returnAjax(array('code'=>2,'msg'=>'商品还在黑名单中，请在编辑中剔除！'));

        $res = $this->load('internal')->saleUp($id);
        if ($res)
            $this->returnAjax(array('code' => 1));
        $this->returnAjax(array('code' => 2, 'msg' => '操作失败，请稍后重试！'));
    }

    //提交价格信息
    public function setPrice() {
        $saleId = $this->input('saleId', 'int', 0);
        $isSale = $this->input('isSale', 'int', 0);
        $isLicense = $this->input('isLicense', 'int', 0);
        if ($saleId <= 0) {
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
        }
        $data = array();
        if ($isSale) {
            $priceType      = $this->input('priceType', 'int', 2);
            $price          = $this->input('price', 'int', 0);
            $isOffprice     = $this->input('isOffprice', 'int', 2);
            $salePrice      = $this->input('salePrice', 'int', 0);
            $priceDate      = $this->input('priceDate', 'int', 2);
            $salePriceDate  = $this->input('salePriceDate', 'text', '');
            if ($priceType == 1) {//定价
                if ($price <= 0) {//未填写销售价格
                    $this->returnAjax(array('code' => 2, 'msg' => '请输入销售价格'));
                }
                if ($isOffprice == 1 && $salePrice > $price) {
                    $this->returnAjax(array('code' => 2, 'msg' => '特价价格不能大于销售价格'));
                }
                if ($isOffprice == 1 && $salePrice <= 0) {//是特价但未填写特价价格
                    $this->returnAjax(array('code' => 2, 'msg' => '请输入特价价格'));
                } elseif ($isOffprice == 1 && $priceDate == 1 && $salePriceDate == '') {//特价且限时未选择时间
                    $this->returnAjax(array('code' => 2, 'msg' => '请输入销售价格'));
                } elseif ($isOffprice == 1) {
                    $data['isOffprice'] = 1;
                    $data['salePrice'] = $salePrice;
                    $data['salePriceDate'] = ($priceDate == 2) ? 0 : strtotime($salePriceDate);
                } else {
                    $data['isOffprice'] = 2;
                }
                $data['price'] = $price;
                $data['priceType'] = 1;
            } else {//议价
                $data['priceType'] = 2;
                $data['isOffprice'] = 2;
            }
            $data['isSale'] = 1;
            $data['isLicense'] = $isLicense ? 1 : 2;
        } elseif ($isLicense) {
            $data['isLicense'] = 1;
            $data['isSale'] = 2;
        } else {
            $this->returnAjax(array('code' => 2, 'msg' => '至少选择 出售与许可 中一项'));
        }
        $sale = $this->load('internal')->getSaleInfo($saleId);
        if (empty($sale)) {
            $this->returnAjax(array('code' => 2, 'msg' => '出售信息不存在'));
        } else {
            if ($sale['isTop'] < 3 && $data['isOffprice'] == 1) {
                $data['isTop'] = 3; //特价自动修改置项值
            } elseif ($sale['isTop'] == 3 && $data['isOffprice'] != 1) {
                //非特价还原置项值
                if (empty($sale['tminfo']['embellish'])) {
                    $data['isTop'] = 0;
                } else {
                    $data['isTop'] = 2;
                }
            }
        }
        $res = $this->load('internal')->update($data, $saleId);
        if ($res) {
            //修改标签
             if ($isOffprice == 1 && $salePrice > 0) {
                 $this->load('internal')->updateOffprice($sale['number'], 1, 1);
             }else{
                 $this->load('internal')->updateOffprice($sale['number'], 1, 2);
             }
            
            
            $this->load('log')->addSaleLog($saleId, 10, '商品价格已修改', serialize($data)); //修改价格信息
            $this->load('usercenter')->pushTmPrice($sale['number'], $sale, $data); //推送到用户
            $this->returnAjax(array('code' => 1, 'msg' => '操作成功'));
        }
        $this->returnAjax(array('code' => 2, 'msg' => '操作失败'));
    }

    //提交备注信息
    public function setMemo() {
        $saleId = $this->input('saleId', 'int', 0);
        $memo = $this->input('memo', 'text', '');
        if ($saleId <= 0) {
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
        }
        if ($memo == '') {
            $this->returnAjax(array('code' => 2, 'msg' => '请输入备注信息'));
        }
        $data = array(
            'memo' => $memo
        );
        $res = $this->load('internal')->update($data, $saleId);
        if ($res) {
            $this->load('log')->addSaleLog($saleId, 11, "修改备注为：$memo", 'test'); //修改备注信息
            $this->returnAjax(array('code' => 1, 'msg' => '操作成功'));
        }
        $this->returnAjax(array('code' => 2, 'msg' => '操作失败'));
    }

    //更新包装信息
    public function setEmbellish() {
        $saleId = $this->input('saleId', 'int', 0);
        if ($saleId <= 0) {
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
        }
        $type       = $this->input('type', 'text', '');
        $label      = $this->input('label', 'text', '');
        $length     = $this->input('length', 'text', '');
        $plat       = $this->input('platform', 'text', '');
        $isTop      = $this->input('isTop', 'int', 0);
        $listSort   = $this->input('listSort', 'int', 0);
        $phone      = $this->input('viewPhone', 'text', '');

        $sale = array(
            'type'      => $type,
            'label'     => $label,
            'length'    => $length,
            'platform'  => $plat,
            'isTop'     => $isTop,
            'listSort'  => $listSort,
            'viewPhone' => $phone,
        );
        $bzpic  = $this->input('bzpic', 'text', '');
        $alt1   = $this->input('alt1', 'text', '');
        $alt2   = $this->input('alt2', 'text', '');
        $tjpic  = $this->input('tjpic', 'text', '');
        $value  = $this->input('value', 'text', '');
        $intro  = $this->input('intro', 'text', '');
        $tminfo = array(
            'embellish' => $bzpic,
            'indexPic'  => $tjpic,
            'value'     => $value,
            'intro'     => $intro,
            'alt1'      => $alt1,
            'alt2'      => $alt2,
        );
        if ($isTop < 2 && !empty($tminfo['embellish'])) {
            $sale['isTop'] = 2; //有包装图片的置项值
        }
        //列表排序处理
        if ($listSort > 0 && $listSort < 222) {
            $sale['isTop'] = 0; //有列表排序就不用置顶排序
        }
        $res = $this->load('internal')->setEmbellish($saleId, $sale, $tminfo);
        if ($res) {
            //设置SEO的信息
            $sid                    = $this->input('sid', 'int', '0');
            $data['vid']            = $this->input('tid', 'int', '0');
            $data['seotitle']       = $this->input('seo_title', 'string', '');
            $data['keyword']        = $this->input('seo_keyword', 'string', '');
            $data['description']    = $this->input('seo_description', 'string', '');
            $data['isUse']          = $this->input('seo_isUse', 'int', '1');
            $reArr                  = $this->load('seo')->viewSetSeo($sid, $data, 8);
            $this->returnAjax($reArr);
        }
        $this->returnAjax(array('code' => 2, 'msg' => '操作失败'));
    }

    //检查商标是否可出售，可直接生成默认商品
    public function checkNumber() {
        $number = $this->input('number', 'text', '');
        $isAdd  = $this->input('add', 'int', 0);
        if (empty($number))
            $this->returnAjax(array('code' => 6));

        if (!$this->load('trademark')->existTm($number))
            $this->returnAjax(array('code' => 4)); //无商标信息

        $first = $this->load('trademark')->getFirst($number, 'n');
        if ($first == 3)
            $this->returnAjax(array('code' => 5)); //商标已无效

        $saleId = $this->load('internal')->existSale($number);
        if ($saleId)
            $this->returnAjax(array('code' => 2, 'id' => $saleId)); //在出售中

        
//$isBlack = $this->load('blacklist')->isBlack($number);
    //if ( $isBlack ) $this->returnAjax(array('code'=>3));//在黑名单中

        if ($isAdd) {
            //正常商标马上创建默认的出售信息
            $saleId = $this->load('internal')->addDefault($number);
            if ($saleId)
            $this->returnAjax(array('code' => 1, 'id' => $saleId));

            $this->returnAjax(array('code' => 0));
        }
        $this->returnAjax(array('code' => -1));
    }

    //删除联系人
    public function delContact() {
        $saleId = $this->input('saleId', 'int', 0);
        $id     = $this->input('id', 'int', 0);
        if ($saleId <= 0 || $id <= 0) {
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
        }
        $r['eq'] = array('id' => $id);
        $contact = $this->load('internal')->findContact($r);
        if (empty($contact)) {
            $this->returnAjax(array('code' => 2, 'msg' => '联系人不存在'));
        }
        $res = $this->load('internal')->delContact($id, $saleId, 2);
        if ($res) {
            $this->load('log')->addSaleLog($saleId, 13, "联系人ID：$id 被删除了", serialize($contact)); //删除联系人
            $this->returnAjax(array('code' => 1));
        }
        $this->returnAjax(array('code' => 2, 'msg' => '请联系人必须至少保留一个！如商品为上架状态，要保留一个审核过的联系人！'));
    }

    //审核联系人
    public function setVerify() {
        $saleId = $this->input('saleId', 'int', 0);
        $id     = $this->input('id', 'int', 0);
        if ($saleId <= 0 || $id <= 0) {
            $this->returnAjax(array('code' => 2));
        }
        $r['eq'] = array('id' => $id);
        $contact = $this->load('internal')->findContact($r);
        if (empty($contact)) {
            $this->returnAjax(array('code' => 2, 'msg' => '联系人不存在'));
        }
        $res = $this->load('internal')->setVerify($id, $saleId);
        if ($res) {
            if(!empty($contact['uid'])){
                $this->checkMsg($contact['uid']);//发送消息给用户
            }
            $this->load('log')->addSaleLog($saleId, 14, "联系人ID：$id 审核通过", serialize($contact)); //联系人审核通过
            $this->returnAjax(array('code' => 1));
        }
        $this->returnAjax(array('code' => 2));
    }

    //驳回联系人
    public function delVerify() {
        $id = $this->input('id', 'int', 0);
        //$saleId = $this->input('saleId', 'int', 0);
        $reason = $this->input('reason', 'text', '');
        if ($id <= 0) {
            $this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
        }
        if ($reason == '') {
            $this->returnAjax(array('code' => 2, 'msg' => '请填写原因'));
        }
        $r['eq'] = array('id' => $id);
        $contact = $this->load('internal')->findContact($r);
        if (empty($contact)) {
            $this->returnAjax(array('code' => 2, 'msg' => '联系人不存在'));
        }
        $res = $this->load('internal')->delVerify($id, $contact['saleId'], $reason);
        if ($res) {
            if(!empty($contact['uid'])){
                $this->checkMsg($contact['uid']);//发送消息给用户
            }
            $this->load('log')->addSaleLog($contact['saleId'], 15, "联系人ID：$id 被驳回并删除了(原因：$reason)", serialize($contact)); //联系人审核通过
            $this->returnAjax(array('code' => 1));
        }
        $this->returnAjax(array('code' => 2, 'msg' => '驳回失败了'));
    }

    protected function getSetting() {
        $saleStatus     = C("SALE_STATUS");
        $saleSource     = C("SOURCE");
        $saleType       = C("SALE_TYPE");
        $tmNums         = C("TM_NUMBER");
        $tmLabel        = C("TM_LABEL");
        $tmType         = C("TYPES");
        $salePlat       = C("SALE_PLATFORM");
        $tmPrice        = C("SEARCH_PRICE");
        $tmClass        = range(1, 45);

        $this->set('tmType', $tmType);
        $this->set('tmNums', $tmNums);
        $this->set('tmClass', $tmClass);
        $this->set('tmPrice', $tmPrice);
        $this->set('tmLabel', $tmLabel);
        $this->set('saleType', $saleType);
        $this->set('saleSource', $saleSource);
        $this->set('saleStatus', $saleStatus);
        $this->set('salePlat', $salePlat);
    }

    //导入数据弹出界面
    public function import() {
        $source = C('SOURCE');
        $this->set('source', $source);
        $this->display();
    }

    //excel文件上传
    public function ajaxUploadExcel() {
        $msg = array(
            'code'      => 0,
            'msg'       => '',
            'filename'  => '',
        );
        if (empty($_FILES) || empty($_FILES['fileName'])) {
            $msg['msg'] = '请上传EXCEL文件';
            $this->returnAjax($msg);
        }
        $obj = $this->load('upload')->uploadExcel('fileName', 'excel');
        if ($obj->fileurl) {
            $msg['code']    = 1;
            $msg['fileurl'] = $obj->fileurl;
        } else {
            $msg['msg'] = $obj->msg;
        }
        $this->returnAjax($msg);
    }

    //导入数据提交操作
    public function importForm() {
        $param['name']      = $this->input('name', 'text', '');
        $param['phone']     = $this->input('phone', 'text', ''); //电话
        $param['source']    = $this->input('source', 'text', ''); //来源
        $filePath           = $this->input('excelurl', 'text', ''); //来源
        $result             = $this->PHPExcelToArr($filePath, $param);

        $this->returnAjax($result);
    }

    //把文件里面的数据读取出来，然后组成一个数组返回  
    public function PHPExcelToArr($filePath, $param) {
        $SBarr      = $this->load('excel')->PHPExcelToArr($filePath);
        /*   * 商标已传的黑名单  不存在该商标      上传成功的  上传失败的 黑名单* */
        $saleExists = $saleNotHas = $saleSucess = $saleError = $saleNotContact = array();
        if ($SBarr) {
            if (isset($SBarr['statue']) && $SBarr['statue'] == 1) {
                $data['code']   = 0;
                $data['msg']    = '上传数量超过3000条';
            } else {
                foreach ($SBarr as $k => $item) {
                    if ((!$item['phone'] && !$param['phone']) || (!$item['name'] && !$param['name'])) {
                        $saleNotContact[] = $item;
                        continue;
                    }
                    $tmInfo = $this->load('trademark')->getTmInfo($item['number']);
                    if (!$tmInfo) {//不存在该商标
                        $saleNotHas[] = $item;
                        continue;
                    }
                    //商标已上传的
                    $saleB = $this->load('internal')->existSale($item['number']);
                    if ($saleB) {
                        $saleExists[] = $item;
                        if ($param['phone'] || $item['phone']) {
                        $phone = $param['phone'] ? $param['phone'] : $item['phone'];
                        }
                        $saleBContact = $this->load('internal')->getSaleContactByPhone($item['number'], $phone);
                        //如果没有这个联系人，就写入这个联系人信息
                        if (!$saleBContact) {
                            $dataContat = $item;
                            $dataContat['date']     = time();
                            $dataContat['phone']    = $param['phone'] ? $param['phone'] : $item['phone'];
                            $dataContat['name']     = $param['name'] ? $param['name'] : $item['name'];
                            $dataContat['number']   = $item['number'];
                            $dataContat['source']   = $param['source'];
                            $dataContat['saleType'] = 1;
                            $dataContat['tid']      = $tmInfo['tid'];
                            $dataContat['userId']   = 0;
                            $dataContat['isVerify'] = 1;
                            $dataContat['saleId']   = $saleB;
                            $result = $this->load('internal')->addContact($dataContat, $saleB);
                        }
                        continue;
                    } else {
                        //开始写入商标
                        $result = $this->load('internal')->saleZZ($tmInfo, $item, $param);
                        if ($result) {
                        $saleSucess[] = $item;
                        } else {
                        $saleError[] = $item;
                        }
                    }
                }
                $numSucess          = count($saleSucess);
                $data['code']       = 1;
                $data['alldata']    = count($SBarr);
                $data['sucdata']    = $numSucess;
                $data['errdata']    = (count($SBarr) - $numSucess);
                if ($data['errdata'] > 0) {
                    $data['filepath'] = $this->load('excel')->upErrorExcel($saleExists, $saleNotHas, $numSucess, $saleError, $saleNotContact);
                }
            }
        } else {
            //没有商标数据
            $data['code']   = 0;
            $data['msg']    = '文件没有数据';
        }

        if (file_exists(FILEDIR . $filePath)) {
            unlink(FILEDIR . $filePath);
        }
        return $data;
    }

    //导出数据提交操作
    public function excel() {
        $params = $this->getFormData();
        $list = $this->load('internal')->getExcelList($params);
        if($list['total']>5000){
            $this->redirect('导出数据超出5000条，请斟酌筛选条件！', '/internal/index');
            exit;
        }
        $result = array();

        //获取所有联系人
        foreach ($list['rows'] as $k => $v) {
            $result[$k] = $this->load('internal')->getSaleInfo($v['id']);
        }

        $excelTable = $params['excelTable'];
        $data['filepath'] = $this->load('excel')->downloadExcel($result, $excelTable);
    }
    
    /*
     * 历史交易列表
     * 
     */
    public function history() {
        $this->getSetting();
        //参数
        $page = $this->input('page', 'int', '1');

        $params['tmNumber']     = $this->input('tmNumber', 'string', '');
        $params['type']         = $this->input('type', 'int', '0');
        $params['dateStart']    = $this->input('dateStart', 'string', '');
        $params['dateEnd']      = $this->input('dateEnd', 'string', '');
        $is_export = $this->input('exports', 'int', '0');//是否导出

        $res = $this->load('internal')->getHistoryList($params, $page, $this->rowNum, $is_export);

        $total  = empty($res['total']) ? 0 : $res['total'];
        $list   = empty($res['rows']) ? array() : $res['rows'];
        $pager      = $this->pager($total, $this->rowNum);
        $pageBar    = empty($list) ? '' : getPageBar($pager);

        //导出数据
        if($is_export==1){
            $this->load('excel')->downloadHistoryExcel($list);
            exit;
        }
        
        //获取所有联系人
        foreach ($list as $k => &$v) {
            $v['info'] = unserialize($v['data']);
            $v['imgUrl'] = $this->load('internal')->saleImg($v['number']);
            $v['member'] = $this->load("member")->getMemberById($v['memberId']);
        }
        $this->set("pageBar", $pageBar);
        $this->set('s', $params);
        $this->set('saleList', $list);
        $this->display();
    }
    
    /**
     *历史交易的弹出页面
     */
    public function upcomplate() {
        $id     = $this->input('id', 'int', 0);
        //得到id对应的联系人信息
        $res    = $this->load('internal')->historyInfo($id);
        $this->set('list', unserialize($res['data']));

        $this->set('id', $id);
        $this->display();
    }
    
     /**
     * 修改历史交易人信息
     */
    public function updateHistory() {
        //得到相关参数
        $id= $this->input('id', 'int', 0);
        if ($id == 0)
            $this->returnAjax(array('code' => 1, 'msg' => '参数错误'));

        $cid    = $this->input('cid', 'int', 0);
            
        $info   = $this->load('internal')->historyInfo($id);
        $data   = unserialize($info['data']);
        $data['income']['cid'] = $cid;
        //保存到数据库中
        $datas = array(
            'data' => serialize($data),
        );
        $rst = $this->load('internal')->updateHistory($datas, $id);
        //返回结果
        if ($rst) {
            $this->returnAjax(array('code' => 0));
        } else{
            $this->returnAjax(array('code' => 1, 'msg' => '操作失败'));
        } 
    }

    public function batchVerify()
    {
        $this->display();
    }

    public function setBatchVerify()
    {
        $uid    = $this->input('uid', 'int', 0);
        if ( $uid <= 0 ) $this->returnAjax(array('code' => 0));

        $info   = $this->load('usercenter')->getUser($uid);
        if ( empty($info) ) $this->returnAjax(array('code' => 0));

        $res = $this->load('internal')->batchVerify($uid, $type);
        if ( $res ) $this->returnAjax(array('code' => 1));
        $this->returnAjax(array('code' => 0,'msg'=>'无需审核'));
    }

    //检查是否有user
    public function checkUser()
    {
        $str = $this->input('str', 'text', 0);        

        if ( isCheck($str) == 2 ){
            $uid    = $this->load('usercenter')->existUser($str);
            if ( $uid <= 0 ) $this->returnAjax(array('code' => 0));
            $mobile = $str;
        }else{
            $uid    = $this->input('str', 'int', 0);
            if ( $uid <= 0 ) $this->returnAjax(array('code' => 0));
            $info   = $this->load('usercenter')->getUser($uid);
            if ( empty($info) ) $this->returnAjax(array('code' => 0));
            $mobile = $info['mobile'];
        }
        $r['eq']    = array('status' => 3);
        $r['raw']   = " id IN (SELECT saleId FROM t_sale_contact WHERE uid = $uid && source = 12 && isVerify = 2) ";
        $noVerify   = $this->load('internal')->countSale($r);
        $flag = array(
            'code'      => 1,
            'uid'       => $uid,
            'mobile'    => $mobile,
            'count'     => $noVerify,
            );
        $this->returnAjax($flag);
    }

    /**
     * 更新商标数据--抓取最新数据
     */
    public function updateTm(){
        $number = $this->input('number','string');
        $rst = $this->load('internal')->updateTm($number);
        if($rst){
            $this->returnAjax(array('code'=>0));
        }else{
            $this->returnAjax(array('code'=>1,'msg'=>'更新数据失败'));
        }
    }

}

?>