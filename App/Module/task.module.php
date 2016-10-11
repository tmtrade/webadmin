<?
/*
 * 定时任务处理类
 */
class TaskModule extends AppModule
{
    public $models = array(
        'sale'      => 'sale',
        'contact'   => 'saleContact',
        'tminfo'    => 'saleTminfo',
        'setting'   => 'systemSetting',
    );

    private $soon;

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

    /*
     * 更新注册日期为0的商品
     * @author   Xuni
     */
    public function runRegDate($type=1)
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
                    if ($type==1) exit('no data to update');
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
                if ($type==1) exit('no data to update');
                break;
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
                if ($type==1) echo "list-$p finish...\n";
                $succList   = $faildList = array();
                $this->begin('sale');
            }
        }
        return true;
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


    /*
     * 更新有效期截止日期为0的商品
     * @author   Xuni
     */
    public function runEndDate($type=1)
    {
        $id     = 0;
        $num    = 0;
        $succList   = $faildList = array();
        $start      = $this->msectime();
        $this->begin('sale');
        $rand = randCode(6);
        while ( true ) {
            $num++;
            $sale   = $this->getEndDate($id);

            if ( empty($sale['id']) ){
                $p      = intval($num/1000) > 0 ? intval($num/1000) + 1 : 1;
                $end    = $this->msectime() - $start;
                if ( empty($succList) && empty($faildList) ){
                    if ($type==1) exit('no data to update');
                }
                $this->commit('sale');

                $log = array(
                    'useTime'   => $end,
                );
                if ( count($succList) > 0 ){
                    $_log = $log;
                    $_log['succ']       = count($succList);
                    $_log['succList']   = $succList;
                    $name = 'update_endData_'.date("Y-m-d")."($rand)-list$p-----success.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ( count($faildList) > 0 ){
                    $_log = $log;
                    $_log['faild']       = count($faildList);
                    $_log['faildList']   = $faildList;
                    $name = 'update_endData_'.date("Y-m-d")."($rand)-list$p-----faild.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ($type==1) exit('no data to update');
                break;
            }

            $id         = $sale['id'];
            $number     = $sale['number'];
            $info       = $this->load('trademark')->getTmInfo($number);
            $endDate    = strtotime($info['valid_end']) > 0 ? strtotime($info['valid_end']) : 0;

            if ( $endDate == 0 ){
                $faildList[] = $number;
            }else{
                $data       = array('endDate' => $endDate);
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
                    $name = 'update_endData_'.date("Y-m-d")."($rand)-list$p-----success.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ( count($faildList) > 0 ){
                    $_log = $log;
                    $_log['faild']       = count($faildList);
                    $_log['faildList']   = $faildList;
                    $name = 'update_endData_'.date("Y-m-d")."($rand)-list$p-----faild.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ($type==1) echo "list-$p finish...\n";
                $succList   = $faildList = array();
                $this->begin('sale');
            }
        }
        return true;
    }

    private function getEndDate($id=0)
    {
        $r['limit'] = 1;
        $r['order'] = array('id'=>'asc');
        $r['eq']    = array('endDate'=>0);
        $r['raw']   = " id > $id ";
        $r['col']   = array('number','id');
        return $this->import('sale')->find($r);
    }

    /*
     * 处理即将到续展期的商品
     * @author   Xuni
     */
    public function runSoonFallDue($type=0)
    {
        $day = (int)$this->getSetting('soon_fall_due');
        if ( $day == 0 ) {
            $this->updateSetting('soon_fall_due', 730);
            $day = 730;
        }
        $this->soon = $day;

        $id     = 0;
        $num    = 0;
        $succList   = $faildList = array();
        $start      = $this->msectime();
        $this->begin('sale');
        $rand = randCode(6);
        while ( true ) {
            $num++;
            $sale   = $this->getSoonGoods($id);

            if ( empty($sale['id']) ){
                $p      = intval($num/1000) > 0 ? intval($num/1000) + 1 : 1;
                $end    = $this->msectime() - $start;
                if ( empty($succList) && empty($faildList) ){
                    if ($type==1) exit('no data to update');
                }
                $this->commit('sale');

                $log = array(
                    'useTime'   => $end,
                );
                if ( count($succList) > 0 ){
                    $_log = $log;
                    $_log['succ']       = count($succList);
                    $_log['succList']   = $succList;
                    $name = 'update_SoonFallDue_'.date("Y-m-d")."($rand)-list$p-----success.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ( count($faildList) > 0 ){
                    $_log = $log;
                    $_log['faild']       = count($faildList);
                    $_log['faildList']   = $faildList;
                    $name = 'update_SoonFallDue_'.date("Y-m-d")."($rand)-list$p-----faild.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ($type==1) exit('no data to update');
                break;
            }

            $id         = $sale['id'];
            $number     = $sale['number'];

            $offList    = explode(',', $sale['offprice']);
            $offList[]  = 2;
            $offList    = array_filter( array_unique($offList) );
            sort($offList);

            $data['offprice'] = implode(',', $offList);
            $r['eq']    = array('id'=>$id);
            $flag       = $this->import('sale')->modify($data, $r);

            if ( $flag ) {
                $succList[] = $number;
            }else{
                $faildList[] = $number;
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
                    $name = 'update_SoonFallDue_'.date("Y-m-d")."($rand)-list$p-----success.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ( count($faildList) > 0 ){
                    $_log = $log;
                    $_log['faild']       = count($faildList);
                    $_log['faildList']   = $faildList;
                    $name = 'update_SoonFallDue_'.date("Y-m-d")."($rand)-list$p-----faild.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ($type==1) echo "list-$p finish...\n";
                $succList   = $faildList = array();
                $this->begin('sale');
            }
        }
        return true;
    }

    private function getSoonGoods($id=0)
    {
        $day    = $this->soon ? $this->soon : 730;
        $time   = strtotime("+{$day} days");

        $r['limit'] = 1;
        $r['order'] = array('id'=>'asc');
        $r['raw']   = "id > $id AND endDate > 0 AND NOT MATCH (`offprice`) AGAINST ('2') AND $time > endDate ";
        $r['col']   = array('number','id', 'offprice');
        return $this->import('sale')->find($r);
    }

    /*
     * 处理商品小于等于5项
     * @author   Xuni
     */
    public function runGoodsLessFive($type=0)
    {
        $id         = (int)$this->getSetting('goods_less_five');
        $num        = 0;
        $succList   = $faildList = array();
        $start      = $this->msectime();
        $this->begin('sale');
        $rand = randCode(6);
        while ( true ) {
            $num++;
            $sale   = $this->getGreaterID($id);

            if ( empty($sale['id']) ){
                $p      = intval($num/1000) > 0 ? intval($num/1000) + 1 : 1;
                $end    = $this->msectime() - $start;
                if ( empty($succList) && empty($faildList) ){
                    if ($type==1) exit('no data to update');
                }
                $this->commit('sale');

                $log = array(
                    'useTime'   => $end,
                );
                if ( count($succList) > 0 ){
                    $_log = $log;
                    $_log['succ']       = count($succList);
                    $_log['succList']   = $succList;
                    $name = 'update_GoodsLessFive_'.date("Y-m-d")."($rand)-list$p-----success.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ( count($faildList) > 0 ){
                    $_log = $log;
                    $_log['faild']       = count($faildList);
                    $_log['faildList']   = $faildList;
                    $name = 'update_GoodsLessFive_'.date("Y-m-d")."($rand)-list$p-----faild.log";
                    Log::write(print_r($_log,1), $name);
                }
                $this->updateSetting('goods_less_five', $id);
                if ($type==1) exit('no data to update');
                break;
            }

            $id         = $sale['id'];
            $number     = $sale['number'];
            $info       = $this->load('trademark')->getTmInfo($number);
            $goodsList  = array_filter( explode(';', $info['goods']) );

            if ( count($goodsList) > 5 ){
                $faildList[] = $number;
            }else{
                $offList    = explode(',', $sale['offprice']);
                $offList[]  = 3;
                $offList    = array_filter( array_unique($offList) );
                sort($offList);

                $data['offprice'] = implode(',', $offList);
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
                    $name = 'update_GoodsLessFive_'.date("Y-m-d")."($rand)-list$p-----success.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ( count($faildList) > 0 ){
                    $_log = $log;
                    $_log['faild']       = count($faildList);
                    $_log['faildList']   = $faildList;
                    $name = 'update_GoodsLessFive_'.date("Y-m-d")."($rand)-list$p-----faild.log";
                    Log::write(print_r($_log,1), $name);
                }
                if ($type==1) echo "list-$p finish...\n";
                $succList   = $faildList = array();
                $this->updateSetting('goods_less_five', $id);
                sleep(1);//每一千条记录等待一秒
                $this->begin('sale');
            }
        }
        return true;
    }

    /*
     * 获取大于ID的出售信息
     * @author   Xuni
     */
    private function getGreaterID($id=0)
    {
        $r['limit'] = 1;
        $r['order'] = array('id'=>'asc');
        $r['raw']   = " id > $id ";
        $r['col']   = array('number', 'id', 'offprice');
        return $this->import('sale')->find($r);
    }

    public function runOffpriceGoods($type=0)
    {
        $list = $this->getOffpriceGoods();
        if ( empty($list) ) {
            $type == 1 && exit('no data');
            return false;
        }

        $succList   = $faildList = array();
        $start      = $this->msectime();
        //$rand       = randCode(6);

        foreach ($list as $info){
            $data = array('id'=>$info['id']);
            $flag = $this->load('queuelib')->addQueue('offpriceDown', $data, 'offpriceDown_'.$info['number']);
            if ( $flag === false ){
                $faildList[] = $info['number'];
            }else{
                $succList[] = $info['number'];
            }
        }
        $end    = $this->msectime() - $start;
        $message = array(
            'succList'      => $succList,
            'faildList'    => $faildList,
            'opTime'        => $end,
        );
        $logs = array(
            'type'      => 4,
            'action'    => 21,
            'status'    => 1,
            'data'      => $message,
            'desc'      => 'trade_runOffpriceGoods',
            'created'   => time(),
            'memo'      => '添加特价到期数据放入队列中',
        );
        $this->load('log')->addSystemLog($logs);

        $type == 1 && exit('run finished');
        return true;
    }

    /*
     * 获取已到期的特价商品数据
     */
    private function getOffpriceGoods()
    {
        $time = time();
        $r['eq'] = array(
            'priceType'     => 1,
            'isOffprice'    => 1,
        );
        $r['raw']   = " `salePriceDate` > 0 AND `salePriceDate` < $time ";
        $r['limit'] = 2000;//特价不需要超过2000个
        $r['col']   = array('id', 'number');
        return $this->import('sale')->find($r);
    }

    /*
     * 获取配置
     * @author   Xuni
     */
    private function getSetting($key)
    {
        if ( empty($key) ) return false;

        $r['eq']    = array('key'=>$key);
        $r['limit'] = 1;

        $setting = $this->import('setting')->find($r);
        if ( empty($setting) ) return '0';

        return $setting['value'];
    }

    /*
     * 更新设置的值
     */
    private function updateSetting($key, $value)
    {
        if ( empty($key) ) return false;

        $r['eq']    = array('key'=>$key);
        $data       = array('value'=>$value);

        $setting    = $this->import('setting')->find($r);
        if ( empty($setting) ){
            $data = array(
                'key'   => $key,
                'value' => $value,
            );
            return $this->import('setting')->create($data);
        }
        return $this->import('setting')->modify($data, $r);
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