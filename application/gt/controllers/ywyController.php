<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    业务员选择
 * 作成者：周义
 * 作成日：2011/02/21
 * 更新履历：
 *********************************/
class gt_ywyController extends gt_controllers_baseController {
	/*
	 * 业务员选择弹出画面
	 * flg: 0：采购员  1 销售员  2：仓库管理员
	 */
	public function listAction(){
		$flg = $this->_getParam('flg','0');       //类别
		$dwbh = $this->_getParam('dwbh','000000');//单位编号
		
		if($flg=='0'){
			$title = '采购业务员选择';
		}else if($flg=='1'){
			$title = '销售业务员选择';
		}else if($flg=='2'){
			$title = '仓库管理员选择';
		}

		$this->_view->assign("title",$title);
		$this->_view->assign("flg",$flg);
		$this->_view->assign("dwbh",$dwbh);
		$this->_view->display ("ywy_01.php" );
	}
	
	/*
	 * 业务员列表画面数据取得
	 */
	public function getlistdataAction(){
		//查询相关参数		
		$filter ['flg'] = $this->_getParam("flg","0");
		$filter ['dwbh'] = $this->_getParam("dwbh","000000");	
	
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_ywy_searchParams'] = $_POST;
				unset($_SESSION['gt_ywy_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_ywy_filterParams'] = $_POST;
				unset($_SESSION['gt_ywy_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['gt_ywy_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_ywy_searchParams'];  //固定查询条件
			
		$model = new gt_models_ywy();
		header("Content-type:text/xml");
		echo $model->getListData($filter);
	}

     /*
	 * 自动完成
	 */
	public function autocompleteAction(){
		$filter ['searchkey'] = $this->_getParam('q');
    	$filter ['flg'] = $this->_getParam("flg",'0');
    	$filter ['dwbh'] = $this->_getParam("dwbh","000000");	
        $model = new gt_models_ywy ( );
	    $result = $model->getData($filter);
	    echo json_encode($result);
	}
}