<?

/**
 * 数据跟踪
 *
 * 访问者数据，模块跟踪，模块走势图
 *
 * @package    Module
 * @author     Far
 * @since      2016年5月17日11:00:07
 */
class VisitlogModule extends AppModule
{
	public $models = array(
		'visitlog'              => 'visitlog',
		'sessions'              => 'visitlogSessions',
                'sem'                   => 'visitlogSem',
	);
	
}
?>