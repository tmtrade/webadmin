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
        //$page = $this->input('p','int','1');

        //$this->load('run')->run($page);
        exit('function closed');
    }

    public function one()
    {
        //$number = $this->input('n', 'string', '');
        //$debug  = $this->input('d', 'int', 0);
        //if ( empty($number) ) exit('no data.');
        //$this->load('run')->runSale($number, $debug);
        exit('function closed');
    }

    public function update()
    {
        //$page = $this->input('p','int','1');

        //$this->load('run')->update($page);
        $this->load('run')->runRegDate();
    }

    public function updateName()
    {
        //$page = $this->input('p','int','1');

        //$this->load('run')->update($page);
        $this->load('run')->runName();
    }


    public function importPt()
    {
        //$this->load('run')->importPt();
    }


    public function importPtId()
    {
        $number = $this->input('id', 'string', '');
        $ids = array_filter(array_unique(explode(',', $number)));
        if ( empty($ids) ) exit('no ids.');
        $this->load('run')->importPt($ids);
    }

    // public function importOp()
    // {
    //     $this->load('run')->importOp();
    // }

    public function deleteContact()
    {
        $this->load('run')->deleteNoPhoneContact();
    }

}
?>