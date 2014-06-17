<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   商品价格调整(spjgtz)
 * 作成者：孙宏志
 * 作成日：2011/1/4
 * 更新履历：
 *********************************/
class jc_spjgtzController extends jc_controllers_baseController {
	

	/*
	 * 画面初期显示
	 */
	public function indexAction()
	{
		//$this->_view->assign ( "title", "基础管理-一品多价维护" ); 
		$this->_view->display ( "spjgtz_01.php" );
	}	


	/*
     * 获取商品信息
     */
	public function getspxxAction() {
		$model = new jc_models_spjgtz();
		$data = $model->getSpxx($this->_getParam ('shpbh'));
		echo Common_Tool::json_encode($data);
	}
	
	/*
	 * 保存商品价格信息
	 */
	public function saveAction() {
		$result['status'] = '0'; 

		try {
			$model = new jc_models_spjgtz();
			//必须输入项验证
			if(!$model->inputCheck())
			{
				$result['status'] = '1';  //必须输入项验证错误
			}
			else
			{
				//开始一个事务
			    $model->beginTransaction ();

			    //调价信息保存
			    $model->saveTiaojia();

			    //保存成功
			    if($result['status'] == '0'){

				    $model->commit ();
				    for($i=0; $i<count($_POST ["#grid_mingxi"]); $i++)
				    {
				    	Common_Logger::logToDb("商品价格调整  商品编号：".$_POST ["#grid_mingxi"][$i][1]);
				    }
				    
			    }else
			    {
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