<?
/**
 * 定时任务处理方法类
 *
 * 将需要定时处理的方法放到本类中，方便在定时任务类中添加处理设置
 *
 * @package     ConsoleAction
 * @author      Xuni
 * @since       2015-04-15
 */
class CronjobAction extends QueueCommonAction
{

	/**
	 * 定时任务（测试）
	 * @author	xuni
	 * @since	2015-06-26
	 *
	 * @access	public
	 * @return	boolean
	 */
    public function test()
    {
        return true;
    	//Log::write('hello world', date('Y-m-d').'-cronjob-test.log');
    }
    
    
    /**
    * 执行删除过期的广告
    * @author	Far
    * @since	2015-06-30
    * 每月10号凌晨执行
    * @access	public
    * @return	boolean
    */
    public function delAd(){
	   return $this->load('ad')->delPastAd();
    }

    /**
    * 更新注册日期
    * @author   Xuni
    * @since    2016-07-27
    * 每周五午夜23点23分执行
    * @access   public
    * @return   boolean
    */
    public function updateRegDate(){
       return $this->load('run')->runRegDate(0);
    }


}
?>