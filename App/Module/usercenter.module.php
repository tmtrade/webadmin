<?
/**
* 用户中心功能
*
* 处理与用户中心相关的功能
* 推送价格变动数据、其他
*
* @package	Module
* @author	Xuni
* @since	2016-03-02
*/
class UserCenterModule extends AppModule
{
    public $models = array(
        'myuser'    => 'user',
    );

    /**
     * 推送价格变动数据到用户中心
     * 
     * @author  Xuni
     * @since   2016-03-02
     *
     * @access  public
     * @param   string  $number     商标号
     * @param   array   $oldInfo    价格修改前的数据
     * @param   array   $newInfo    价格修改后的数据
     * 
     * @return  bool
     */
    public function pushTmPrice($number, array $oldInfo, array $newInfo)
    {
        if ( empty($number) ) return false;
        return $this->importBi('usercenter')->pushTmPrice($number, $oldInfo, $newInfo);
    }

    //判断用户是否存在，返回uid
    public function existUser($mobile)
    {
        if ( empty($mobile) || isCheck($mobile) != 2 ) return 0;

        $r['eq']    = array('mobile'=>$mobile);
        $r['col']   = array('id');
        $r['limit'] = 1;

        $info   = $this->import('myuser')->find($r);
        if ( empty($info) || $info['id'] <= 0 ) return 0;
        return $info['id'];
    }

    public function getUser($uid)
    {
        if ( !is_numeric($uid) || $uid <= 0 ) return array();

        $r['eq']    = array('id'=>$uid);
        $r['limit'] = 1;

        return $this->import('myuser')->find($r);
    }
}
?>