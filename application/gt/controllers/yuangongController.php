<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    员工选择
 * 作成者：周义
 * 作成日：2010/12/15
 * 更新履历：
 *********************************/
class gt_yuangongController extends gt_controllers_baseController {
	/*
	 * 员工选择弹出画面
	 * flg: 0：采购员  1 销售员2：仓库管理员 9:全体
	 */
	public function listAction(){
		$flg = $this->_getParam('flg');
		
		if($flg=='0'){
			$title = '采购业务员选择';
		}else if($flg=='1'){
			$title = '销售业务员选择';
		}else if($flg=='2'){
			$title = '仓库管理员选择';
		}else{
			$title = '员工选择';
		}

		$this->_view->assign("title",$title);
		$this->_view->assign("flg",$flg);
		$this->_view->assign("searchkey",$this->_getParam("searchkey",''));
		$this->_view->display ( "yuangong_01.php" );
	}
	
	/*
	 * 员工列表画面数据取得
	 */
	public function getlistdataAction(){
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['orderby'] = $this->_getParam("orderby",1);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
		
		//查询相关参数		
		$filter ['flg'] = $this->_getParam("flg","0");                 //用途
	
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_yuangong_searchParams'] = $_POST;
				unset($_SESSION['gt_yuangong_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_yuangong_filterParams'] = $_POST;
				unset($_SESSION['gt_yuangong_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['gt_yuangong_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_yuangong_searchParams'];  //固定查询条件
			
		$model = new gt_models_yuangong();
		header("Content-type:text/xml");
		echo $model->getListData($filter);
	}

     /*
	 * 自动完成
	 */
	public function autocompleteAction(){
		$filter ['searchkey'] = $this->_getParam('q');
    	$filter ['flg'] = $this->_getParam("flg");
        $yuangong_model = new gt_models_yuangong ( );
	    $result = $yuangong_model->getAutocompleteData($filter);
	    echo json_encode($result);
	}
}