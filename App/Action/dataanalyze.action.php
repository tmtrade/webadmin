<?
header("Content-type: text/html; charset=utf-8");

/**
 * 广告管理
 *
 * @package    Action
 * @author     Far
 * @since      2016年6月7日10:29:46
 */
class DataAnalyzeAction extends AppAction
{
         
    /**
     * 出售数据分析
     */
    public function index(){
        $type       = $this->input('type','int','1');
        $dateStart 	= $this->input('dateStart', 'string');
        $dateEnd 	= $this->input('dateEnd', 'string');
        
        $dateStart  = empty($dateStart) ? "" : strtotime($dateStart);
        $dateEnd    = empty($dateEnd) ? "" : strtotime($dateEnd)+86399;//结束时间为一天的最后
        
        $list = $this->load('dataanalyze')->dataAnalyze($dateStart,$dateEnd);
        
        $view = "";
        $i =0;
        switch ($type){
            case 1: //价格区间
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
                    $i +=1;
                }
                foreach ($price as $k=>$v){
                    $count = round(($v/$i)*100,2);
                    $data[] = array($k." ".$v,$count);
                }
                break;
            
            case 2://包装比例
                $tinfo = array();
                foreach ($list as $v){
                    if($v['tminfo']==1){
                        $tinfo['包装'] +=1; 
                    }else{
                        $tinfo['未包装'] +=1; 
                    }
                    $i +=1;
                }
                foreach ($tinfo as $k=>$v){
                    $count = round(($v/$i)*100,2);
                    $data[] = array($k." ".$v,$count);
                }
                break;
                
            case 3://渠道来源
                $source = array();
                $saleSource     = C("SOURCE");//来源渠道
                foreach ($list as $v){
                    if(!empty($saleSource[$v['source']])){
                        $source[$saleSource[$v['source']]] +=1; 
                    }
                    $i +=1;
                }
                foreach ($source as $k=>$v){
                    $count = $v;
                    $data[] = $count;
                    $name[] = $k;
                }
                $view = "dataanalyze/dataanalyze.index1.html";
                break;
                
            case 4://分类分布情况
                $class = array();
                $tmClass = range(1, 45);
                foreach ($tmClass as $val){
                    foreach ($list as $v){
                        $c = explode(",", $v['class']);
                        if(in_array($val, $c)){
                            $class["第".$val."类"] +=1;
                        }else{
                            $class["第".$val."类"] +=0;
                        }
                    }
                }
                foreach ($class as $k=>$v){
                    $count = $v;
                    $data[] = $count;
                    $name[] = $k;
                }
                $view = "dataanalyze/dataanalyze.index1.html";
                break;
            case 5://分类分布情况
                echo "暂不开放";exit;
                break;
        
        }
        $this->set("data",array("name"=>json_encode($name),"date"=>json_encode($data)));
        $this->set("s",array("dateStart"=>date("Y-m-d",$dateStart),"dateEnd"=>date("Y-m-d",$dateEnd),'type'=>$type));
        $this->display($view);
    }
       
}

?>