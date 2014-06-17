<?php 
/******************************************************************
 ***** 模         块：       仓储模块(CC)
 ***** 机         能：       近有效期报警信息(jyxqbjxx)
 ***** 作  成  者：       handong
 ***** 作  成  日：        2011/05/25
 ***** 更新履历：

 ******************************************************************/

class cc_jyxqbjxxController extends cc_controllers_baseController {
     /*
      * 退货区列表画面显示
      */
	public function indexAction(){
		$this->_view->assign('title','仓储管理-近有效期报警');
		$this->_view->display('jyxqbj_01.php');
	}
	
    /*
	 * 得到报警信息
	 */
	public function getlistdataAction(){
	//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
        $filter ['orderby'] = $this->_getParam ( "orderby","1"); //排序列
		$filter ['direction'] = $this->_getParam ( "direction","ASC"); //排序方式		
		//保持排序条件
		$_SESSION["sortParams"]["orderby"] = $filter ['orderby'];
		$_SESSION["sortParams"]["direction"] = $filter ['direction'];
		$model = new cc_models_jyxqbjxx();
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	}
}
?>