<?
/**
* 商标相关信息
*
* 查询商标基础信息、图片、状态等
*
* @package	Module
* @author	Xuni
* @since	2015-10-22
*/
class TrademarkModule extends AppModule
{
    public $models = array(
        'img'		=> 'imgurl',
        'tm'		=> 'trademark',
        'second'	=> 'secondstatus',
        //'third'		=> 'statusnew',
    );

    protected $col = array(
        'auto as `tid`', 'id as `number`',
        'trademark as `name`','class','reg_date',
        'pid','valid_end',"group_concat(distinct `goods` separator ';') as `goods`",
        'group_concat(distinct `group`) as `group`',
        'group_concat(distinct `class`) as `strclass`'
    );

    /**
     * 群组字符串替换处理
     * 
     * @author  Jeany
     * @since   2015-11-5
     * @access  public
     * @return  void
     */
    public function groupReplace($str) 
    {
        $str = str_replace(",", ' ', $str); //替换全角空格为半角 
        $str = str_replace("　", ' ', $str); //替换全角空格为半角 
        $str = str_replace("    ", ' ', $str); //替换全角空格为半角 
        $str = str_replace("<br>", ' ', $str); //替换全角空格为半角 
        $str = str_replace("\n", ' ', $str); //替换全角空格为半角 
        $str = str_replace("\r", ' ', $str); //替换全角空格为半角 
        $str = str_replace("\t", ' ', $str); //替换全角空格为半角 
        $str = str_replace("\r\n", ' ', $str); //替换全角空格为半角 
        $str = str_replace('&lt;br&gt;', ' ', $str); //替换全角空格为半角 
        $str = str_replace('/\(.*?\)/', ' ', $str); //替换全角空格为半角 
        $str = preg_replace("/(?:\()(.*)(?:\))/i", '', $str);//替换带(1)(xx)(一)这种类型的数据
        $result = '';
        $strArr = explode(" ",$str);
        $strArr = array_filter(array_unique($strArr)); //去掉空字符串
        sort($strArr);
        $result = implode(',', $strArr);
        return $result; 
    }
    
    /**
     * 获取商标名称类型
     * 
     * @author  Jeany
     * @since   2015-11-5
     * @access  public
     * @return  void
     */
    public function getTmType($name)
    {
        $name   = str_replace(" ","",$name);
        $pregtx = "图形";//图形验证正则
        $pregZW = "/^[\x7f-\xff]+$/"; //中文验证正则
        $pregYW = "/^[a-zA-Z]+$/";//英文验证正则
        $pregSZ = "/^[0-9]+$/";//数字
        
        $pregZWBH = "/[\x7f-\xff]/"; //包含中文
        $pregYWBH = "/[a-zA-Z]+/";//包含英文验证正则
        $pregSZBH = "/[0-9]+/";//包含数字
        $value    = 0;
        if(preg_match($pregZW,$name)  && !strstr($name,$pregtx)){ $value = 1;}//中文
        if(preg_match($pregYW,$name)){$value = 2;}//英文
        if($pregtx == $name){$value = 3;}//图形
        if(preg_match($pregZWBH,$name) && preg_match($pregYWBH,$name) && !strstr($name,$pregtx) && !preg_match($pregSZBH,$name)){$value = 4;}//中+英
        if(preg_match($pregZWBH,$name)  && strstr($name,$pregtx) && strlen($name) != 6 && !preg_match($pregSZBH,$name)){$value = 5;}//中+图
        $str = str_replace("图形","",$name);
        if(preg_match($pregYWBH,$name)  && strstr($name,$pregtx) && !preg_match($pregZWBH,$str)){$value = 6;}//英+图
        if(preg_match($pregZWBH,$name) && preg_match($pregYWBH,$name) && strstr($name,$pregtx)){$value = 7;}//中+英+图
        if(preg_match($pregSZ,$name)){$value = 8;}//数字
        
        return $value;
    }

    /**
     * 获取商标名称字数
     * 
     * @author  Jeany
     * @since   2015-11-5
     * @access  public
     * @return  void
     */
    public function getTmLength($name)
    {
        $name   = str_replace(" ","",$name);
        $strlen = 0;
        $pregZWBH = "/[\x{4e00}-\x{9fa5}]+/u"; //包含中文
        $pregSZBH = "/[0-9]/"; //包含数字
        $pregYWBH = "/[a-zA-Z]/u";//包含英文验证 

        //包含中文的，都按照中文计算
        if( preg_match($pregZWBH,$name) ){
            preg_match_all($pregZWBH, $name, $match);
            $str    = implode("",$match[0]);
            $strlen = mb_strlen($str, 'utf8');
        }elseif ( preg_match($pregYWBH,$name) ){//包含英文
            preg_match_all($pregYWBH, $name, $match);
            $strlen = count($match[0]);
        }elseif ( preg_match($pregSZBH,$name) ){//数字
            preg_match_all($pregSZBH, $name, $match);
            $strlen = count($match[0]);
        }else{//其他
            $strlen = mb_strlen($name, 'utf8');
        }

        $strlen = $strlen > 6 ? 6 : $strlen;
        return $strlen;
    }

    /**
     * 获取商标可入驻平台
     * 
     * @author  Jeany
     * @since   2015-11-5
     * @access  public
     * @return  void
     */
    public function getTmPlatform($class)
    {
        $strArr = array();
        $platform = C("SALE_PLATFORM");
        if ( is_array($class) ){
            foreach ($class as $c) {
                $resArr = $this->getTmPlatform($c);
                $strArr = array_merge($strArr, $resArr);
            }
        }else{
            foreach($platform as $key => $val){
                $arrVal = explode(",",$val['value']);
                if(in_array($class,$arrVal)){
                    $strArr[] = $key;  
                }
            }
        }
        $strArr = array_unique($strArr);
        return $strArr;
    }

    /**
    * 通过商标号获取商标信息
    */
    public function getTmInfo($number)
    {
        $r['eq']	= array('id' => $number);
        $r['col']   = $this->col;

        $info		= $this->findTm($r);

        if(empty($info) || empty($info['tid'])) return array();

        $info['class']          = array_filter( explode(',', $info['strclass']) );
        $info['imgUrl']         = $this->getImg($number);
        $info['group']          = $this->groupReplace($info['group']);
        $info['status']         = $this->getFirst($number);
        $info['second']         = $this->getSecond($info['tid']);
        $proposer               = $this->load('proposer')->getNew($info['pid']);
        $info['proName']        = empty($proposer['cnName']) ? '' : $proposer['cnName'];

        return $info;
    }

    //获取商标其他信息，如：字数，中英文，平台等
    public function getTmOther($number)
    {   
        $r['eq']    = array('id' => $number);
        $r['col']   = array('trademark as `name`', 'group_concat(distinct class) as `strclass`');

        $info       = $this->findTm($r);
        if(empty($info) || empty($info['strclass'])) return array();

        $res    = array();
        $class  = array_filter( explode(',', $info['strclass']) );

        $res['type']        = $this->getTmType($info['name']);
        $res['length']      = $this->getTmLength($info['name']);;
        $res['platform']    = $this->getTmPlatform($class);

        return $res;
    }

    //判断商标号是否存在 
    public function existTm($number)
    {
        $r['eq']    = array('id'=>$number);
        $r['col']   = array('id');

        $res = $this->findTm($r);
        if ( empty($res) || empty($res['id']) ) return false;
        return true;
    }

    /**
    * 获取商标tid（主键）
    *
    * @author	Xuni
    * @since	2015-10-22
    *
    * @access	public
    * @return	void
    */
    public function getTid($number, $class)
    {
        $r['eq'] = array(
            'id' 	=> $number,
            'class'	=> $class,
        );
        $r['col'] = array('auto as `tid`');
        $info = $this->findTm($r);
        return empty($info) ? '' : $info['tid'];
    }

    /**
    * 获取商标的基础model方法调用
    *
    * @author	Xuni
    * @since	2015-10-22
    *
    * @access	public
    * @return	void
    */
    public function findTm($role)
    {
        return $this->import('tm')->find($role);
    }

    /**
    * 商标基础信息
    *
    * @author	Xuni
    * @since	2015-10-22
    *
    * @access	public
    * @return	void
    */
    public function getInfo($number, $field = '')
    {
        if ( intval($number) <= 0 ) return array();

        $r['eq'] 	= array('id'=>$number);
        if ( !empty($field) &&  is_array($field) ){
            $r['col'] = $field;
        }

        return $this->findTm($r);
    }

    /**
    * 获取商标图片地址
    *
    * @author	Xuni
    * @since	2015-10-22
    *
    * @access	public
    * @return	void
    */
    public function getImg($number)
    {
        $default = '/Static/images/img1.png';
        if ( intval($number) <= 0 ) return $default;

        $r['eq'] 	= array('trademark_id'=>$number);
        $img 		= $this->import('img')->find($r);

        return empty($img) ? $default : $img['url'];
    }

    public function getFirst($number, $type='c')
    {
        if ( intval($number) <= 0 ) return '';

        $r['eq']    = array('trademark_id'=>$number);
        $r['col']   = array('three_status');
        $second     = $this->import('second')->find($r);
        //返回数字
        if ( $type == 'n' ){
            return $second['three_status'];
        }
        //返回字符(中文)
        $Seconds    = C("SecondStatus");
        return $Seconds[$second['three_status']];
    }

    /**
    * 商标二级状态数据列表
    *
    * @author	Xuni
    * @since	2015-10-22
    *
    * @access	public
    * @return	void
    */
    public function getSecond($tid)
    {
        if ( intval($tid) <= 0 ) return array();

        $r['eq'] 	= array('tid'=>$tid);
        $second 	= $this->import('second')->find($r);

        if ( empty($second) ) return array();

        $list 		= array();
        $Seconds 	= C("SecondStatus");
        foreach (range(1, 28) as $v) {
            $key = 'status'.$v;
            if ($second[$key] == 1){
                $list[$v] = $Seconds[$v];
            }
        }
        return $list;
    }

    /**
    * 商标三级状态数据列表
    *
    * @author	Xuni
    * @since	2015-10-22
    *
    * @access	public
    * @return	void
    */
    public function getThird($tid)
    {
        $r['eq'] 	= array('tid'=>$tid);
        $r['order']	= array('date'=>'desc');
        $r['limit'] = 100;

        return $this->import('third')->find($r);
    }
    /**
    * 通过商标号获取商标信息(多条数据)
    *
    * @author  haydn
    * @since   2015-12-06
    *
    * @access  public
    * @param   int     $tmid    商标号
    * @return  array   $data   数据包
    */
    public function getTrademarkByIds($tmid)
    {
        $r['eq']    = array('id' => $tmid);
        $r['col']   = array('auto','id','class as class_id');
        $r['limit'] = 100;
        $data       = $this->import('tm')->findAll($r);
        return $data;
    }
    /**
    * 获取二级状态信息
    *
    * @author  haydn
    * @since   2015-12-06
    *
    * @access  public
    * @param   string  $tmid   商标号
    * @param   int     $class  国际分类
    * @return  array   $data   数据包
    */
    public function getTwoStageInfo($tmid,$class)
    {
        $r['eq']        = array('trademark_id' => $tmid , 'class_id' => $class);
        $r['col']       = array('tid','trademark_id','class_id','three_status');
        $r['limit']     = 100;
        $data           = $this->import('second')->find($r);
        return !empty($data) ? $data[0] : array();
    }
    /**
    * 获取三级状态信息
    *
    * @author  haydn
    * @since   2015-12-06
    *
    * @access  public
    * @param   string  $tmid   商标号
    * @param   int     $class  国际分类
    * @return  array   $data   数据包
    */
    public function getThreeStageInfo($tmid,$class)
    {
        $array          = array();
        $r['eq']        = array('trademark_id' => $tmid , 'class' => $class);
        $r['col']       = array('tid','trademark_id','class','status','date');
        $r['limit']     = 100;
        $data           = $this->import('third')->findAll($r);
        if( !empty($data['total']) ){
            foreach( $data['rows'] as $key => $val ){
                $array[] = $val;
            }
        }
        return $array;
    }
    /**
    * 获取申请人下的相同名称的商标数量
    *
    * @author  haydn
    * @since   2015-12-06
    *
    * @access  public
    * @param   string  $tmid   商标号
    * @param   int     $class  国际分类
    * @return  int     $total  商标数量
    */
    public function getAlikeBrand($tmid,$class)
    {
        $total      = 0;
        $r1['eq']   = array('id' => $tmid , 'class' => $class);
        $r1['col']  = array('auto','proposer_id','trademark');
        $tmArr      = $this->import('tm')->find($r1);
        if( !empty($tmArr) ){
            $r['eq']    = array('proposer_id' => $tmArr['proposer_id'],'trademark' => $tmArr['trademark']);
            $r['notIn'] = array('auto'=>array($tmArr['auto']));
            $r['col']   = array('COUNT( * ) AS total');
            $data       = $this->findTm($r);
            $total      = $data['total'];
        }
        return $total;
    }

    public function getAllInfo($number, $isInfo=0, $isProposer=0, $isSecond=0, $isImage=0)
    {
        if ( empty($number) ) return array();
        $data = array();
        if ( $isInfo ){
            $r['eq']        = array('id'=>$number);
            $r['limit']     = 100;
            $data['info']   = $this->import('tm')->find($r);
        }

        if ( $isInfo && $isProposer ){
            $data['proposer']       = $this->load('proposer')->get($data['info']['proposer_id']);
            $data['proposerNew']    = $this->load('proposer')->getNew($data['info']['pid']);
        }

        if ( $isSecond ){
            $r['eq']        = array('trademark_id'=>$number);
            $r['limit']     = 100;
            $data['second'] = $this->import('second')->find($r);
        }

        if ( $isImage ){
            $r['eq']        = array('trademark_id'=>$number);
            $r['limit']     = 1;
            $data['image']  = $this->import('img')->find($r);
        }

        return $data;
    }
}
?>