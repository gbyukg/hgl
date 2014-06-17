<?php
/*********************************
 * 模块：   仓储模块(CC)
 * 机能：   打包台登陆信息(dbtdl)
 * 作成者：ZhangZeliang
 * 作成日：2011/05/17
 * 更新履历：
 *********************************/
class cc_dbtdlController extends cc_controllers_baseController 
{
	/*
	 * 首页
	 */
	public function indexAction()
	{
		
		$this->_view->assign('title', '仓储管理-打包台登陆');
		$model = new cc_models_dbtdl();
		$array_ckxx = $model->get_ckxx(); //获取仓库信息
        //如仓库信息中只有一条数据，则将该条数据默认显示在打开页面中
		if(count($array_ckxx) == 1)
		{
			$this->_view->assign('con', 'one');
			$this->_view->assign('rec',$array_ckxx[0]);
		}else
		{
			$this->_view->assign('con', 'more');
		}
		$this->_view->display('dbtdl_01.php');
	}
	
	/*
	 * 获取打包台信息
	 */
	public function getdbtxxAction()
	{
		$model = new cc_models_dbtdl();
		$ckbh = $this->_getParam('ckbh');
		echo json_encode($model->get_dbtxx($ckbh));
	}
	
	/*
	 * 下一步
	 */
	public function nextAction()
	{
		$filter['ckbh'] = $this->_getParam('ckbh');
		$filter['dbt'] = $this->_getParam('dbt');
		try
		{
			$model = new cc_models_dbtdl();
			$model->beginTransaction();
			$status = $model->get_status($filter);
			if($status == '1')
			{
				//更改打包台登陆信息
				$model->updateDbtdlxx($filter);
			}
			//保存打包台登陆信息
            $model->saveDbt($filter);
            //更改打包台信息
            $model->updateDbtxx($filter);
			$model->commit();
		}catch (Exception $e)
		{
			$model->rollBack();
			throw $e;
		}
	}
}
?>