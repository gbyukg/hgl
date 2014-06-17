<?php
/*********************************
 * 模块：    配送模块(PS)
 * 机能：   分配线路(FPXL)
 * 作成者：刘枞
 * 作成日：2011/08/17
 * 更新履历：
 *********************************/
class ps_fpxlController extends ps_controllers_baseController {
	
	/*
	 * 初始页面
	 */
	public function indexAction() {

		$this->_view->display ( "fpxl_01.php" );
	}
	
	
	/*
	 * 列表xml数据取得
	 */
	public function getthdlistdataAction() {
		//取得列表参数
		$filter ['rqkey'] = $this->_getParam ( "rq", '' ); 	             //开始日期
		
		$model = new ps_models_fpxl();
		header ( "Content-type:text/xml" );                 //返回数据格式xml
		echo $model->getGridDanjuData( $filter );
	}
	

	/*
     * 获取发货区信息
     */
	public function getfhqAction() {
		$model = new ps_models_fpxl();
		$result = $model->getFHQ();
		echo Common_Tool::json_encode($result);
	}
	
	
	/*
	 * 保存操作
	 */
	public function saveAction() {
		$result['status'] = '0'; 
		try {
			$Model = new ps_models_fpxl();

			//必须输入项验证
			if(!$Model->inputCheck()){
				$result['status'] = '1';             //必须输入项验证错误
			}else{
			    $Model->beginTransaction ();			   //开始一个事务
			    
			    //验证数据库中是否已存在同一条数据，并做相应处理
				$Model->logicCheck();
			    
			    if($result['status'] == '0'){
				    $Model->commit();       //成功，提交事务
			    }else{
				    $Model->rollBack();     //有错误发生,事务回滚
			    }
			}
			echo json_encode($result);

		} catch ( Exception $e ){
			$Model->rollBack();		       //存在异常回滚
     		throw $e;
		}
	}
	
	
}