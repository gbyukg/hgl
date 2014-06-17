<?php
/*********************************
 * 模块：   基础模块(jc)
 * 机能：    商品与客户关系查询(SPYKHGXCX)
 * 作成者：姚磊
 * 作成日：2011/1/7
 * 更新履历：
 *********************************/
class jc_spykhgxcxController extends jc_controllers_baseController {

	
	
	/*
	 * 商品与客户关系维护初始页面
	 */
	public function indexAction() { 	
		
		$this->_view->assign ( "title", "基础管理-商品与客户关系查询" ); //标题
		$this->_view->display ( "spykhgxcx_01.php" );
	}
	

 	/*
	 * 多商品明细
	 */
 	public function  getdwbhgridAction(){
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式	
 		$model = new jc_models_spykhgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getdanweiGridData($filter);
 	}
 	/*
 	 * 单商品明细
 	 */
 	public function getdanshpgridAction(){
 		
 		$model = new jc_models_spykhgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getdanshpGridData();
 	}
 	/*
 	 * 无商品明细
 	 */
 	public function getwushpgridAction(){
 		
 		$model = new jc_models_spykhgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getwushpGridData();
 	}
 	
 	/*
 	 * 商品明细
 	 */
 	
 	public function getshpmxgridAction(){
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式
 		$dwbh = $this->_getParam ("flg");
 		$model = new jc_models_spykhgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getshpmxGrid($filter,$dwbh);
 		
 	}
 	
	/*
 	 * 客户明细
 	 */
 	
 	public function getdwbhmxgridAction(){
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式
 		$shpbh = $this->_getParam ("flg");
 		$model = new jc_models_spykhgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getdwbhmxGrid($filter,$shpbh);
 		
 	}
 	
 	
 	/*
 	 * 多客户
 	 */
 	public function getduoshpgridAction(){
 		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$filter ['orderby'] = $this->_getParam ( "orderby","2"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式	
 		$model = new jc_models_spykhgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getduoshpGrid($filter);
 	}
 	
	/*
 	 * 单客户
 	 */
 	public function getdanshpgygridAction(){
 		
 		$model = new jc_models_spykhgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getdanshpGrid();
 	}
	/*
 	 * 无客户
 	 */
 	public function getwushpgygridAction(){
 		
 		$model = new jc_models_spykhgxcx();
 		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getwushpGrid();
 	}
 	
}