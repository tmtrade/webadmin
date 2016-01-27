<?
/**
 * 申请人
 *
 * 获取出售信息，添加出售信息
 * 
 * @package	Module
 * @author	JEANY
 * @since	2015-09-15
 */
class ProposerModule extends AppModule
{
    /**
     * 引用业务模型
     */
    public $models = array(
        'proposer'  => 'proposer',
        'new'       => 'proposerNew',
        );

	/**
	 * 获取申请人信息
	 *
     * @author  Xuni
	 * @access	public
	 * @param	array 	$id			申请人ID
	 *
	 * @return	array
	 */
    public function get($id)
    {
		$r['eq']['id'] = $id;
		$data = $this->import('proposer')->find($r);
    	return $data ? $data : array();
    }

    /**
     * 申请人基础方法
     *
     * @author  Xuni
     * @access  public
     * @param   array   $r         条件
     *
     * @return  array
     */
    public function findInfo($r)
    {
        $data = $this->import('proposer')->find($r);
        return $data ? $data : array();
    }

    /**
     * 获取新申请人信息
     *
     * @author  Xuni
     * @access  public
     * @param   array   $id         申请人newID
     *
     * @return  array
     */
    public function getNew($id)
    {
        $data = $this->import('new')->get($id);
        return $data ? $data : array();
    }

    //获取新申请人中文名称
    public function getNewName($id)
    {
        $data = $this->import('new')->get($id);
        return empty($data['cnName']) ? '' : $data['cnName'];
    }

    /**
     * 新申请人基础方法
     *
     * @author  Xuni
     * @access  public
     * @param   array   $r         条件
     *
     * @return  array
     */
    public function findNew($r)
    {
        $data = $this->import('new')->find($r);
        return $data ? $data : array();
    }

	
}
?>