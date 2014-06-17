<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   商品保质期维护(spbzqwh)
 * 作成者：梁兆新
 * 作成日：2011/1/5
 * 更新履历：
 *********************************/
class jc_spbzqwhController extends jc_controllers_baseController {
	
	/*
	 * 商品保质期维护首页
	 */
	public function indexAction()
	{
		$this->_view->display ( "spbzqwh_01.php" );
	}	


	/*
     * 获取商品信息
     */
	public function getspxxAction() {
		$model = new jc_models_spbzqwh();
		$data = $model->getSpxx($this->_getParam ('shpbh'));
		echo Common_Tool::json_encode($data);
	}
	
	/*
	 * 保存商保质期信息
	 */
	public function saveAction() {
		$result['status'] = '0'; 
		try {
			$model = new jc_models_spbzqwh();
			$rtnlogic = $model->logicCheck();
			//必须输入项验证
			if(!$model->inputCheck())
			{
				$result['status'] = '1';  //必须输入项验证错误
			}
			elseif(($rtnlogic <> '1'))
			{
				$result['status'] = '2';  //商品编号不存在
			}
			else
			{
				//开始一个事务
			    $model->beginTransaction ();

			    //商品保质期信息保存
			    $model->savebz();

			    //保存成功
			    if($result['status'] == '0'){
				    $model->commit ();
				    for($i=0; $i<count($_POST ["#grid_bzqwh"]); $i++)
				    {
				    	Common_Logger::logToDb('商品保质期维护  商品编号:'.$_POST ["#grid_bzqwh"][$i][1]);
				    }	
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