<?
class RunModule extends AppModule
{
    public $models = array(
        'ts'    => 'tsale',
        'tst'   => 'tsaleTrademark',
        'sale'  => 'sale',
    );

    private function msectime() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
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

}
?>