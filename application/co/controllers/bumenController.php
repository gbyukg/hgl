<?php
/*********************************
 * 模块：    共通模块(CO)
 * 机能：    部门选择画面
 * 作成者：周义
 * 作成日：2010/08/05
 * 更新履历：
 *********************************/
class co_bumenController extends co_controllers_baseController {
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	
	}
	
	/**
	 * 部门选择弹出画面
	 *
	 */
	public function  listAction() {
		$this->_view->display ( "co_bumen_01.php" );
	}
	
	/**
	 * 部门树形列表xml
	 *
	 */
	public function getlistdataAction()
	{
		$bumen_model = new co_models_bumen ( );
		header("Content-type:text/xml");
		echo $bumen_model->getTreeData('000000');
		
	}
	


}