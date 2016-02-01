<?
/**
 * 后台执行入口
 *
 * 执行所有后台功能队列，总是在进程中执行着。
 * 服务器环境需要配置cronjob 对其进行实时执行。
 *
 * @package     Action
 * @author      Xuni
 * @since       2015-01-13
 */
class TaskAction extends QueueCommonAction
{
    
    public function index()
    {
        $page = $this->input('p','int','1');

        $this->load('run')->run($page);

    }

    public function one()
    {
        $number = $this->input('n', 'string', '');
        $debug  = $this->input('d', 'int', 0);
        if ( empty($number) ) exit('no data.');
        $this->load('run')->runSale($number, $debug);
    }

}
?>