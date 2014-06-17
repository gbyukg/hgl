<?php
require_once('Smarty/Smarty.class.php');

class Common_View_Smarty
{
    /**
     * @var Smarty
     */
	protected $_smarty = false;

	public function __construct(){
		$this->_smarty = new Smarty();
	}

	protected function _run($template){	    
		$this->_smarty->display($template);	
	}

	/**
	 * assign值,请参考templates本身的语言
	 * 使用方法$this->assign("userName","Vincent");
	 *        $list = array('vincent','eglin');
	 *        $this->assign($list);
	 * @param array $var
	 */
	
    public function assign($tpl_var, $value = null)
    {
        return  $this->_smarty->assign($tpl_var, $value);
    }


    /**
     * 返回smarty合并后的模板字符串
     *
     * @param string $template
     * @return string
     */
	public function render($template){
		return $this->_smarty->fetch($template);
	}

	/**
	 * 显示模板
	 * 如果该模板不存在,则在默认路径下显示
	 * @param string $template
	 */
	public function display($template){
		$this->_smarty->display($template);
	}

    function fetchPage($template)
    {
        ob_start();
        $this->display($template);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    
    
     public function setTemplateDir($value)
    {
        $this->_smarty->template_dir = $value;
    }
    
     public function setCompileDir($value)
    {
        $this->_smarty->compile_dir = $value;
    }
    
    
}
