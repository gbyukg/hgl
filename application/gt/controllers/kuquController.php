<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    库区选择
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_kuquController extends gt_controllers_baseController {
	/*
	 * 库区选择弹出画面
	 * flg:  0 ->可用及冻结 1->全部
	 * ckbh:仓库编号
	 * ckmch:仓库名称
	 */
	public function listAction(){
		$this->_view->assign("title","库区选择");
		$this->_view->display ( "kuqu_01.php" );
	}
	
	/*
	 * 库区选择弹出画面数据取得
	 * flg:  0 ->可用及冻结 1->全部
	 */
	public function getlistdataAction()	{
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
		
		//查询相关参数
		$filter ['flg'] = $this->_getParam("flg","0");
		$filter ['ckbh'] = $this->_getParam("ckbh","");

		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_kuqu_searchParams'] = $_POST;
				unset($_SESSION['gt_kuqu_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_kuqu_filterParams'] = $_POST;
				unset($_SESSION['gt_kuqu_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['gt_kuqu_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_kuqu_searchParams'];  //固定查询条件
			
		$model = new gt_models_kuqu();
		header("Content-type:text/xml");
		echo $model->getListData($filter);
	}
	
    /**
     * 取得单条仓库信息
     *flg:  0 ->可用及冻结 1->全部
     */
	public function getsingledataAction(){
    	$filter ['flg'] = $this->_getParam("flg");
    	$filter ['searchkey'] = $this->_getParam("searchkey","");
		$cangku_model = new gt_models_cangku();

	    echo json_encode($cangku_model->getSingleData($filter));
	}
	
    /*
	 * 自动完成
	 */
	public function autocompleteAction(){
		$searchkey = $this->_getParam('q');
        $kuqu_model = new gt_models_kuqu();
	    $result = $kuqu_model->getAutocompleteData($searchkey);
	    echo json_encode($result);
	}
}