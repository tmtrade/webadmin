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
}
?>