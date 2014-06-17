<?php
/*********************************
 * 模块：    销售模块(XS)
 * 机能：    销售历史价格查询(xslsjgcx)
 * 作成者：周义
 * 作成日：2011/01/20
 * 更新履历：
 *********************************/
class xs_xslsjgcxController extends xs_controllers_baseController {
	
	/*
	 * 初始页面
	 */
	public function indexAction() {
		//默认三个月内数据
		$this->_view->assign("zhzhrq",Zend_Date::now()->toString('yyyy-MM-dd'));//终止日期 当前日
		$this->_view->assign("kshrq",Zend_Date::now()->addMonth(-3)->toString('yyyy-MM-dd'));//开始日期（3月前）
		
		$this->_view->assign("title","销售-历史销售价格查询"); //标题
		$this->_view->assign("dwbh",$this->_getParam("dwbh")); //单位编号
		$this->_view->assign("dwmch",$this->_getParam("dwmch")); //单位名称
		$this->_view->assign("shpbh",$this->_getParam("shpbh")); //商品编号
		$this->_view->assign("shpmch",$this->_getParam("shpmch")); //商品名称
		$this->_view->display ( "xslsjgcx_01.php" );
		
	}
	
	/*
	 * 列表数据取得
	 * 
	 */
	public function getlistdataAction(){
		//翻页相关参数
		$filter ['posStart'] = $this->_getParam("posStart",0);
        $filter ['count'] = $this->_getParam("count",50);
		$filter ['orderby'] = $this->_getParam("orderby",2);
		$filter ['direction'] = $this->_getParam("direction",'DESC');

		//取得一般查询条件参数并保存至session
		if($this->_request->isPost()){
			$_SESSION['xs_xslsjgcx_searchParams'] = $_POST;
		}

		//取得检索条件
		$filter['filterParams'] = $_SESSION['xs_xslsjgcx_searchParams'];  //查询条件
			
		$model = new xs_models_xslsjgcx();
		header("Content-type:text/xml");
		echo $model->getListData($filter);
	}

}