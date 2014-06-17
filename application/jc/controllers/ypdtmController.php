<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   一品多条码(ypdtm)
 * 作成者：孙宏志
 * 作成日：2010/12/13
 * 更新履历：
 *********************************/
class jc_ypdtmController extends jc_controllers_baseController {
	

	/*
	 * 一品多条码初始页面
	 */
	public function indexAction() {
		//$this->_view->assign ( "title", "基础管理-一品多条码维护" ); //标题
		$this->_view->display ( "ypdtm_01.php" );
	}	

	/*
     * 检查商品编号存在
     */
	public function checkspbhAction() {
		$model = new jc_models_ypdtm();
		if($model->checkSpbh($this->_getParam ('spbh'))==FALSE){
			echo 0; //不存在
		} else {
			echo 1; //存在
		};
	}
	
	/*
     * 获取商品信息
     */
	public function getspxxAction() {
		$model = new jc_models_ypdtm();
		$result = $model->getSpxx($this->_getParam ('spbh'));
		echo Common_Tool::json_encode($result);
	}

	/*
     * 获取表格信息
     */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['searchkey'] = $this->_getParam ( "searchkey", '' ); //检索条件
		$model = new jc_models_ypdtm ( );
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}
	
	/*
	 * 保存多条码信息
	 */
	public function saveAction() {
		$result['status'] = '0'; 
		//商品编号取得
		$shpbh = $_POST['SHPBH'];
		try {
			$model = new jc_models_ypdtm();
			$rtnlogic = $model->logicCheck($shpbh);
			//必须输入项验证
			if(!$model->inputCheck($shpbh))
			{
				$result['status'] = '1';  //必须输入项验证错误
			}
			elseif(($rtnlogic['rtncode'] <> 0))
			{
				$result['status'] = '2';  //项目合法性验证错误
				$result['data'] = $rtnlogic['shptm']; //商品条码
			}
			else
			{
				//开始一个事务
			    $model->beginTransaction ();

			    //该商品对应的所有多条码信息删除
			    $model->delTiaoma($shpbh);
			    //多条码信息保存
			    $model->saveTiaoma($shpbh);

			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $shpbh;
				    $model->commit ();
				    Common_Logger::logToDb("一品多条码信息删除：".$result['data']);
				    Common_Logger::logToDb("一品多条码信息新规：".$result['data']);
			    }else{
				    $model->rollBack ();//有错误发生
			    }

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