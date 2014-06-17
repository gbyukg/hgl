<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   商品编码修改(spbmxg)
 * 作成者：孙宏志
 * 作成日：2010/12/23
 * 更新履历：
 *********************************/
class jc_spbmxgController extends jc_controllers_baseController {
	

	/*
	 * 商品编号修改
	 */
	public function indexAction()
	{
		//$this->_view->assign ( "title", "基础管理-一品多价维护" ); 
		$this->_view->display ( "spbmxg_01.php" );
	}	


	/*
     * 获取商品信息
     */
	public function getspxxAction() {
		$model = new jc_models_spbmxg();
		$data = $model->getSpxx($this->_getParam ('shpbh'));
		echo Common_Tool::json_encode($data);
	}
	
	/*
	 * 保存商品条码信息
	 */
	public function saveAction() {
		$result['status'] = '0'; 

		try {
			$model = new jc_models_spbmxg();
			$rtnlogic = $model->logicCheck();
			//必须输入项验证
			if(!$model->inputCheck())
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

			    //条码信息保存
			    $model->saveTiaoma();

			    //保存成功
			    if($result['status'] == '0'){

				    $model->commit ();
				    for($i=0; $i<count($_POST ["#grid_mingxi"]); $i++)
				    {
				    	Common_Logger::logToDb("商品编码修改  商品编号：".$_POST ["#grid_mingxi"][$i][1]);
				    }
/*					foreach ( $_POST ["#grid_mingxi"][1] as $shangpin ) 
				    {
				 		Common_Logger::logToDb("商品编码修改  商品编号：".$shangpin);
				    }*/

				    
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