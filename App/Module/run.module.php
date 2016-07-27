<?
class RunModule extends AppModule
{
    public $models = array(
        //'ts'        => 'tsale',
        //'tst'       => 'tsaleTrademark',
        'sale'      => 'sale',
        'contact'   => 'saleContact',
        'patent'    => 'patent',
        'ptinfo'    => 'patentInfo',
        'ptlist'    => 'patentList',
        'ptcontact' => 'patentContact',
        'test'      => 'test',
    );

    public function deleteNoPhoneContact($saleId=0)
    {
        $r = array();
        $r['raw']   = " saleId > $saleId ";
        $r['eq']    = array('source'=>'9','phone'=>'');
        $r['group'] = array('saleId'=>'asc');
        $r['col']   = array('saleId','count(id) as num','group_concat(id) as ids');
        $r['limit'] = '10000';
        $data = $this->import('contact')->find($r);
        $success = $faild = array();
        $_saleId = '';
        foreach ($data as $k => $v) {
            $rl         = array();
            $rl['eq']   = array('saleId'=>$v['saleId']);

            $count = $this->import('contact')->count($rl);

            if ( $count > $v['num'] ){
                $success[$v['saleId']] = $v['ids'];
                $role = array('in'=>array('id'=>array_filter(explode(',', $v['ids']))));
                $this->import('contact')->remove($role);
            }else{
                $faild[$v['saleId']] = $v['ids'];
            }

            if ( $k == (count($data)-1) ){
                echo "\n saleId = ".$v['saleId'];
            }
        }
        echo "<pre>";
        echo "\n success Data: \n";
        print_r($success);

        echo "\n faild Data: \n";
        print_r($faild);

        $_log = array(
            'saleId'    => $_saleId,
            'success'   => $success,
            'faild'     => $faild,
        );
        $name = 'deleteContact-'.date('Y-m-d-H-i').randCode(4).'.log';

        Log::write(print_r($_log,1), $name);
    }

    // public function importOp()
    // {
    //     $r['limit'] = 10000;
    //     $list = $this->import('test')->find($r);
    //     if ( empty($list) ) exit('no data.');
    //     $id = 30001;
    //     foreach ($list as $k => $v) {
    //         $type       = $v['number'];
    //         $number     = $v['department'];
    //         $advisor    = $v['type'];
    //         $t1         = empty($v['price'])?'':'联系方式：'.$v['price'].';';
    //         $t2         = empty($v['memo'])?'':'价格：'.$v['memo'].';';
    //         $t3         = empty($v['advisor'])?'':'QQ：'.$v['advisor'].';';

    //         $data = array(
    //             'id'            => $id,
    //             'number'        => $number,
    //             'type'          => $type,
    //             'price'         => 0,
    //             'advisor'       => $advisor,
    //             'department'    => '',
    //             'memo'          => $t1.$t2.$t3,
    //             );
    //         $rl['eq'] = array('id'=>$v['id']);
    //         $this->import('test')->modify($data, $rl);

    //         $id++;
    //     }
    // }
    // public function importOp()
    // {
    //     $r['limit'] = 10000;
    //     $list = $this->import('test')->find($r);
    //     if ( empty($list) ) exit('no data.');
    //     $id = 1;
    //     foreach ($list as $k => $v) {
    //         $data = array(
    //             'id'            => $id,
    //             );
    //         $rl['eq'] = array('id'=>$v['id']);
    //         $this->import('test')->modify($data, $rl);

    //         $id++;
    //     }
    // }

    private function msectime() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    //获取单个专利数据
    public function getPatentInfo($number)
    {
        if ( empty($number) ) return array();

        $eq = (strpos($number, '.') !== false) ? array('number'=>$number) : array('code'=>$number);

        $r['eq']    = $eq;
        $r['limit'] = 1;
        $info = $this->import('ptlist')->find($r);
        if ( !empty($info['data']) ){
            $data = unserialize($info['data']);
            return $data;
        }
        
        $code   = (strpos($number, '.') !== false) ? strstr($number, '.', true) : $number;
        $url    = 'http://wanxiang.chaofan.wang/detail.php?id=%s&t=json';

        $url    = sprintf($url, $code);
        $data   = $this->requests($url);
        if ( !empty($data) && !empty($data['id']) ){            
            $_data = array(
                'code'      => $code,
                'number'    => $number,
                'data'      => serialize($data),
                );
            $this->import('ptlist')->create($_data);
        } 
        return $data;
    }

    //导入专利数据
    public function importPt($ids=array())
    {
        $num        = 0;
        $succList   = $faildList = array();
        $start      = $this->msectime();
        $rand       = randCode(6);

        $list   = $this->getPatentList($ids);
        foreach ($list as $k => $v) {
            $num++;
            $number     = trim($v['number']);
            $source     = trim($v['type']);
            $saleType   = '1';//出售类型，1：出售，2：许可，3：出+许
            $isVerify   = '1';//审核
            $advisor    = trim($v['advisor']);//顾问名称
            $department = trim($v['department']);//顾问部门
            $memo       = trim($v['memo']);//备注 
            $name       = trim($v['name']);
            $phone      = trim($v['phone']);
            $price      = trim($v['price']);
            
            $number = strtolower($number);//专利编号 带.
            $info   = $this->getPatentInfo($number);

            if ( empty($info) || empty($info['id']) ){
                $faildList[] = $number;
                $faildLists[] = array('number'=>$number,'msg'=>'未查询到专利');
                continue;
            }

            $code       = $info['id'];
            $patentId   = $this->isSale($number);
            if ( $patentId ) {
                if ( $this->isContact($patentId, $phone) ) {
                    $faildList[] = $number;
                    $faildLists[] = array('number'=>$number,'msg'=>'联系人重复');
                    continue;
                }

                $contact = array(
                    'patentId'      => $patentId,
                    'source'        => $source,
                    'number'        => $number,
                    'code'          => $code,
                    'name'          => $name,
                    'phone'         => $phone,
                    'price'         => $price,
                    'saleType'      => $saleType,
                    'isVerify'      => $isVerify,
                    'advisor'       => $advisor,
                    'department'    => $department,
                    'date'          => time(),
                    'memo'          => $memo,
                    );
                $flag = $this->import('ptcontact')->create($contact);
                if ( $flag ) {
                    $succList[] = $number;
                }else{
                    $faildList[] = $number;
                    $faildLists[] = array('number'=>$number,'msg'=>'联系人插入失败');
                }
                continue;
            }

            $_class = array();
            $_group = array();
            foreach ($info['ipcs'] as $ky => $val) {
                array_push($_class, current($val['ancestors']));
                array_push($_group, $val['id']);
                $_group = array_merge($_group, (array)$val['ancestors']);
            }
            $group = implode(',', array_unique(array_filter($_group)));//专利所有群组

            $title  = $info['title']['original'] ? $info['title']['original'] : $info['title']['zh-cn'];//专利标题
            $type   = 0;//专利类型
            if ( strpos($info['typeName'], '发明') !== false ){
                $type = 1;
            }elseif ( strpos($info['typeName'], '新型') !== false || strpos($info['typeName'], '实用') !== false ){
                $type = 2;
            }elseif ( strpos($info['typeName'], '外观') !== false ){
                $type = 3;
            }
            $_class = array_unique(array_filter($_class));
            if ( $type == 3 ){
                $class  = implode(',', $_class);
            }else{
                if ( empty($_class) ){
                    $_class = array();
                    foreach (explode(',', $group) as $ky => $val) {
                        $_class[] = strtolower( substr($val,0,1) );
                    }
                }
                $_class = array_map('strtolower', $_class);
                $class  = empty($_class) ? '' : implode(',', array_map('ord', $_class));
            }
            

            $applyDate  = (int)strtotime($info['application_date']);//申请日
            $publicDate = (int)strtotime($info['earliest_publication_date']);//最早公开日
            $viewPhone  = $this->load('phone')->getRandPhone();
            $_memo      = '后台程序默认创建';
            $patent = array(
                'number'        => $number,
                'code'          => $code,
                'class'         => $class,
                'group'         => $group,
                'title'         => $title,
                'status'        => '1',
                'type'          => $type,
                'applyDate'     => $applyDate,
                'publicDate'    => $publicDate,
                'date'          => time(),
                'viewPhone'     => $viewPhone,
                'memo'          => $_memo,
                );

            $ptinfo = array(
                'number'    => $number,
                'code'      => $code,
                'intro'     => '',
                );

            $contact = array(
                'source'        => $source,
                'number'        => $number,
                'code'          => $code,
                'name'          => $name,
                'phone'         => $phone,
                'price'         => $price,
                'saleType'      => $saleType,
                'isVerify'      => $isVerify,
                'advisor'       => $advisor,
                'department'    => $department,
                'date'          => time(),
                'memo'          => $memo,
                );

            $this->begin('patent');//开始事务

            $patentId   = $this->import('patent')->create($patent);
            if ( $patentId > 0 ){                
                $ptinfo['patentId'] = $contact['patentId'] = $patentId;
                $flag1      = $this->import('ptinfo')->create($ptinfo);
                $flag2      = $this->import('ptcontact')->create($contact);
                if ( $flag1 && $flag2 ){
                    $flag = true;
                }else{
                    $flag = false;
                }
            }else{
                $flag = false;
            }            

            if ( $flag ) {
                $this->commit('patent');
                $succList[] = $number;
            }else{
                $this->rollBack('patent');
                $faildList[] = $number;
                $faildLists[] = array('number'=>$number,'msg'=>'专利数据插入失败');
            }
        }

        $end    = $this->msectime() - $start;
        $log = array(
            'useTime'   => $end,
            );
        $_name_ = 'importPt';
        if ( count($succList) > 0 ){
            $_log = $log;
            $_log['succ']       = count($succList);
            $_log['succList']   = $succList;
            $name = $_name_.date("Y-m-d")."($rand)-list-----success.log";
            Log::write(print_r($_log,1), $name);
        }
        if ( count($faildList) > 0 ){
            $_log = $log;
            $_log['faild']       = count($faildList);
            $_log['faildList']   = $faildList;
            $_log['msg']         = $faildLists;
            $name = $_name_.date("Y-m-d")."($rand)-list-----faild.log";
            Log::write(print_r($_log,1), $name);
        }
        echo "list-$p finish...\n";
    }

    //获取所有待处理的专利
    private function getPatentList($ids=array())
    {
        if ( !empty($ids) ){
            $r['in'] = array('number'=>$ids);
        }
        //$r['raw']   = " id > 4895";
        $r['limit'] = 1000000;
        return $this->import('test')->find($r);
    }

    //判断专利是否出售
    private function isSale($number)
    {
        $r['limit'] = 1;
        $r['eq']    = array('number'=>$number);
        $r['col']   = array('id');
        $res = $this->import('patent')->find($r);
        return $res['id'] > 0 ? $res['id'] : 0;
    }

    //判断专利是否出售
    private function isContact($patentId, $phone)
    {
        $r['limit'] = 1;
        $r['eq']    = array('patentId'=>$patentId,'phone'=>$phone);
        $r['col']   = array('id');
        $res = $this->import('ptcontact')->find($r);
        return $res['id'] > 0 ? true : false;
    }


    //更新没有商品名称的数据
    public function runName()
    {
        $id     = 0;
        $num    = 0;
        $succList   = $faildList = array();
        $start      = $this->msectime();
        $this->begin('sale');
        $rand = randCode(6);
        while ( true ) {
            $num++;
            $sale   = $this->getEmptyName($id);

            if ( empty($sale['id']) ){
                $p      = intval($num/1000) > 0 ? intval($num/1000) + 1 : 1;
                $end    = $this->msectime() - $start;
                if ( empty($succList) && empty($faildList) ){
                    exit('no data to update');
                }
                $this->commit('sale');

                $log = array(
                    'useTime'   => $end,
                    );
                if ( count($succList) > 0 ){
                    $_log = $log;
                    $_log['succ']       = count($succList);
                    $_log['succList']   = $succList;
                    $name = 'updateName'.date("Y-m-d")."($rand)-list$p-----success.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ( count($faildList) > 0 ){
                    $_log = $log;
                    $_log['faild']       = count($faildList);
                    $_log['faildList']   = $faildList;
                    $name = 'updateName'.date("Y-m-d")."($rand)-list$p-----faild.log";
                    Log::write(print_r($_log,1), $name);
                }
                exit('no data to update');
            }

            $id         = $sale['id'];
            $number     = $sale['number'];
            $info       = $this->load('trademark')->getInfo($number, array('trademark as name'));
            //$regDate    = strtotime($info['reg_date']) > 0 ? strtotime($info['reg_date']) : 0;

            if ( empty($info['name']) ){
                $faildList[] = $number;
            }else{
                $data       = array('name' => $info['name']);
                $r['eq']    = array('id'=>$id);
                $flag       = $this->import('sale')->modify($data, $r);

                if ( $flag ) {
                    $succList[] = $number;
                }else{
                    $faildList[] = $number;
                }
            }  

            if ( $num % 1000 == 0 ){
                $this->commit('sale');
                $end    = $this->msectime() - $start;
                $start  = $this->msectime();
                $p      = intval($num/1000) > 0 ? intval($num/1000) : 1;

                $log = array(
                    'useTime'   => $end,
                    );
                if ( count($succList) > 0 ){
                    $_log = $log;
                    $_log['succ']       = count($succList);
                    $_log['succList']   = $succList;
                    $name = 'updateName'.date("Y-m-d")."($rand)-list$p-----success.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ( count($faildList) > 0 ){
                    $_log = $log;
                    $_log['faild']       = count($faildList);
                    $_log['faildList']   = $faildList;
                    $name = 'updateName'.date("Y-m-d")."($rand)-list$p-----faild.log";
                    Log::write(print_r($_log,1), $name);
                }
                echo "list-$p finish...\n";
                $succList   = $faildList = array();
                $this->begin('sale');
            }
        }
    }

    public function runRegDate()
    {
        $id     = 0;
        $num    = 0;
        $succList   = $faildList = array();
        $start      = $this->msectime();
        $this->begin('sale');
        $rand = randCode(6);
        while ( true ) {
            $num++;
            $sale   = $this->getRegDate($id);

            if ( empty($sale['id']) ){
                $p      = intval($num/1000) > 0 ? intval($num/1000) + 1 : 1;
                $end    = $this->msectime() - $start;
                if ( empty($succList) && empty($faildList) ){
                    exit('no data to update');
                }
                $this->commit('sale');

                $log = array(
                    'useTime'   => $end,
                    );
                if ( count($succList) > 0 ){
                    $_log = $log;
                    $_log['succ']       = count($succList);
                    $_log['succList']   = $succList;
                    $name = 'update'.date("Y-m-d")."($rand)-list$p-----success.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ( count($faildList) > 0 ){
                    $_log = $log;
                    $_log['faild']       = count($faildList);
                    $_log['faildList']   = $faildList;
                    $name = 'update'.date("Y-m-d")."($rand)-list$p-----faild.log";
                    Log::write(print_r($_log,1), $name);
                }
                exit('no data to update');
            }

            $id         = $sale['id'];
            $number     = $sale['number'];
            $info       = $this->load('trademark')->getTmInfo($number);
            $regDate    = strtotime($info['reg_date']) > 0 ? strtotime($info['reg_date']) : 0;

            if ( $regDate == 0 ){
                $faildList[] = $number;
            }else{
                $data       = array('regDate' => $regDate);
                $r['eq']    = array('id'=>$id);
                $flag       = $this->import('sale')->modify($data, $r);

                if ( $flag ) {
                    $succList[] = $number;
                }else{
                    $faildList[] = $number;
                }
            }  

            if ( $num % 1000 == 0 ){
                $this->commit('sale');
                $end    = $this->msectime() - $start;
                $start  = $this->msectime();
                $p      = intval($num/1000) > 0 ? intval($num/1000) : 1;

                $log = array(
                    'useTime'   => $end,
                    );
                if ( count($succList) > 0 ){
                    $_log = $log;
                    $_log['succ']       = count($succList);
                    $_log['succList']   = $succList;
                    $name = 'update'.date("Y-m-d")."($rand)-list$p-----success.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ( count($faildList) > 0 ){
                    $_log = $log;
                    $_log['faild']       = count($faildList);
                    $_log['faildList']   = $faildList;
                    $name = 'update'.date("Y-m-d")."($rand)-list$p-----faild.log";
                    Log::write(print_r($_log,1), $name);
                }
                echo "list-$p finish...\n";
                $succList   = $faildList = array();
                $this->begin('sale');
            }
        }
    }

    private function getRegDate($id=0)
    {
        $r['limit'] = 1;
        $r['order'] = array('id'=>'asc');
        $r['eq']    = array('regDate'=>0);
        $r['raw']   = " id > $id ";
        $r['col']   = array('number','id');
        return $this->import('sale')->find($r);
    }

    private function getEmptyName($id=0)
    {
        $r['limit'] = 1;
        $r['order'] = array('id'=>'asc');
        $r['eq']    = array('name'=>'');
        $r['raw']   = " id > $id ";
        $r['col']   = array('number','id');
        return $this->import('sale')->find($r);
    }

    private function update($page)
    {
        $start = $this->msectime();
        $res = $this->getNewSale($page);
        if ( count($res['rows']) <= 0 ){
            exit('no data to update');
        }
        $succ = $faild = 0;
        $list = $res['rows'];
        $succList = $faildList = array();
        //$this->begin('sale');
        foreach ($list as $k => $v) {
            $number = $v['number'];
            $info   = $this->load('trademark')->getTmInfo($number);
            if ( empty($info) ){
                exit("<number is not a trademark>");
            }
            $regDate = strtotime($info['reg_date']) > 0 ? strtotime($info['reg_date']) : 0;
            if ( $regDate == 0 ){
                $faild++;
                $faildList[] = $number;
                continue;
            }

            $data = array(
                'regDate' => $regDate,
            );

            $r['eq'] = array('id'=>$v['id']);
            $flag = $this->import('sale')->modify($data, $r);

            if ( $flag ) {
                //$this->commit('sale');
                $succ++;
                $succList[] = $number;
            }else{
                //$this->rollBack('sale');
                $faild++;
                $faildList[] = $number;
            }
        }
        $this->commit('sale');
        $end = $this->msectime() - $start;
        $log = array(
            'useTime'   => $end,
            );
        if ( count($succList) > 0 ){
            $_log = $log;
            $_log['succ']       = count($succList);
            $_log['succList']   = $succList;
            $name = 'update'.date("Y-m-d").'-p'.$page.'-----success.log';
            Log::write(print_r($_log,1), $name);
        }
        if ( count($faildList) > 0 ){
            $_log = $log;
            $_log['faild']       = count($faildList);
            $_log['faildList']   = $faildList;
            $name = 'update'.date("Y-m-d").'-p'.$page.'-----faild.log';
            Log::write(print_r($_log,1), $name);
        }
        echo "finish!!!";
    }

    private function run($page)
    {
        $start = $this->msectime();
        $res = $this->load('run')->getSaleNumber($page);
        $saleList = $res['rows'];
        //debug($saleList);
        $succ = $faild = 0;
        $succList = $faildList = array();
        //$this->begin('sale');
        foreach ($saleList as $k => $v) {
            $number = $v['number'];
            $flag = $this->runSale($number);
            if ( $flag ) {
                //$this->commit('sale');
                $succ++;
                $succList[] = $number;
            }else{
                //$this->rollBack('sale');
                $faild++;
                $faildList[] = $number;
            }
        }
        $end = $this->msectime() - $start;
        $log = array(
            'useTime'   => $end,
            );
        if ( count($succList) > 0 ){
            $_log = $log;
            $_log['succ']       = count($succList);
            $_log['succList']   = $succList;
            $name = 'task'.date("Y-m-d").'-p'.$page.'-----success.log';
            Log::write(print_r($_log,1), $name);
        }
        if ( count($faildList) > 0 ){
            $_log = $log;
            $_log['faild']       = count($faildList);
            $_log['faildList']   = $faildList;
            $name = 'task'.date("Y-m-d").'-p'.$page.'-----faild.log';
            Log::write(print_r($_log,1), $name);
        }
        echo "finish!!!";
    }

    //单条执行（已关闭）
    private function runSale($number, $debug=0)
    {
        $number = trim( $number );
        $resTm  = $this->load('run')->getTminfo($number);
        $list    = $this->load('run')->getSale($number);

        $type = $label = $platform = $length = array();
        $price      = 0;//指导价格
        $priceType  = 2;//价格类型 1定价，2议价
        $isOffprice = 2;//是否特价
        $salePrice  = 0;//特价价格
        $salePriceDate = 0;//特价时间

        $status = 1;//销售中
        $isTop  = 0;//不置项
        $date   = 0;//出售时间
        $hits   = 0;//阅读数
        $memo   = '';

        $info   = $this->load('trademark')->getTmInfo($number);
        if ( empty($info) ){
            if ( $debug ) echo "<number is not a trademark>";
            return false;
        } 

        $class  = implode(',', $info['class']);

        $contact = array();//联系人
        foreach ($list as $ky => $val) {
            $type       = array_merge($type, explode(',', $val['types']));
            $label      = array_merge($label, explode(',', $val['label']));
            $length     = array_merge($length, explode(',', $val['sblength']));
            $platform   = array_merge($platform, explode(',', $val['platform']));

            if ( $val['isTop'] == 1 ){
                $isTop  = 1;//置项
            }
            if ( $val['date'] > 0 ){
                $date = $date <= 0 ? $val['date'] : $date;
                if ( $date > $val['date'] ){
                    $date = $val['date'];
                }
            }
            $hits += $val['hits'];
            if ( $price < $val['guideprice'] ){
                $price = $val['guideprice'];
            }
            if ( $price < $val['guideprice'] ){
                $price = $val['guideprice'];
            }
            if ( $val['salePrice'] > 0 ){
                $isOffprice     = 1;//特价
                $salePrice      = $salePrice <= 0 ? $val['salePrice'] : $salePrice;
                $salePriceDate  = $salePriceDate <= 0 ? $val['salePriceDate'] : $salePriceDate;
                if ( $salePrice > $val['salePrice'] ){
                    $salePrice      = $val['salePrice'];
                    $salePriceDate  = $val['salePriceDate'];
                }
            }
            if ( !empty($val['memo']) ){
                $memo .= ' | '.$val['memo'];
            }

            $contact[] = array(
                'source'        => intval($val['source']),
                'userId'        => intval($val['userId']),
                'tid'           => intval($val['tid']),
                'number'        => $number,
                'name'          => trim($val['contact']),
                'phone'         => trim($val['phone']),
                'price'         => $val['price'],
                'saleType'      => 1,
                'isVerify'      => 1,
                'advisor'       => $val['offerman'],
                'department'    => $val['department'],
                'date'          => $val['date'],
                );
        }
        $priceType  = $price > 0 ? 1 : 2;

        $type       = implode(',', array_unique(array_filter($type)));//商标类型（中、英。。。）
        $label      = implode(',', array_unique(array_filter($label)));//商标标签（精品、紧急。。。）
        $length     = implode(',', array_unique(array_filter($length)));//商标字数（1，2，3，4，5.。。）
        $platform   = implode(',', array_unique(array_filter($platform)));//商标可入驻的平台（天猫、京东。。。）
        $viewPhone  = $this->load('phone')->getRandPhone();
        $sale = array(
            'tid'           => intval($info['tid']),
            'number'        => $number,
            'class'         => $class,
            'group'         => trim($info['group']),
            'name'          => trim($info['name']),
            'pid'           => intval($info['pid']),
            'price'         => $price,
            'priceType'     => $priceType,
            'isOffprice'    => $isOffprice,
            'salePrice'     => $salePrice,
            'salePriceDate' => $salePriceDate,
            'status'        => $status, 
            'isSale'        => 1,
            'isLicense'     => 2,
            'isTop'         => $isTop,
            'type'          => $type,
            'platform'      => $platform,
            'label'         => $label,
            'length'        => $length,
            'date'          => $date,
            'viewPhone'     => $viewPhone,
            'hits'          => intval($hits),
            'memo'          => $memo,
            );
        
        $tminfo = array(
            //'tid'       => $info['tid'],
            'number'    => $number,
            'embellish' => $resTm['bzpic'],
            'indexPic'  => $resTm['tjpic'],
            'value'     => $resTm['valueAnalysis'],
            'intro'     => $resTm['intro'],
            'memo'      => $resTm['closeReason'],
            );

        $data = array(
            'sale'          => $sale,
            'saleTminfo'    => $tminfo,
            'saleContact'   => $contact,
            );
        //debug($data);
        $res = $this->load('internal')->addAll($data);
        if ( !$res && $debug ) {
            echo "<pre><add error>";
            print_r($data);
            echo "</pre>";
        }
        return $res;
    }

    private function getNewSale($page=1, $limit=1000)
    {
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['raw']   = ' regDate = 0 ';
        $r['col']   = array('number','id');
        return $this->import('sale')->findAll($r);
    }

    private function getSaleNumber($page=1, $limit=500)
    {
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['in']    = array('status'=>array(1,5));
        $r['raw']   = " number != '' ";
        //$r['group'] = array('number'=>'asc');
        $r['col']   = array('DISTINCT `number`');
        return $this->import('ts')->findAll($r);
    }

    private function getSale($number)
    {
        $r['eq']    = array('number'=>$number);
        $r['limit'] = 1000;
        //$r['order'] = array('number'=>'asc');
        return $this->import('ts')->find($r);
    }

    private function getTminfo($number)
    {
        $r['eq']    = array('number'=>$number);
        //$r['limit'] = 1000;
        $r['order'] = array('bzpic'=>'desc');
        return $this->import('tst')->find($r);
    }

    


    public function requests( $url, $type='GET', $params=array() )
    {
        $_type = ($type == 'POST') ? 'POST' : 'GET';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_type);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt(
            $ch, CURLOPT_POSTFIELDS, $params
        );
        $result = curl_exec($ch);
        
        if($result === false) {
            $result = curl_error($ch);
        }
        curl_close($ch);
        $res =  json_decode(trim($result,chr(239).chr(187).chr(191)),true);
        return $res;
    }

}
?>