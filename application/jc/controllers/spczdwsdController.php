<?php
/*********************************
 * 模块：   基础模块(JC)
 * 机能：   商品拆装单位(spczdwsd)
 * 作成者：魏峰
 * 作成日：2011/01/05
 * 更新履历：
 *********************************/
class jc_spczdwsdController extends jc_controllers_baseController {
	/*
     * 商品拆装单位维护画面显示
     */
	public function indexAction() {
		$this->_view->assign ( "title", "基础管理-商品拆装单位设定" ); 	//标题	
		$this->_view->display ( 'spczdwsd_01.php' );
	}
	
	/*
     * 获取商品信息
     */
	public function getspxxAction() {
		$model = new jc_models_spczdwsd();
		$result = $model->getSpxx($this->_getParam ('spbh'));
		echo Common_Tool::json_encode($result);
	}
	
	/*
     * 获取商品拆装单位设定信息
     */
	public function getlistdataAction() {
		//取得列表参数
		$filter ['searchkey'] = $this->_getParam ( "searchkey", '' ); //检索条件
		$model = new jc_models_spczdwsd();
		header ( "Content-type:text/xml" ); //返回数据格式xml
		echo $model->getGridData ( $filter );
	}	
	
	/*
     * 获取商品信息
     */
	public function getdwmchAction() {
		$model = new jc_models_spczdwsd();
		$result = $model->getDwmch();
		echo Common_Tool::json_encode($result);
	}	
	
	/* 保存商品拆装单位信息
	 */
	public function saveAction() {
		$result['status'] = '0'; 
		//商品编号取得
		$shpbh = $_POST['SHPBH'];
		
		try {
			$model = new jc_models_spczdwsd();
			$rtnlogic = $model->logicCheck();
			//必须输入项验证
			if(!$model->inputCheck($shpbh))
			{
				$result['status'] = '1';  //必须输入项验证错误
			}
			elseif(($rtnlogic['rtncode'] <> 0))
			{
				if ($rtnlogic['rtncode'] == 1){
					$result['status'] = '2';  //项目合法性验证错误
					$result['data'] = $rtnlogic['dwbh']; //单位编号
				}elseif ($rtnlogic['rtncode'] == 2){
				   $result['status'] = '3';  //项目合法性验证错误
				   $result['data'] = $rtnlogic['xjdw']; //下级单位
				}
			}
			else
			{
				//开始一个事务
			    $model->beginTransaction ();

			    //该商品对应的所有单位拆装信息删除
			    $model->delChaizhuang($shpbh);
			    //单位拆装信息保存
			    $model->saveChaizhuang($shpbh);

			    //保存成功
			    if($result['status'] == '0'){
			    	$result['data'] = $shpbh;
				    $model->commit ();
				    Common_Logger::logToDb("商品单位拆装信息删除：".$result['data']);
				    Common_Logger::logToDb("商品单位拆装信息新规：".$result['data']);
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