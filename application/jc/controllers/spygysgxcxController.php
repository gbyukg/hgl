<?php
/*********************************
 * 模块：   基础模块(jc)
 * 机能：    商品与供应商关系查询(SPYGYSGXCX)
 * 作成者：姚磊
 * 作成日：2011/1/6
 * 更新履历：
 *********************************/
class jc_spygysgxcxController extends jc_controllers_baseController {

	
	
	/*
	 * 商品与供应商关系维护初始页面
	 */
	public function indexAction() { 	
		
		$this->_view->assign ( "title", "基础管理-商品与供应商关系查询" ); //标题
		$this->_view->display ( "spygysgxcx_01.php" );
	}
	

 	/*
	 * 多商品明细
	 */
 	public function  getdwbhgridAction(){
 				//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式		
 		$model = new jc_models_spygysgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getdanweiGridData($filter);
 	}
 	/*
 	 * 单商品明细
 	 */
 	public function getdanshpgridAction(){
 				//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式	
 		$model = new jc_models_spygysgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getdanshpGridData($filter);
 	}
 	/*
 	 * 无商品明细
 	 */
 	public function getwushpgridAction(){
 				//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
 		$model = new jc_models_spygysgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getwushpGridData($filter);
 	}
 	
 	/*
 	 * 商品明细
 	 */
 	
 	public function getshpmxgridAction(){
 				//取得分页排序参数
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式	
 		$dwbh = $this->_getParam ("flg");
 		$model = new jc_models_spygysgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getshpmxGrid($filter,$dwbh);
 		
 	}
 	
	/*
 	 * 供应商明细
 	 */
 	
 	public function getdwbhmxgridAction(){
		 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式
 		$shpbh = $this->_getParam ("flg");
 		$model = new jc_models_spygysgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getdwbhmxGrid($filter,$shpbh);
 		
 	}
 	
 	
 	/*
 	 * 多供应商
 	 */
 	public function getduoshpgridAction(){
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式	
 		$model = new jc_models_spygysgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getduoshpGrid($filter);
 	}
 	
	/*
 	 * 单供应商
 	 */
 	public function getdanshpgygridAction(){
 		
 		$model = new jc_models_spygysgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getdanshpGrid();
 	}
	/*
 	 * 无供应商
 	 */
 	public function getwushpgygridAction(){
 		
 		$model = new jc_models_spygysgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getwushpGrid();
 	}
 	
}