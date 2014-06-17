<?php
/*********************************
 * 模块：    共通模块(GT)
 * 机能：    销售单选择
 * 作成者：周义
 * 作成日：2010/11/15
 * 更新履历：
 *********************************/
class gt_xshdController extends gt_controllers_baseController {
	/*
	 * 销售单选择弹出画面
	 * flg  0：未出库  1：已出库 (已出库，退票，已签收)
	 */
	public function listAction(){
		$flg = $this->_getParam('flg');
		
		if($flg=='0'){
			$title = '销售单选择(未出库)';
		}else if($flg=='1'){
			$title = '销售单选择(已出库)';
		}
 
		$this->_view->assign("endDate",Zend_Date::now()->toString('yyyy-MM-dd'));//截止日期 当前日
		$this->_view->assign("beginDate",Zend_Date::now()->addDay(-14)->toString('yyyy-MM-dd'));//开始日期（2周前）
     	$this->_view->assign("title",$title);
		$this->_view->assign("flg",$flg);
		$this->_view->display ( "xshd_01.php" );
	}
	
	/*
	 * 销售单列表画面数据取得
	 */
	public function getlistdataAction(){
		//翻页排序相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['orderby'] = $this->_getParam("orderby",2);
		$filter ['direction'] = $this->_getParam("direction",'ASC');
		
		//查询相关参数
		$filter ['flg'] = $this->_getParam("flg","0");  //0 未出库 1 已出库
		
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_xshd_searchParams'] = $_POST;
				unset($_SESSION['gt_xshd_filterParams']); //清空精确查询条件
			}
			
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['gt_xshd_filterParams'] = $_POST;
				unset($_SESSION['gt_xshd_searchParams']); //清空一般查询条件
			}
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['gt_xshd_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['gt_xshd_searchParams'];  //固定查询条件

		//业务查询处理
		$xshd_model = new gt_models_xshd();
		header("Content-type:text/xml");
    	echo $xshd_model->getListData($filter);
	}
	
	/*
	 * 销售单明细列表数据取得
	 */
	public function getmxlistdataAction(){
		//查询相关参数
		$filter ['xshdbh'] = $this->_getParam("xshdbh");  //销售单编号
		//业务查询处理
		$xshd_model = new gt_models_xshd();
    	header("Content-type:text/xml");
		echo $xshd_model->getMxListData($filter);
	}
}