<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    首营企业选择画面
 * 作成者：苏迅
 * 作成日：2010/11/02
 * 更新履历：
 *********************************/
class gt_shouyingController extends gt_controllers_baseController {
		
	/*
	 * 首营企业选择弹出画面
	 */
	public function listAction()
	{
		$searchkey = $this->_getParam("searchkey",'');
		$this->_view->assign("searchkey",$searchkey);
		$this->_view->display ( "shouying_01.php" );
		
	}
	
	/*
	 * 单位选择弹出画面数据取得
	 */
	public function getlistdataAction()
	{
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",30);
		//$filter ['flg'] = $this->_getParam("flg","0");
		$filter ['searchkey'] = $this->_getParam("searchkey",'');
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
			
		$shouying_model = new gt_models_shouying();
		
		header("Content-type:text/xml");
		echo $shouying_model->getXmlData($filter);

	}
	
    /**
     * 取得单条单位信息
     *
     */
	public function getsingledataAction()
	{
    	$searchkey = $this->_getParam("searchkey","");
		$shouying_model = new gt_models_shouying();
		
	    echo json_encode($shouying_model->getSingleData($searchkey));
	
	}
}