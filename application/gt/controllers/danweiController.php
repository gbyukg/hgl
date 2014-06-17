<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    单位选择
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_danweiController extends gt_controllers_baseController {
	/*
	 * 单位选择弹出画面
	 * flg:  0 ->销售 1->采购 2->全部
	 * status:0->可用 1->全部
	 */
	public function listAction(){
	    $flg = $this->_getParam('flg','0');
	    
		if($flg=='0'){
			$title = '销售客户选择';
		}else if($flg=='1'){
			$title = '采购客户选择';
		}else if($flg=='2'){
			$title = '客户单位选择';
		}

		$this->_view->assign("title",$title);
		$this->_view->assign("flg",$flg);
		$this->_view->assign("status",$this->_getParam('status','0'));
		$this->_view->assign("searchkey",$this->_getParam("searchkey",''));
		$this->_view->display ( "danwei_01.php" );
	}
	
	/*
	 * 单位选择弹出画面数据取得
	 * flg:  0 ->销售 1->采购
	 * status:0->可用 1->全部
	 */
	public function getlistdataAction(){
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');

		//查询相关参数
		$filter ['flg'] = $this->_getParam("flg");
		$filter ['status'] = $this->_getParam("status","0");
	
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_danwei_searchParams'] = $_POST;
				unset($_SESSION['gt_danwei_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_danwei_filterParams'] = $_POST;
				unset($_SESSION['gt_danwei_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['gt_danwei_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_danwei_searchParams'];  //固定查询条件
			
		$danwei_model = new gt_models_danwei();
		header("Content-type:text/xml");
		echo $danwei_model->getListData($filter);
	}

    /*
	 * 自动完成
	 * flg:  0 ->销售 1->采购
	 */
	public function autocompleteAction(){
    	$filter ['searchkey'] = $this->_getParam('q'); //查找字符串
    	$filter ['flg'] = $this->_getParam("flg");  
        $danwei_model = new gt_models_danwei ( );
	    $result = $danwei_model->getAutocompleteData($filter);
	    echo Common_Tool::json_encode($result);
	}
	
	public function getdanweiinfoAction(){
		$filter ['flg'] = $this->_getParam("flg");  
		$filter ['dwbh'] = $this->_getParam("dwbh");
        $danwei_model = new gt_models_danwei ( );
	    $result = $danwei_model->getDanweiInfo($filter);
	    echo Common_Tool::json_encode($result);		
	}
}