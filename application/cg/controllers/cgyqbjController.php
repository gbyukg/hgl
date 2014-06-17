<?php
/**********************************************************
 * 模块：   采购模块(CG)
 * 机能：   采购逾期报警
 * 作成者：侯殊佳
 * 作成日：2011/06/10
 * 更新履历：
 * 	2011/08/31  LiuC  追加退货单明细显示
 **********************************************************/
class cg_cgyqbjController extends cg_controllers_baseController {
	/*
     * 采购逾期报警画面显示
     */
	public function indexAction() {
		$this->_view->assign ( 'title', '采购管理-采购逾期报警' );
		$this->_view->display ( 'cgyqbj_01.php' );
	}
	
	
	/*
	 * 采购逾期报警列表数据
	 */
	public function getlistdataAction(){
		//取得分页排序参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 ); //当前页起始
		$filter ['count'] = $this->_getParam ( "count",50); //默认显示数量
		$model = new cg_models_cgyqbj();
		header ( "Content-type:text/xml" ); //返回数据格式xml		
		echo $model->getGridData ( $filter );
	}
	
	
	/*
	 * 采购退货明细列表xml数据取得
	 */
	public function getthdmxlistdataAction() {
		//取得列表参数
		$filter ['posStart'] = $this->_getParam ( "posStart", 0 );  //当前页起始
		$filter ['count'] = $this->_getParam ( "count", 50 ); 		//默认显示数量
		$filter ['bh'] = $this->_getParam ( "bh", '' ); 	    //开始日期
		$filter ['orderby'] = $this->_getParam ( "orderby", 1 ); 	//排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		$model = new cg_models_cgyqbj();
		header ( "Content-type:text/xml" );           //返回数据格式xml
		echo $model->getGridMingxiData( $filter );
	}

	
	
}