<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   客户特价(khtj)
 * 作成者：李洪波
 * 作成日：2011/01/05
 * 更新履历：
 *********************************/
class jc_khtjController extends jc_controllers_baseController {
	/*
	 * 客户特价初始页面
	 */
	public function indexAction()
	{
		$this->_view->assign ( "ZHXRQ", date("Y-m-d"));  //执行日期
		$this->_view->assign ( "ZHZHRQ", date("Y-m-d"));  //终止日期
		$this->_view->assign ( "title", "基础管理-客户特价维护" ); 
		$this->_view->display ( "khtj_01.php" );
	}
     /*
     * 获取单位名称
     */
	public function getdwmchAction() {
 		$model = new jc_models_khtj();
		$result = $model->getDwmch($this->_getParam ('dwbh'));
		echo Common_Tool::json_encode($result);
	}
     /*
     * 检查特价信息验证
     */
	public function checktjxxAction() {
		$model = new jc_models_khtj();
		//取得列表参数
		$filter ['dwbh'] = $this->_getParam ( "dwbh", '' ); //检索条件_单位编号
		$filter ['shpbh'] = $this->_getParam ( "shpbh", '' ); //检索条件_商品编号
		$filter ['zhxrq'] = $this->_getParam ( "zhxrq", '' ); //检索条件_执行日期
		$filter ['zhzhrq'] = $this->_getParam ( "zhzhrq", '' ); //检索条件_终止日期
		if($model->checkTjxx($filter)==FALSE){
			echo 0; //不存在
		} else {
			echo 1; //存在
		};
	}
     /**
     * 通过商品编号取得商品相关信息
     */
	public function getshangpininfoAction()
	{
    	$filter ['shpbh'] = $this->_getParam('shpbh');   //检索项目值
		$model = new jc_models_khtj ( );
	    echo json_encode($model->getShangpinInfo($filter));
	}
     /*
     * 获取表格信息
     */
	public function getlistdataAction() {
		$filter ['orderby'] = $this->_getParam ( "orderby",1 ); //排序列
		$filter ['direction'] = $this->_getParam ( "direction", 'ASC' ); //排序方式
		//一般查询
		if($this->_getParam ( "isfilter", '0' )=='0'){
		   //取得一般查询条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['jc_khtj_searchParams'] = $_POST;
				unset($_SESSION['jc_khtj_filterParams']); //清空精确查询条件
			}
		}else{//精确查询
			//取得过滤条件参数并保存至session
			if($this->_request->isPost()){
				$_SESSION['jc_khtj_filterParams'] = $_POST;
				//unset($_SESSION['gt_shangpin_searchParams']); //清空一般查询条件
			}
		}
		//取得检索条件
		$filter['filterParams'] = $_SESSION['jc_khtj_filterParams'];  //精确查询条件
		$filter['searchParams'] = $_SESSION['jc_khtj_searchParams'];  //固定查询条件
		$model = new jc_models_khtj();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	/*
	 * 保存客户特价信息
	 */
	public function saveAction() {
		$result['status'] = '0'; 
		//单位编号取得
		$dwbh = $_POST['DWBH'];
		try {
			$model = new jc_models_khtj();
			//开始一个事务
			$model->beginTransaction ();
			//删除客户特价信息
			$model->delTejia($dwbh);
			//客户特价信息保存
			$model->saveTejia($dwbh);
			//保存成功
			if($result['status'] == '0'){
			    Common_Logger::logToDb("【客户特价维护 单位编号：".$dwbh."】");
			    $model->commit ();
			 }else{
			    $model->rollBack ();//有错误发生
			}
			echo json_encode($result);
		}
		 catch ( Exception $e )
		 {
			//回滚
			$model->rollBack ();
			throw $e;
		}
	}
}
